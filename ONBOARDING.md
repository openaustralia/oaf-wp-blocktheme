# Onboarding — OAF WordPress block theme

> A first-day path for a new engineer or contractor. It sequences setup, a
> "does it work" check, and one real end-to-end change. It does **not** restate
> what's already documented — for the full picture read
> [README.md](README.md) (product, install, features) and
> [CLAUDE.md](CLAUDE.md) (architecture, module map, PHPCS conventions). This
> page links to them rather than duplicating them.

## The one thing to understand first

**The repo *is* the theme.** It's checked out at
`wp-content/themes/oaf-wp-blocktheme` on a site. There is **no build step**
(no webpack/npm bundling, no compiled JS) and **no test suite** — PHP and JS
ship as-is. The only automated gate is PHP linting (PHPCS). Requires PHP 8.3+
and WordPress 6.6+.

That mental model rules out most of what you'd expect in a web app: there's no
application database of its own, no migrations, no background jobs, no API to
run. If you catch yourself looking for a `dev` server build or a test command,
stop — there isn't one.

## Day one setup

Prerequisites: PHP 8.3+, Composer, and [mise](https://mise.jdx.dev/) (which
provides `wp-env`). Then, from the repo root:

```sh
composer install    # dev tooling (PHPCS + WordPress Coding Standards) — needed before linting
mise install        # provides wp-env + wp-studio
wp-env start         # boots WordPress with this repo as the active theme (Docker required)
sh .wp-setup.sh      # seed the dev site: permalinks, standard pages (from patterns), reading settings
```

`wp-env` needs Docker running. On some networks it (and Node) time out on
startup — if so, prefix with
`NODE_OPTIONS=--network-family-autoselection-attempt-timeout=5000`.

### Verify it works

Work through these — they're steps to follow, not a passing report:

- [ ] `composer run phpcs` completes with no errors (this is exactly what CI runs).
- [ ] `wp-env start` prints a local URL; opening it shows the OAF site with the
      maroon brand, header masthead and footer.
- [ ] After `sh .wp-setup.sh`, the About / Collection / People / Contact /
      Donate pages resolve and render their patterns.
- [ ] **Appearance → Editor** opens the Site Editor and the front end matches
      the editor (same fonts/colours).

## Your first change (end to end)

A gentle change that exercises the whole loop — edit code → lint → preview →
ship. We'll change a footer default value.

Most footer text is *editable in the admin* (**Appearance → OAF Theme →
Settings**), stored in the single `oaf_theme_options` option and read via
`oaf_option()`, each field falling back to a code default. The defaults live in
[inc/options.php](inc/options.php) — e.g. the ABN at
[inc/options.php:25](inc/options.php#L25) and the Acknowledgement of Country at
[inc/options.php:36](inc/options.php#L36).

1. **Make the edit.** Change a default string in
   [inc/options.php](inc/options.php) (keep it a cosmetic tweak you'll revert).
2. **Lint it.** Run `composer run phpcs`. If it complains about style, try
   `composer run phpcbf` to auto-fix, then re-run.
3. **Preview it.** On a site whose setting is blank, the default now shows in
   the footer. Reload the front end (or the Site Editor) to see it.
4. **Ship it (the part people forget).** Connected sites self-update via the
   **Git Updater** plugin tracking `main`, which offers an update **only when
   `Version:` in [style.css](style.css#L10) increases**. Merging to `main` does
   not ship on its own: changes accumulate under `## [Unreleased]` in
   [CHANGELOG.md](CHANGELOG.md), and you cut a release with
   `mise run bump <major|minor|patch>`. See [CONTRIBUTING.md](CONTRIBUTING.md)
   for the full changelog and release workflow.
5. **Revert** your cosmetic change (and the version bump) once you've seen the
   loop work.

Note: because footer values are *settings*, a real content fix is usually done
in the admin screen, not in code. Editing the default is the exception — do it
when you want to change what a blank field falls back to.

## Where things live

Skim [README.md → "What's in the theme"](README.md) for the file-tree
overview, then [CLAUDE.md → Architecture](CLAUDE.md) for how it fits together.
The short version:

| Path | What's there |
|------|--------------|
| `style.css` | Theme header (+ Git Updater headers, **the version**) and **all** bespoke `.oaf-*` component CSS |
| `theme.json` | Palette, self-hosted fonts, layout, element/heading styles (schema v3) |
| `functions.php` | Wires up the `inc/` modules, theme supports, block + pattern registration |
| `inc/` | Settings, People CPT, avatars, stats, contact-form routing, required-page creation |
| `templates/` | Home, blog, single, page, archive, search, 404 (`.html`) |
| `patterns/` | Header, footer, and the five marketing pages (`.php`, so they can resolve theme asset URLs) |
| `parts/` | Thin `.html` wrappers that just reference the header/footer patterns |
| `blocks/` | Two dynamic blocks: `people-grid`, `raisely-donation-form` (no JS build) |

Two things that surprise people (both explained in [CLAUDE.md](CLAUDE.md)):
**the header and footer are patterns, not template parts**, and **the five
marketing pages are patterns** whose page content is a single `wp:pattern`
reference — so their real markup is in `patterns/page-*.php`, not the page.

## Contributing

- **Branch off `main`**; open a PR (the repo has a PR template); disclose AI
  assistance in human-readable form. Every PR adds a `## [Unreleased]` entry to
  [CHANGELOG.md](CHANGELOG.md) (a CI check enforces it; the `no-changelog` label
  skips it for internal-only changes). See [CONTRIBUTING.md](CONTRIBUTING.md).
- **CI must be green.** `.github/workflows/php.yml` runs `composer validate
  --strict`, a `php -l` syntax sweep, and `composer run phpcs` on PRs and pushes
  to `main`. Run `composer run phpcs` locally before pushing.
- **Match the surrounding WPCS-clean style.** Escape late (at output). The
  intentional PHPCS deviations (the `oaf` short prefix, `oaf_stat()` as an
  escaping function, `*.asset.php` filenames) are encoded in
  [phpcs.xml.dist](phpcs.xml.dist) — don't "fix" them.
- **Australian English**; hyphens or commas, never em dashes. Content is
  strictly non-partisan and must not imply tax-deductibility (OAF is an ACNC
  charity, not a DGR).

## Audience notes

- **New to WordPress FSE / block themes?** Start in the Site Editor
  (**Appearance → Editor**) to see templates, parts and Styles visually, then
  map what you see back to `templates/`, `parts/` and `theme.json`.
- **Experienced, want the model fast?** Read [CLAUDE.md](CLAUDE.md)
  top-to-bottom — it's the maintained architecture reference.
- **Contractor with a scoped task?** Most content is data (settings, People
  posts, the Raisely embed), not code. Confirm whether your change belongs in
  an admin screen, a pattern, `theme.json`, or `style.css` before editing — and
  remember the `style.css` version bump is what actually ships it.
