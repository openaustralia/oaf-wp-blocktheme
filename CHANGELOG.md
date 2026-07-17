# Changelog

All notable changes to the OpenAustralia Foundation block theme are documented
in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project aims to follow [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Changes land under [Unreleased] as they are merged. Merging to `main` does not
ship them: connected sites only update when the `Version` header in `style.css`
increases, which happens as a deliberate release (see
[CONTRIBUTING.md](CONTRIBUTING.md)).

## [Unreleased]

### Changed

- The footer now renders the ACNC Registered Charity tick as an inline SVG
  instead of a raster image, so the logo stays crisp at any size.
- The navigation now collapses to the hamburger across the whole small-tablet
  range (600-781px), matching where the rest of the masthead reflows.
  Previously WordPress core's fixed 600px breakpoint left the full horizontal
  nav showing and wrapping to a second row between 600px and 781px.

### Fixed

- Mobile navigation overlay: the open hamburger menu no longer clips its links
  against the right edge. The masthead's `margin-left:auto` (which right-aligns
  the desktop nav) was leaking onto the nav container inside the open overlay,
  shrinking it and pushing "Collection" off-screen. The margin is now reset
  within `.is-menu-open` and the panel padded so links sit flush-left.

## [1.3.0] - 2026-07-06

### Changed

- Copy edits to the homepage and People page (Adam's feedback).

## [1.2.0] - 2026-07-01

### Changed

- Reworked the Contact page form from a per-enquiry accordion into a single
  Jetpack multi-step form (progress indicator, Back/Next navigation): step 1
  asks what the enquiry is about, step 2 collects details, step 3 the message
  and an optional file attachment.
- Routing now happens in PHP (`inc/contact-form.php`) via the `contact_form_to`
  and `contact_form_subject` filters, keyed on the step-1 choice: general
  (`contact@oaf.org.au`), media (`media@oaf.org.au`, `[Media Contact]`) and
  government or law enforcement (`exec@oaf.org.au`, `[OAF Contact: Gov/LEO]`),
  with each service routed to its own `contact@` address.
- Warmer, mySociety-inspired copy, including a "check the service's help page
  first" nudge on step 1.

### Added

- Reactive step-1 behaviour (`assets/js/contact-form.js`): choosing a specific
  service shows a "contact that team directly" link, and every enquiry except
  media and government/law enforcement shows a "we are not the government"
  notice.

### Fixed

- The file-upload field now renders its visible, clickable drop zone. The
  previous markup omitted the inner dropzone block, so the upload control
  collapsed to zero height and could not be used.

## [1.1.0] - 2026-07-01

### Changed

- Rebuilt the Contact page pattern around a working contact form. The old
  placeholder `<form>` (a Custom HTML block that never sent mail and was stripped
  by WordPress.com's content sanitiser) is replaced with Jetpack Form blocks,
  which survive sanitising and deliver submissions.
- The form is organised as an accordion by enquiry type, each routed to its own
  inbox with a subject prefix: general (`contact@oaf.org.au`, `[OAF Contact]`),
  media (`media@oaf.org.au`, `[Media Contact]`), and government or law
  enforcement (`exec@oaf.org.au`, `[OAF Contact: Gov/LEO]`, with organisation,
  phone and file-attachment fields and a "gov/LEO only" notice).
- Questions about a specific service now point people to that service's help
  page and support address (Right to Know, They Vote for You, PlanningAlerts,
  OpenAustralia.org.au) rather than a form.
- Removed the "We're not on Facebook, Instagram or X." line from the contact
  channels.

## [1.0.0] - 2026-07-01

First stable release: a full-site-editing (FSE) block theme for oaf.org.au,
carrying the OpenAustralia Foundation's maroon institutional brand.

### Added

- Full-site-editing setup driven by `theme.json`, with the block editor styled
  to match the front end.
- Templates for the front page, blog home, single posts, pages, archives,
  search and 404, plus a `page-no-title` template with a title hero.
- Reusable header and footer template parts.
- Block patterns for the site's key pages: home collection, home stats, about,
  collection, contact, donate and donate alternatives, people, plus header,
  footer and a donate band.
- Two custom blocks: People Grid and Raisely Donation Form.
- A People custom post type with a role taxonomy. Bios are stored as block
  markup, and a Quick Edit workflow manages each person's photo and role.
- Local avatars: People photos replace Gravatar for bylines and comments.
- Live statistics bridge that integrates with the Alaveteli Stats plugin when
  it is installed and degrades gracefully when it is not. Used by the home
  stats pattern.
- Admin tooling to create or recreate the site's required pages from patterns.
- A theme settings screen for editable options used by both the front end and
  the patterns.
- Self-hosted Fira Sans and Merriweather fonts (woff2 declared via `theme.json`
  `fontFace`), replacing an earlier Google Fonts CDN dependency.
- Maroon brand styling, including a dark-panel maroon variant.
- PHP_CodeSniffer with the WordPress Coding Standards (WPCS), enforced by a CI
  workflow that runs on pull requests.

### Versioning note

This theme ships updates through the Git Updater plugin, which tracks `main`
and reads the `Version` header in `style.css`. During early development the
version line briefly reached 1.1.0 and 1.1.1, then was re-baselined back through
0.0.x. 1.0.0 (2026-07-01) marks the first stable release under the consolidated
numbering and supersedes those earlier development builds.

[Unreleased]: https://github.com/openaustralia/oaf-wp-blocktheme/compare/v1.3.0...HEAD
[1.3.0]: https://github.com/openaustralia/oaf-wp-blocktheme/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/openaustralia/oaf-wp-blocktheme/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/openaustralia/oaf-wp-blocktheme/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/openaustralia/oaf-wp-blocktheme/releases/tag/v1.0.0
