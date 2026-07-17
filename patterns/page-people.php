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
		<h1 class="wp-block-heading">On the tools.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-lead"} -->
		<p class="oaf-lead">This work is a collective - a long list of volunteers headed by a small team. Contributors, staff and board are proud to put their names to their work.</p>
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
		<p class="oaf-intro">Keeping services running day to day.</p>
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
		<p class="oaf-intro">The board meets four times a year. Governance documents are public.</p>
		<!-- /wp:paragraph -->
		<!-- wp:oaf/people-grid {"group":"board-of-directors"} /-->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band oaf-people-section","backgroundColor":"white","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band oaf-people-section has-white-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">Contributors</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-intro"} -->
		<p class="oaf-intro">Our services are open source. Thanks to the volunteers who build and improve them across all our projects.</p>
		<!-- /wp:paragraph -->
		<!-- wp:oaf/contributors /-->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"oaf/donate-band"} /-->
