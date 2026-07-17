<?php
/**
 * Server render for the Contributors block.
 *
 * Outputs the cached, de-duplicated volunteer contributors (see
 * inc/contributors.php) as a grid of avatars linking to each person's GitHub
 * profile. Avatars are self-hosted, so nothing here calls a third party. A
 * contributor whose avatar could not be stored falls back to the same initials
 * circle used elsewhere in the theme.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oaf_people  = function_exists( 'oaf_get_contributors' ) ? oaf_get_contributors() : array();
$oaf_wrapper = get_block_wrapper_attributes( array( 'class' => 'oaf-contributors' ) );

if ( empty( $oaf_people ) ) {
	// While the first background refresh runs the cache is empty. Nudge editors;
	// show nothing to visitors.
	if ( current_user_can( 'edit_posts' ) ) {
		printf(
			'<div %1$s><p>%2$s</p></div>',
			$oaf_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped wrapper.
			esc_html__( 'Contributors are being fetched from GitHub. Reload in a moment.', 'oaf-wp-blocktheme' )
		);
	}
	return;
}

$oaf_palette = function_exists( 'oaf_person_palette' ) ? oaf_person_palette() : array( '#800000' );

echo '<div ' . $oaf_wrapper . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped wrapper.

foreach ( $oaf_people as $oaf_person ) {
	$oaf_login   = isset( $oaf_person['login'] ) ? $oaf_person['login'] : '';
	$oaf_profile = isset( $oaf_person['profile'] ) ? $oaf_person['profile'] : '';
	$oaf_avatar  = isset( $oaf_person['avatar'] ) ? $oaf_person['avatar'] : '';

	if ( '' === $oaf_login ) {
		continue;
	}

	if ( '' !== $oaf_avatar ) {
		$oaf_avatar_html = sprintf(
			'<span class="oaf-avatar oaf-avatar--photo"><img src="%1$s" alt="" width="72" height="72" loading="lazy" decoding="async" /></span>',
			esc_url( $oaf_avatar )
		);
	} else {
		$oaf_color       = ( function_exists( 'oaf_avatar_color_for_name' ) )
			? oaf_avatar_color_for_name( $oaf_login )
			: $oaf_palette[0];
		$oaf_initials    = function_exists( 'oaf_person_initials' ) ? oaf_person_initials( $oaf_login ) : mb_strtoupper( mb_substr( $oaf_login, 0, 1 ) );
		$oaf_avatar_html = sprintf(
			'<span class="oaf-avatar" style="background:%1$s" aria-hidden="true">%2$s</span>',
			esc_attr( $oaf_color ),
			esc_html( $oaf_initials )
		);
	}

	printf(
		'<a class="oaf-contributor" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s<span class="oaf-contributor__name">%3$s</span></a>',
		esc_url( $oaf_profile ),
		$oaf_avatar_html, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
		esc_html( $oaf_login )
	);
}

echo '</div>';
