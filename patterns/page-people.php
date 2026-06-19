<?php
/**
 * Title: Page - People
 * Slug: oaf/page-people
 * Categories: oaf
 * Block Types: core/post-content
 * Post Types: page
 * Description: Full People page - hero, staff and board of directors.
 *
 * @package oaf-wp-blocktheme
 */
?>
<!-- wp:group {"align":"full","className":"oaf-pagehero","backgroundColor":"maroon","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:paragraph {"className":"oaf-eyebrow"} -->
		<p class="oaf-eyebrow">People</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading">People before software.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-lead"} -->
		<p class="oaf-lead">Our contributors are named. Our staff are named. Our board is named. The work is collective - a small team and a long list of volunteers.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band oaf-people-section","backgroundColor":"white","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band oaf-people-section has-white-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">Team</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-intro"} -->
		<p class="oaf-intro">A small team keeps the services running day to day.</p>
		<!-- /wp:paragraph -->
		<!-- wp:oaf/people-grid {"group":"team"} /-->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band oaf-people-section","backgroundColor":"sand","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band oaf-people-section has-sand-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">Board of directors</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-intro"} -->
		<p class="oaf-intro">The board met four times last financial year. Governance documents are public.</p>
		<!-- /wp:paragraph -->
		<!-- wp:oaf/people-grid {"group":"board-of-directors"} /-->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"oaf/donate-band"} /-->
