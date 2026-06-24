<?php
/**
 * Title: Header (masthead)
 * Slug: oaf/header
 * Categories: oaf
 * Block Types: core/template-part/header
 * Inserter: no
 *
 * @package oaf-wp-blocktheme
 */

$oaf_uri  = get_template_directory_uri();
$oaf_home = home_url( '/' );
?>
<!-- wp:group {"align":"full","className":"oaf-masthead","backgroundColor":"maroon","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull oaf-masthead has-maroon-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:html -->
		<a class="oaf-masthead__logo" href="<?php echo esc_url( $oaf_home ); ?>" aria-label="OpenAustralia Foundation - home"><img src="<?php echo esc_url( $oaf_uri . '/assets/img/oaf-wordmark-white.svg' ); ?>" alt="OpenAustralia Foundation" width="220" height="24"></a>
		<!-- /wp:html -->

		<!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"left"}} -->
			<!-- wp:navigation-link {"label":"About","url":"/about/","kind":"custom","isTopLevelLink":true} /-->
			<!-- wp:navigation-link {"label":"Collection","url":"/collection/","kind":"custom","isTopLevelLink":true} /-->
			<!-- wp:navigation-link {"label":"Blog","url":"/blog/","kind":"custom","isTopLevelLink":true} /-->
			<!-- wp:navigation-link {"label":"People","url":"/people/","kind":"custom","isTopLevelLink":true} /-->
			<!-- wp:navigation-link {"label":"Contact","url":"/contact/","kind":"custom","isTopLevelLink":true} /-->
		<!-- /wp:navigation -->

		<!-- wp:group {"className":"oaf-donate-btn","layout":{"type":"default"}} -->
		<div class="wp-block-group oaf-donate-btn">
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/donate/">Donate</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
