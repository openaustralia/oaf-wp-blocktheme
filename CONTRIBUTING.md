# Contributing

Thanks for helping improve the OpenAustralia Foundation block theme. This guide
covers the two things that trip people up most: keeping the changelog current,
and how a change actually reaches live sites.

## Getting set up

The repo *is* the theme, and there is no build step. For local work:

```sh
composer install        # dev tooling (PHPCS + WPCS); needed before linting
composer run phpcs      # lint all PHP (the only automated code check)
composer run phpcbf     # auto-fix PHPCS violations where possible
wp-env start            # boot WordPress with this repo as the active theme
```

See [README.md](README.md) for the theme tour and [CLAUDE.md](CLAUDE.md) for the
architecture notes. CI runs `composer validate --strict`, a `php -l` syntax
sweep, and `composer run phpcs` on every pull request.

## Every pull request updates the changelog

We keep [CHANGELOG.md](CHANGELOG.md) in the
[Keep a Changelog 1.1.0](https://keepachangelog.com/en/1.1.0/) format. Every pull
request adds an entry under the `## [Unreleased]` heading at the top of the file,
so the changelog is always current and we never reconstruct it from git history
later.

Group each entry under the type that fits, and only include the types you need:

- **Added** for new features.
- **Changed** for changes in existing functionality.
- **Deprecated** for soon-to-be-removed features.
- **Removed** for now-removed features.
- **Fixed** for bug fixes.
- **Security** for anything touching vulnerabilities.

Write for a person reading the release notes, not for a machine. Describe the
change and why it matters in a sentence or two, in Australian English, without
em dashes (use commas or hyphens). Do not paste commit messages or a diff.

A CI check fails a pull request whose diff does not touch `CHANGELOG.md`. If a
change genuinely has no user-facing or notable effect (CI tweaks, an internal
refactor, a typo in a comment), apply the **`no-changelog`** label to the pull
request and the check is skipped.

## Versioning: merging is not shipping

We follow [Semantic Versioning](https://semver.org/spec/v2.0.0.html):

- **MAJOR** for breaking changes,
- **MINOR** for new, backwards-compatible features,
- **PATCH** for backwards-compatible bug fixes.

Connected sites self-update through the Git Updater plugin, which tracks `main`
and offers an update **only when the `Version` header in `style.css` increases.**
So merging a pull request to `main` does *not* ship it. Changes accumulate under
`[Unreleased]` until someone decides it is time to release. **Not every merge
bumps the version**, and that is deliberate: it lets us batch several merged
changes into one release.

## Cutting a release

When the accumulated `[Unreleased]` changes are ready to go live, run the bump
command from a clean `main`:

```sh
mise run bump patch     # 1.3.0 -> 1.3.1
mise run bump minor     # 1.3.0 -> 1.4.0
mise run bump major     # 1.3.0 -> 2.0.0
mise run bump 1.5.2      # or set an explicit version
```

The command does the whole release in one step:

1. Bumps the `Version` header in `style.css`.
2. Renames `## [Unreleased]` in `CHANGELOG.md` to the new version with today's
   date, opens a fresh empty `## [Unreleased]`, and updates the compare-links.
3. Makes one signed commit (`Release X.Y.Z`) and a `vX.Y.Z` tag.
4. Asks for confirmation, then pushes `main` and the tag to GitHub, at which
   point Git Updater offers the update to connected sites.

It refuses to run if you are not on `main`, if the working tree is dirty, if
`[Unreleased]` is empty, or if the target tag already exists.
