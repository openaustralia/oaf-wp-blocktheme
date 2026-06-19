<?php
/**
 * Editable theme options: defaults, accessor and sanitisation.
 *
 * A single wp_option (`oaf_theme_options`) stores an associative array of every
 * editable value. Defaults mirror the original hard-coded content, so the site
 * renders identically until an admin saves the settings form.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_default_options' ) ) {
	/**
	 * Default value for every editable option.
	 *
	 * @return array<string,string>
	 */
	function oaf_default_options() {
		return array(
			'org_name'           => 'OpenAustralia Foundation',
			'abn'                => '24 138 089 942',
			'acnc_url'           => 'https://www.acnc.gov.au/charity/charities/6bf25724-39af-e811-a960-000d3ad24282/profile',
			'abr_url'            => 'https://www.abr.business.gov.au/ABN/View/24138089942',
			'github_url'         => 'https://github.com/openaustralia',
			'bluesky_url'        => 'https://bsky.app/profile/oaf.org.au',
			'mastodon_url'       => 'https://social.oaf.org.au/@oaf',
			'linkedin_url'       => 'https://www.linkedin.com/company/openaustralia-foundation',
			'planningalerts_url' => 'https://www.planningalerts.org.au',
			'righttoknow_url'    => 'https://www.righttoknow.org.au',
			'theyvoteforyou_url' => 'https://theyvoteforyou.org.au',
			'openaustralia_url'  => 'https://www.openaustralia.org.au',
			'acknowledgement'    => 'We acknowledge the traditional owners of the land now known as Australia. We pay our respects to their elders past, present and emerging.',
			'raisely_embed'      => '',
		);
	}
}

if ( ! function_exists( 'oaf_option' ) ) {
	/**
	 * Read a single theme option, falling back to its default when unset or empty.
	 *
	 * `raisely_embed` legitimately defaults to an empty string, so an empty value
	 * there is returned as-is.
	 *
	 * @param string $key Option key.
	 * @return string
	 */
	function oaf_option( $key ) {
		$defaults = oaf_default_options();
		$opts     = get_option( 'oaf_theme_options', array() );
		$value    = isset( $opts[ $key ] ) ? $opts[ $key ] : '';

		if ( '' === $value && isset( $defaults[ $key ] ) ) {
			return $defaults[ $key ];
		}

		return $value;
	}
}

if ( ! function_exists( 'oaf_sanitize_options' ) ) {
	/**
	 * Sanitise the settings array before it is stored.
	 *
	 * Values arrive already unslashed from wp-admin/options.php.
	 *
	 * @param array $input Raw submitted values.
	 * @return array
	 */
	function oaf_sanitize_options( $input ) {
		$input = is_array( $input ) ? $input : array();
		$clean = array();

		$url_keys = array(
			'acnc_url',
			'abr_url',
			'github_url',
			'bluesky_url',
			'mastodon_url',
			'linkedin_url',
			'planningalerts_url',
			'righttoknow_url',
			'theyvoteforyou_url',
			'openaustralia_url',
		);
		foreach ( $url_keys as $key ) {
			$clean[ $key ] = isset( $input[ $key ] ) ? esc_url_raw( trim( $input[ $key ] ) ) : '';
		}

		foreach ( array( 'org_name', 'abn', 'acknowledgement' ) as $key ) {
			$clean[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '';
		}

		// The Raisely embed may contain <script>/<iframe>. Only users who can
		// manage options reach this screen, so store it raw rather than running
		// it through wp_kses (which would strip the embed). Preserve the existing
		// value if a lower-capability save ever occurs.
		if ( current_user_can( 'manage_options' ) && isset( $input['raisely_embed'] ) ) {
			$clean['raisely_embed'] = trim( $input['raisely_embed'] );
		} else {
			$existing               = get_option( 'oaf_theme_options', array() );
			$clean['raisely_embed'] = isset( $existing['raisely_embed'] ) ? $existing['raisely_embed'] : '';
		}

		return $clean;
	}
}
