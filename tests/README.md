# Tests

Fast **unit** tests for the theme's PHP logic. There is deliberately no full
WordPress test install (no wp-env, no Docker, no database) — `tests/bootstrap.php`
defines lightweight stubs for the handful of WordPress functions the tested
modules touch, then loads the individual `inc/*.php` files.

```sh
composer install        # once, to get PHPUnit (a require-dev dependency)
composer run test       # run the suite (also runs in CI, see .github/workflows/php.yml)
```

## What is covered

| File | Covers |
| --- | --- |
| `OptionsTest` | `oaf_option()` default-vs-saved-empty behaviour, `oaf_service_urls()`, and `oaf_sanitize_options()` routing + the `unfiltered_html` gate on the Raisely embed |
| `ContactFormTest` | Enquiry-type routing in `inc/contact-form.php` |
| `ContactRouteSyncTest` | Guards the drift CLAUDE.md warns about: the contact pattern's field-select options must match `oaf_contact_routes()` |
| `StatsTest` | `oaf_stat()` fallback when the Alaveteli stats plugin is absent |
| `AvatarsTest` | Initials, deterministic palette colour, and the initials-SVG data URI |
| `StructureTest` | `theme.json` / `block.json` validity, block asset targets exist, and the `style.css` Version header Git Updater keys on |

## Ground rule

Because the WordPress functions are **stubs** (pass-throughs), tests assert on the
theme's *own* logic — routing, branching, defaults — never on WordPress behaviour
we've replaced. For example, do not assert that `esc_url_raw()` sanitises a URL:
here it is a pass-through. This is unit coverage of our code, not integration
coverage of WordPress; end-to-end/visual checks remain a separate manual layer.
