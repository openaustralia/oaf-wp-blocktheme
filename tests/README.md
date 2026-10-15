# Tests

There are two suites.

**Unit** tests (this directory) cover the theme's own PHP logic and are fast.
There is deliberately no WordPress install behind them (no wp-env, no Docker, no
database): `tests/bootstrap.php` defines lightweight stubs for the handful of
WordPress functions the tested modules touch, then loads the individual
`inc/*.php` files.

```sh
composer install        # once, to get PHPUnit (a require-dev dependency)
composer run test       # run the suite (also runs in CI, see .github/workflows/php.yml)
```

**Integration** tests (`tests/integration/`) run against real WordPress, for the
code the unit stubs cannot reach: page writes (`wp_insert_post()`,
`wp_update_post()`) and the template hierarchy. They run inside the wp-env test
container with plain PHPUnit 11 (WordPress's own test library does not support
PHPUnit 11), and each test starts and ends with no pages.

```sh
wp-env start                    # once; the mise-installed @wordpress/env, not `npx wp-env`
composer run test:integration   # runs in the tests-cli container (also in CI: integration.yml)
```

The suite deletes every page, so `tests/integration/bootstrap.php` refuses to run
unless the database is wp-env's test one (`tests-wordpress`); it will not touch
the development site on port 8888. If `wp-env run` times out on a slow network,
set `NODE_OPTIONS=--network-family-autoselection-attempt-timeout=5000`.

## What is covered

| File | Covers |
| --- | --- |
| `OptionsTest` | `oaf_option()` default-vs-saved-empty behaviour, `oaf_service_urls()`, and `oaf_sanitize_options()` routing + the `unfiltered_html` gate on the Raisely embed |
| `ContactFormTest` | Enquiry-type routing in `inc/contact-form.php` |
| `ContactRouteSyncTest` | Guards the drift CLAUDE.md warns about: the contact pattern's field-select options must match `oaf_contact_routes()` |
| `StatsTest` | `oaf_stat()` fallback when the Alaveteli stats plugin is absent |
| `AvatarsTest` | Initials, deterministic palette colour, and the initials-SVG data URI |
| `RequiredPagesTest` | Which content is a pattern page, plus guards that the required pages match `patterns/`, `theme.json` and the dev seed script |
| `TemplatesTest` | Which pages with a stale classic `_wp_page_template` fall back to their own template |
| `StructureTest` | `theme.json` / `block.json` validity, block asset targets exist, and the `style.css` Version header Git Updater keys on |

### Integration tests

| File | Covers |
| --- | --- |
| `RequiredPageWritesTest` | Creating, skipping, overwriting and repairing the required pages on real WordPress, including the stale-template save that failed in production |
| `TemplateHierarchyTest` | The stale-template fallback through the real `page_template_hierarchy` filter and a queried page |

## Ground rule

Because the WordPress functions are **stubs** (pass-throughs), tests assert on the
theme's *own* logic — routing, branching, defaults — never on WordPress behaviour
we've replaced. For example, do not assert that `esc_url_raw()` sanitises a URL:
here it is a pass-through. This is unit coverage of our code, not integration
coverage of WordPress; end-to-end/visual checks remain a separate manual layer.
