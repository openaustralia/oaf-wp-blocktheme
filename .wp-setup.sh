#!/bin/sh
# Seed the verification site: permalinks, the required pages and reading settings.
set -e
cd /var/www/html

wp rewrite structure '/%postname%/' --hard

# The required pages are defined in inc/required-pages.php and created by the
# same code as Appearance -> OAF Theme -> "Create required pages" (which also
# points the front page and posts page at Home and Blog). That code loads under
# WP-CLI, so no require is needed.
wp eval '$failed = false; foreach ( oaf_reconcile_required_pages( "create", null, true ) as $slug => $result ) { echo str_pad( $slug, 14 ), $result["status"], isset( $result["error"] ) ? " (" . $result["error"]["code"] . ": " . $result["error"]["message"] . ")" : "", "\n"; $failed = $failed || "error" === $result["status"]; } if ( $failed ) { exit( 1 ); }'

echo "SETUP_OK"
