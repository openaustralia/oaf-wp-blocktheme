#!/bin/sh
# Seed the verification site: permalinks, pages (via patterns), reading settings.
set -e
cd /var/www/html

wp rewrite structure '/%postname%/' --hard

for slug in about collection people contact donate; do
  title=$(printf '%s' "$slug" | awk '{print toupper(substr($0,1,1)) substr($0,2)}')
  ID=$(wp post create --post_type=page --post_status=publish --post_title="$title" --post_name="$slug" \
    --post_content="<!-- wp:pattern {\"slug\":\"oaf/page-$slug\"} /-->" --porcelain)
  # These pages supply their own hero via the pattern, so use the no-title canvas
  # template to avoid the title hero that page.html adds.
  wp post meta update "$ID" _wp_page_template page-no-title >/dev/null
  echo "page-$slug=$ID"
done

HOME=$(wp post create --post_type=page --post_status=publish --post_title="Home" --post_name="home" --post_content="" --porcelain)
BLOG=$(wp post create --post_type=page --post_status=publish --post_title="Blog" --post_name="blog" --post_content="" --porcelain)
wp option update show_on_front page >/dev/null
wp option update page_on_front "$HOME" >/dev/null
wp option update page_for_posts "$BLOG" >/dev/null
echo "home=$HOME blog=$BLOG"
echo "SETUP_OK"
