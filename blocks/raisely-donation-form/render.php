<?php
/**
 * Server render for the Raisely Donation Form block.
 *
 * Outputs the embed snippet configured in Appearance -> OAF Theme. That value can
 * only be saved by someone with the `unfiltered_html` capability and may contain
 * <script>/<iframe>, so it is echoed raw rather than escaped. When empty, editors
 * see a prompt and the public sees nothing.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oaf_embed   = function_exists( 'oaf_option' ) ? trim( oaf_option( 'raisely_embed' ) ) : '';
$oaf_wrapper = get_block_wrapper_attributes( array( 'class' => 'oaf-raisely' ) );

if ( '' === $oaf_embed ) {
	if ( current_user_can( 'manage_options' ) ) {
		printf(
			'<div %1$s><p class="oaf-raisely__empty">%2$s</p></div>',
			$oaf_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped wrapper.
			esc_html__( 'Add your Raisely embed code in Appearance → OAF Theme to show the donation form here.', 'oaf-wp-blocktheme' )
		);
	}
	return;
}

printf(
	'<div %1$s>%2$s</div>',
	$oaf_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped wrapper.
	$oaf_embed // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted admin-provided embed.
);
