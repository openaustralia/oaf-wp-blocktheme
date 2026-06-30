<?php
/**
 * Title: Page - Donate
 * Slug: oaf/page-donate
 * Categories: oaf
 * Block Types: core/post-content
 * Post Types: page
 * Description: Full Donate page - hero, the Raisely donation form and what it funds.
 *
 * NOTE: the donation form is the "Raisely Donation Form" block, which renders the
 * embed configured in Appearance -> OAF Theme. Keep the tax-status wording ("not
 * tax deductible") below it - it is mandatory.
 *
 * @package oaf-wp-blocktheme
 */

?>
<!-- wp:group {"align":"full","className":"oaf-pagehero","backgroundColor":"maroon","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:paragraph {"className":"oaf-eyebrow"} -->
		<p class="oaf-eyebrow">Support us</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading">Keep the collection open.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-lead"} -->
		<p class="oaf-lead">Your donation keeps Planning Alerts, Right to Know, They Vote for You and OpenAustralia.org.au online - free for everyone.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band","backgroundColor":"sand","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band has-sand-background-color has-background">
	<!-- wp:group {"className":"oaf-ct oaf-ct--920 oaf-grid-donate","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct oaf-ct--920 oaf-grid-donate">
		<!-- wp:group {"className":"oaf-donate-card","layout":{"type":"default"}} -->
		<div class="wp-block-group oaf-donate-card">
			<!-- wp:oaf/raisely-donation-form /-->
			<!-- wp:html -->
			<p class="oaf-fineprint">OpenAustralia Foundation is a charity registered with ACNC and endorsed for charity tax concessions. At this time, donations are <strong>not</strong> tax deductible.</p>
			<!-- /wp:html -->
			<!-- wp:paragraph -->
			<p>Prefer not to use a card? <a href="/donate/alternatives/">See other ways to give</a>.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
			<h2 class="wp-block-heading has-x-large-font-size">What your donation does</h2>
			<!-- /wp:heading -->
			<!-- wp:html -->
			<ul class="oaf-funds">
				<li><span class="oaf-funds__check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg></span><div><div class="oaf-funds__h">Sends the alerts</div><div class="oaf-funds__b">Planning Alerts emailed 1.4 million alerts last year. Your donation keeps the scrapers running.</div></div></li>
				<li><span class="oaf-funds__check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg></span><div><div class="oaf-funds__h">Keeps the archive public</div><div class="oaf-funds__b">Right to Know carries <?php echo oaf_stat( 'visible_request_count', '12,346' ); ?> FOI requests, archived for everyone to read.</div></div></li>
				<li><span class="oaf-funds__check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg></span><div><div class="oaf-funds__h">Explains the votes</div><div class="oaf-funds__b">They Vote for You turns 200+ parliamentary divisions into plain English each year.</div></div></li>
				<li><span class="oaf-funds__check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg></span><div><div class="oaf-funds__h">No ads, no data selling</div><div class="oaf-funds__b">We don&rsquo;t run ads or sell your details. Donations from people like you keep us independent.</div></div></li>
			</ul>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
