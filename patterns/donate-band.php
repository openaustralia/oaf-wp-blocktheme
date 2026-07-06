<?php
/**
 * Title: Donate band
 * Slug: oaf/donate-band
 * Categories: oaf, call-to-action
 * Description: Full-width maroon donation call to action with the tax-status note.
 *
 * @package oaf-wp-blocktheme
 */

?>
<!-- wp:group {"align":"full","className":"oaf-donateband","backgroundColor":"maroon","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull oaf-donateband has-maroon-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:group {"className":"oaf-donateband__text","layout":{"type":"default"}} -->
		<div class="wp-block-group oaf-donateband__text">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Keep the collection open</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Funded entirely by donations from people who use the services and want them to keep going. OpenAustralia Foundation is a registered charity. Donations are not currently tax deductible.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:buttons {"className":"oaf-cta-row"} -->
		<div class="wp-block-buttons oaf-cta-row">
			<!-- wp:button {"className":"is-style-oaf-rev"} -->
			<div class="wp-block-button is-style-oaf-rev"><a class="wp-block-button__link wp-element-button" href="/donate/">Donate now</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
