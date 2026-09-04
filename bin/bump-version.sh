#!/bin/sh
# Cut a release: bump the Version header in style.css, roll CHANGELOG.md's
# [Unreleased] section into a dated version, commit, tag, and (after a prompt)
# push to origin/main so Git Updater offers the update to connected sites, then
# publish the GitHub Release.
#
# Usage: bin/bump-version.sh [-y|--yes] <major|minor|patch|X.Y.Z>
#   mise run bump minor
#   mise run bump 1.5.2
#
# Guards: must be on main, clean working tree, non-empty [Unreleased], and the
# target tag must not already exist.
set -eu

assume_yes=0
level=""
for arg in "$@"; do
  case "$arg" in
    -y|--yes) assume_yes=1 ;;
    major|minor|patch) level="$arg" ;;
    [0-9]*.[0-9]*.[0-9]*) level="$arg" ;;
    *) printf 'Unknown argument: %s\n' "$arg" >&2; exit 2 ;;
  esac
done

if [ -z "$level" ]; then
  echo "Usage: bin/bump-version.sh [-y|--yes] <major|minor|patch|X.Y.Z>" >&2
  exit 2
fi

# --- Move to repo root so relative paths work however we're invoked ---
cd "$(git rev-parse --show-toplevel)"

# --- Preflight guards ---
branch=$(git rev-parse --abbrev-ref HEAD)
if [ "$branch" != "main" ]; then
  echo "Error: releases are cut from 'main' (you are on '$branch')." >&2
  exit 1
fi

if [ -n "$(git status --porcelain)" ]; then
  echo "Error: working tree is not clean. Commit or stash first." >&2
  exit 1
fi

# [Unreleased] must contain at least one real entry (ignore blanks and ### headings).
unreleased=$(awk '
  /^## \[Unreleased\]/ { grab=1; next }
  grab && /^## \[/ { exit }
  grab { print }
' CHANGELOG.md | grep -v '^[[:space:]]*$' | grep -v '^###' || true)
if [ -z "$unreleased" ]; then
  echo "Error: the [Unreleased] section is empty, nothing to release." >&2
  exit 1
fi

# --- Read current version from the style.css header ---
current=$(sed -n -E 's/^[[:space:]]*Version:[[:space:]]*([0-9]+\.[0-9]+\.[0-9]+).*/\1/p' style.css | head -n1)
if [ -z "$current" ]; then
  echo "Error: could not read Version from style.css." >&2
  exit 1
fi

maj=${current%%.*}
rest=${current#*.}
min=${rest%%.*}
pat=${rest#*.}

case "$level" in
  major) new="$((maj + 1)).0.0" ;;
  minor) new="$maj.$((min + 1)).0" ;;
  patch) new="$maj.$min.$((pat + 1))" ;;
  *)     new="$level" ;;
esac

# New version must be strictly greater (Git Updater only ships on an increase).
if [ "$new" = "$current" ] || \
   [ "$(printf '%s\n%s\n' "$current" "$new" | sort -V | tail -n1)" != "$new" ]; then
  echo "Error: new version $new is not greater than current $current." >&2
  exit 1
fi

if git rev-parse -q --verify "refs/tags/v$new" >/dev/null; then
  echo "Error: tag v$new already exists." >&2
  exit 1
fi

# --- Derive the repo URL for changelog compare-links ---
slug=$(git remote get-url origin | sed -E 's#^git@github\.com:##; s#^https://github\.com/##; s#\.git$##')
repo_url="https://github.com/$slug"
today=$(date +%F)

printf 'Releasing %s -> %s (%s)\n' "$current" "$new" "$today"

# --- Edit style.css Version header (first matching line only) ---
awk -v v="$new" '
  !done && /^[[:space:]]*Version:[[:space:]]/ { sub(/Version:.*/, "Version: " v); done = 1 }
  { print }
' style.css > style.css.tmp && mv style.css.tmp style.css

# --- Roll CHANGELOG.md: rename [Unreleased] -> [new] - today, reopen an empty
#     [Unreleased], and update the footer compare-links. ---
awk -v v="$new" -v old="$current" -v date="$today" -v repo="$repo_url" '
  !head && /^## \[Unreleased\]/ {
    print "## [Unreleased]"
    print ""
    print "## [" v "] - " date
    head = 1
    next
  }
  /^\[Unreleased\]:/ {
    print "[Unreleased]: " repo "/compare/v" v "...HEAD"
    print "[" v "]: " repo "/compare/v" old "...v" v
    next
  }
  { print }
' CHANGELOG.md > CHANGELOG.md.tmp && mv CHANGELOG.md.tmp CHANGELOG.md

# --- Commit and tag (signing honours your git config; never bypassed) ---
git add style.css CHANGELOG.md
git commit -m "Release $new"
# Annotated tag (signed if your git config signs tags); --follow-tags pushes it.
git tag -a -m "Release $new" "v$new"

# --- Confirm, then push ---
if [ "$assume_yes" -ne 1 ]; then
  printf 'Push release %s to origin/main now? This ships it to connected sites. [y/N] ' "$new"
  read -r reply
  case "$reply" in
    y|Y|yes|YES) ;;
    *)
      echo "Committed and tagged locally, not pushed."
      echo "To ship: git push origin main --follow-tags"
      echo "Then:    gh release create v$new --generate-notes"
      exit 0
      ;;
  esac
fi

git push origin main --follow-tags
echo "Pushed v$new. Git Updater will offer the update to connected sites."

# --- Publish the GitHub Release ---
# Pushing the tag does NOT create a Release; they are separate objects. Git
# Updater ships from the tag and the style.css header either way, so a failure
# here does not affect what connected sites receive - it only leaves the
# Releases page missing an entry. So warn and carry on rather than exiting
# non-zero on a release that has already shipped. The GitHub CLI is not assumed
# to be installed.
if ! command -v gh >/dev/null 2>&1; then
  echo "Note: GitHub CLI (gh) not found, so no GitHub Release was published." >&2
  echo "  Publish it with: gh release create v$new --generate-notes" >&2
  echo "  Or by hand:      $repo_url/releases/new?tag=v$new" >&2
elif gh release create "v$new" --title "v$new" --generate-notes; then
  echo "Published the v$new release."
else
  echo "Warning: the GitHub Release could not be published (the code is pushed" >&2
  echo "and shipping regardless). Retry with:" >&2
  echo "  gh release create v$new --generate-notes" >&2
fi
