# Changelog

All notable changes to the OpenAustralia Foundation block theme are documented
in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project aims to follow [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
