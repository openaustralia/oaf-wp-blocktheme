#!/bin/sh
# Seed the verification site: permalinks, the required pages and reading settings.
set -e
cd /var/www/html

wp rewrite structure '/%postname%/' --hard

# The required pages are defined in inc/required-pages.php and created by the
# same code as Appearance -> OAF Theme -> "Create required pages" (which also
# points the front page and posts page at Home and Blog). pages.php loads in
# admin only, so load it here.
wp eval 'require_once get_template_directory() . "/inc/pages.php"; print_r( oaf_create_required_pages( true ) );'

echo "SETUP_OK"
