# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A WordPress Full-Site-Editing (block) theme for oaf.org.au. **The repo *is* the theme** — it lives at `wp-content/themes/oaf-wp-blocktheme` on a site. There is **no build step** (no webpack/npm bundling) and **no test suite**: source files ship as-is. PHP 8.3+, WordPress 6.6+.

## Commands

```sh
composer install              # install dev tooling (PHPCS + WPCS) — required before linting
composer run phpcs            # lint all PHP (the only automated check; CI runs this on PRs + push to main)
composer run phpcbf           # auto-fix PHPCS violations where possible

wp-env start                  # boot WordPress with this repo as the active theme (wp-env comes via mise)
wp-env run cli wp <command>   # run WP-CLI against the dev site
sh .wp-setup.sh               # seed the dev site: permalinks, the standard pages (from patterns), reading settings

mise run bump <level>         # cut a release: bump style.css, roll CHANGELOG, tag, push (level = major|minor|patch|X.Y.Z)
```

CI runs two workflows on PRs. `.github/workflows/php.yml` runs `composer validate --strict`, a `php -l` syntax sweep, and `composer run phpcs`. `.github/workflows/changelog.yml` fails a PR whose diff does not touch `CHANGELOG.md` (skippable with the `no-changelog` label). On some networks `wp-env`/Node needs `NODE_OPTIONS=--network-family-autoselection-attempt-timeout=5000` to avoid connection timeouts.

## Deployment: Git Updater

Connected sites self-update via the Git Updater plugin tracking `main`, which offers an update **only when `Version:` in `style.css` increases**. So merging a PR to `main` does not ship it: changes accumulate under `## [Unreleased]` in `CHANGELOG.md`, and a release is a deliberate version bump. Not every merge bumps the version. The full contributor and release workflow lives in `CONTRIBUTING.md`.

To cut a release run `mise run bump <major|minor|patch|X.Y.Z>` (`bin/bump-version.sh`): it bumps `Version:` in `style.css`, rolls `[Unreleased]` into a dated version section with updated compare-links, commits, tags `vX.Y.Z`, and (after a prompt) pushes `main`. The `GitHub Theme URI` / `Primary Branch` headers in `style.css` drive Git Updater.

## Architecture

**Styling is split deliberately.** `theme.json` (schema v3) owns the palette, the self-hosted fonts (Fira Sans + Merriweather registered via `fontFace`, files in `assets/fonts/`), layout, and element/heading styles. `style.css` holds *all* bespoke `.oaf-*` component styling, hand-ported from a design handoff — it is the single stylesheet, enqueued on the front end and also loaded into the editor via `add_editor_style()` so both match. `.oaf-ct` is the container primitive; full-bleed "bands" are `alignfull` groups wrapping an inner `.oaf-ct`.

**Header and footer are patterns, not template parts.** `patterns/header.php` and `patterns/footer.php` are PHP so they can resolve bundled theme image URLs (`get_template_directory_uri()`) and read theme settings. The `parts/*.html` files are thin wrappers that just `<!-- wp:pattern -->` those patterns. Editing the header/footer means editing the pattern PHP.

**Marketing pages ship as patterns; everything else is templates.** The five pages (about, collection, people, contact, donate) exist as `patterns/page-*.php`. Once created they are normal editable pages whose content is a single `wp:pattern` reference — so their real markup is in the pattern file, not the page. Home, blog, single, archive, search and 404 are driven by `templates/*.html`.

**`functions.php` wires up `inc/` modules.** Loaded on the front end: `options.php`, `people.php`, `avatars.php`, `stats.php`, `contact-form.php`. Loaded only in admin: `pages.php`, `admin.php`.

- **Settings** (`inc/options.php` + `inc/admin.php`): one option `oaf_theme_options`, read everywhere via `oaf_option()`, each field falling back to a design default. Drives the footer's editable values (name/ABN, socials, sister-service links, Acknowledgement of Country) and the Raisely embed. Admin screen: **Appearance → OAF Theme**.
- **Required-page creation** (`inc/pages.php`): the "Create required pages" button is **idempotent** — existing pages/people are skipped. Pattern pages get the `page-no-title` template so `page.html`'s title hero doesn't double up with the pattern's own hero.
- **People** (`inc/people.php`): a `People` custom post type + `Groups` taxonomy (seeded Team / Board) + a Role meta box + Order. Rendered by the `oaf/people-grid` dynamic block, not page markup.
- **Avatars** (`inc/avatars.php`): overrides Gravatar with People photos via `pre_get_avatar_data`, falling back to a coloured initials circle. Set `$args['url']` + `found_avatar` there to fully short-circuit Gravatar.
- **Stats** (`inc/stats.php`): pulls live numbers from the `wp-alaveteli-stats` plugin when present. `oaf_stat()` returns already-escaped markup — it is whitelisted as a custom escaping function in `phpcs.xml.dist`, so do not re-escape at call sites.
- **Contact form** (`inc/contact-form.php`): the contact page pattern is a single Jetpack multi-step form; this module routes each submission to the right inbox by enquiry type via `contact_form_to` / `contact_form_subject`. Keep the field-select options in `patterns/page-contact.php` in sync with `oaf_contact_routes()`.

**Two dynamic blocks** (`blocks/*/render.php`, registered from `block.json` in `functions.php`): `raisely-donation-form` renders the embed snippet stored in settings; `people-grid` lists People by group. Their `editor.js` + `editor.asset.php` are plain (no JS build).

## PHPCS conventions

`phpcs.xml.dist` uses WordPress-Extra + WordPress-Docs + PHPCompatibilityWP. Intentional deviations already encoded there (don't "fix" them):
- The global prefix is `oaf` (under the sniff's 4-char minimum, but explicitly allowed). All globals must still carry it.
- `oaf_stat()` is registered as an escaping function (see Stats above).
- `*.asset.php` block filenames are exempt from the hyphenation sniff.

Escape late (at output), and match the surrounding file's WPCS-clean style — CI will reject anything that isn't.
