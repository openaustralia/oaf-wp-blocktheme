#!/bin/sh
# Seed the verification site: permalinks, demo posts, pages (via patterns), reading settings.
set -e
cd /var/www/html

wp rewrite structure '/%postname%/' --hard

for cat in "Transparency" "Service update" "The foundation" "Partners" "People"; do
  wp term create category "$cat" >/dev/null 2>&1 || true
done

P1=$(wp post create --post_type=post --post_status=publish \
  --post_title="What FOI reform could mean for Right to Know" \
  --post_excerpt="The government has signalled changes to the Freedom of Information Act. Here is what we are watching, and what it could mean for the 12,346 requests already archived on Right to Know." \
  --post_content="<!-- wp:paragraph --><p>The Attorney-General department has released a discussion paper on reforming the Freedom of Information Act.</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class=\"wp-block-heading\">What is being proposed</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Right to Know carries 12,346 published FOI requests, archived for everyone to read.</p><!-- /wp:paragraph --><!-- wp:quote --><blockquote class=\"wp-block-quote\"><p>The government is funded by public money. People have a right to know how it is spent.</p></blockquote><!-- /wp:quote -->" \
  --post_date="2026-05-14 09:00:00" --porcelain)
wp post term set "$P1" category "Transparency" >/dev/null
echo "post1=$P1"

P2=$(wp post create --post_type=post --post_status=publish \
  --post_title="We added 23 councils to Planning Alerts coverage" \
  --post_excerpt="Twenty-three more councils now publish their planning applications through Planning Alerts - covering an extra 410,000 people." \
  --post_content="<!-- wp:paragraph --><p>Twenty-three more councils now publish their planning applications through Planning Alerts.</p><!-- /wp:paragraph -->" \
  --post_date="2026-04-28 09:00:00" --porcelain)
wp post term set "$P2" category "Service update" >/dev/null
echo "post2=$P2"

P3=$(wp post create --post_type=post --post_status=publish \
  --post_title="2025 in numbers: our annual report" \
  --post_excerpt="Last year we sent 1.4 million planning alerts, carried 12,346 FOI requests, and ended the year with 11 cents on the dollar in administration overhead." \
  --post_content="<!-- wp:paragraph --><p>Our full annual report is now online.</p><!-- /wp:paragraph -->" \
  --post_date="2026-04-02 09:00:00" --porcelain)
wp post term set "$P3" category "The foundation" >/dev/null
echo "post3=$P3"

for slug in about collection people contact donate; do
  title=$(printf '%s' "$slug" | awk '{print toupper(substr($0,1,1)) substr($0,2)}')
  ID=$(wp post create --post_type=page --post_status=publish --post_title="$title" --post_name="$slug" \
    --post_content="<!-- wp:pattern {\"slug\":\"oaf/page-$slug\"} /-->" --porcelain)
  echo "page-$slug=$ID"
done

HOME=$(wp post create --post_type=page --post_status=publish --post_title="Home" --post_name="home" --post_content="" --porcelain)
BLOG=$(wp post create --post_type=page --post_status=publish --post_title="Blog" --post_name="blog" --post_content="" --porcelain)
wp option update show_on_front page >/dev/null
wp option update page_on_front "$HOME" >/dev/null
wp option update page_for_posts "$BLOG" >/dev/null
echo "home=$HOME blog=$BLOG"
echo "SETUP_OK"
