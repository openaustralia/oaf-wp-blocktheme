<?php
/**
 * Title: Home - stats band
 * Slug: oaf/home-stats
 * Categories: oaf
 * Inserter: no
 *
 * The full-width figures band beneath the home-page hero. The Right to Know
 * FOI count is sourced live from the wp-alaveteli-stats plugin when present
 * (oaf_stat()), falling back to fixed copy; the other figures are fixed.
 *
 * @package oaf-wp-blocktheme
 */

?>
<!-- wp:group {"align":"full","className":"oaf-stats","backgroundColor":"maroon-deep","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-stats has-maroon-deep-background-color has-background">
	<!-- wp:html -->
	<div class="oaf-ct">
		<div class="oaf-stat"><div class="oaf-stat__num">1.4 million</div><div class="oaf-stat__label">planning alerts sent last year</div></div>
		<div class="oaf-stat"><div class="oaf-stat__num"><?php echo oaf_stat( 'visible_request_count', '12,346' ); ?></div><div class="oaf-stat__label">FOI requests archived</div></div>
		<div class="oaf-stat"><div class="oaf-stat__num">200+</div><div class="oaf-stat__label">parliamentary divisions explained</div></div>
		<div class="oaf-stat"><div class="oaf-stat__num">2007</div><div class="oaf-stat__label">building public tools since</div></div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
