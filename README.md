# OpenAustralia Foundation - WordPress block theme

A WordPress Full-Site-Editing (block) theme for [oaf.org.au](https://www.oaf.org.au/),
the OpenAustralia Foundation. It replaces the legacy `oaf-thematic` classic theme with the
maroon institutional brand from the 2026 redesign.

- **Requires:** WordPress 6.6+, PHP 8.3+
- **Type:** Block theme (FSE) - editable in Appearance → Editor
- **Self-updating:** via [Git Updater](https://git-updater.com/) from this GitHub repo (see below)
- **Fonts:** Fira Sans (body) + Merriweather (serif, used for blockquotes) are **self-hosted**
  from `assets/fonts/` and registered in `theme.json` via `fontFace` (loads on the front end and
  in the editor). Headings use a system Helvetica stack (no webfont). No font request leaves the
  visitor's browser to a third party, so no visitor IP is sent to Google. The bundled woff2 files
  are SIL Open Font License 1.1; see `assets/fonts/OFL.txt`.

## What's in the theme

```text
style.css            Theme header (incl. Git Updater headers) + all .oaf-* component styles
theme.json           Palette, fonts (self-hosted via fontFace), layout, element/heading styles (schema v3)
functions.php        Style enqueue, theme supports, text domain, pattern category, button + block registration
inc/                 options.php  - editable settings (defaults + accessor + sanitise)
                     admin.php    - the Appearance → OAF Theme admin screen (Settings API)
                     pages.php    - idempotent required-page creator + example-people seeding
                     people.php   - People post type, Groups taxonomy, Role field, seed data
blocks/              raisely-donation-form - dynamic block rendering the Raisely embed
                     people-grid           - dynamic block listing People by group
templates/           front-page, home, index, single, page, archive, search, 404
parts/               header, footer (thin - each references a pattern)
patterns/            header, footer, donate-band, home-collection,
                     page-about, page-collection, page-people, page-contact, page-donate
assets/fonts/        Self-hosted Fira Sans + Merriweather woff2 (SIL OFL 1.1, see OFL.txt)
assets/img/          OAF wordmarks, ACNC tick, service logos
languages/           Translation files (.pot/.po/.mo) - text domain oaf-wp-blocktheme
LICENSE              GNU General Public License v2
```

The header masthead and the canonical footer (attribution, charity status + ABN,
Acknowledgement of Country, ACNC Registered Charity tick, sister-service links, socials) live
in `patterns/header.php` and `patterns/footer.php` so they can resolve bundled theme image
URLs. The thin `parts/*.html` reference those patterns. The footer's editable values come from
the theme settings (see below).

## Installing

You can install the theme either from GitHub (recommended, so it can self-update) or by hand.

**With [Git Updater](https://git-updater.com/) (recommended):**

1. Install and activate the Git Updater plugin.
2. Git Updater → Install Theme → enter `openaustralia/oaf-wp-blocktheme`, branch `main`.
3. Activate **OpenAustralia Foundation** under Appearance → Themes. Updates pushed to `main`
   then appear under Dashboard → Updates like any other theme.

**By hand:**

1. Download/clone this repo into `wp-content/themes/oaf-wp-blocktheme` (the repo *is* the theme).
2. Activate **OpenAustralia Foundation** under Appearance → Themes.

After activating, run the one-click setup below, then set a static front page: Settings →
Reading → "A static page" → choose your **Home** page and set the Posts page to **Blog**
(the "Create required pages" button can do this for you).

## Updates (Git Updater)

The theme's `style.css` carries the headers Git Updater needs:

```text
GitHub Theme URI: openaustralia/oaf-wp-blocktheme
Primary Branch: main
```

With the repo public, no token is required. To cut a release, bump `Version:` in `style.css`
and push/tag on `main`; Git Updater offers the update on connected sites. (For a private repo
you would instead add a GitHub Personal Access Token in Git Updater's settings.)

## Theme settings & one-click setup

The theme adds an **Appearance → OAF Theme** admin screen with two parts:

- **Create required pages** - one button creates the standard pages (`about`, `collection`,
  `people`, `contact`, `donate`) plus `home` and `blog`, each seeded with its matching pattern,
  and seeds the example **Team** and **Board** people. It is **idempotent**: pages and people
  that already exist are skipped, so it is safe to run more than once. A checkbox also sets
  `home` as the static front page and `blog` as the posts page. (On a live-site replacement
  where these pages already exist, the button reports them as skipped and changes nothing.)
- **Settings** - editable global "components" that render through the footer everywhere:
  organisation name/ABN/ACNC + ABR links, social profile links, sister-service URLs, the
  Acknowledgement of Country text, and the **Raisely donation embed** (see below). Each field
  falls back to the original design default when left blank. Values are stored in one option
  (`oaf_theme_options`) and read by the patterns via `oaf_option()`.

The five marketing pages ship as **block patterns**, so once created the copy lives in the
normal page editor. The home page, blog index, single posts, archives, search and 404 are
driven by templates.

## People (staff & board)

People are managed under **Admin → People** (a custom post type), not in page markup:

- **Add / edit / remove** a person like a post. Fields: **Name** (title), **Bio** (editor),
  **Role** (sidebar box, e.g. "Acting Executive Officer"), **Photo** (Featured image, optional),
  and **Order** (Page Attributes, for manual sorting).
- **Groups** is a taxonomy seeded with **Team** and **Board of directors**; assign each person
  to a group, rename them, or add more groups later.
- The People page renders each group with the **People Grid** block (`oaf/people-grid`), which
  has a group selector in the sidebar and a live preview. Each card shows the photo when set,
  otherwise the original coloured **initials circle** (initials from the name; colour cycled
  from the brand palette), so the design is unchanged.

Running "Create required pages" seeds the original nine people (5 Team, 4 Board) so the page is
populated and ready to edit. Seeding only runs when no people exist yet.

## Donations (Raisely)

The Donate page uses the **Raisely Donation Form** block (`oaf/raisely-donation-form`), which
renders the embed snippet from **Appearance → OAF Theme → Raisely donation form**. To go live,
paste the embed code from your Raisely campaign dashboard into that field - it appears wherever
the block is placed (the Donate page by default). The header and donate-band "Donate" buttons
link to the internal `/donate/` page. The embed is stored raw and is editable only by users who
can manage options. **Keep the "donations are not tax deductible" wording** below the form; it
is mandatory.

The Contact page still includes a **styled, non-functional** form placeholder; replace it with a
contact-form plugin block/shortcode to make it send mail.

## Editing

Everything is editable in the Site Editor (Appearance → Editor): templates, the header/footer
template parts, colours and fonts (Styles), and the patterns. Brand colours and the type scale
are defined in `theme.json`; bespoke component styling is in `style.css`. Global footer values,
the Raisely embed, and People are managed from the admin screens described above rather than by
editing markup.

## Local development

Local testing uses [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/)
(`@wordpress/env`, provided via `mise`):

```sh
wp-env start                                   # boots WordPress with this repo as the theme
wp-env run cli wp <command>                    # run WP-CLI against the dev site
sh .wp-setup.sh                                # optional: seed demo posts/pages from the CLI
```

`.wp-env.json` and `.wp-setup.sh` are development conveniences and have no effect on a normal
install. (On this network `wp-env`/Node may need
`NODE_OPTIONS=--network-family-autoselection-attempt-timeout=5000` to avoid connection timeouts.)

## License

[GNU General Public License v2 or later](LICENSE).
