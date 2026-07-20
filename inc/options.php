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
			'org_name'                   => 'OpenAustralia Foundation',
			'abn'                        => '24 138 089 942',
			'acnc_url'                   => 'https://www.acnc.gov.au/charity/charities/6bf25724-39af-e811-a960-000d3ad24282/profile',
			'abr_url'                    => 'https://www.abr.business.gov.au/ABN/View/24138089942',
			'github_url'                 => 'https://github.com/openaustralia',
			'bluesky_url'                => 'https://bsky.app/profile/oaf.org.au',
			'mastodon_url'               => 'https://social.oaf.org.au/@oaf',
			'linkedin_url'               => 'https://www.linkedin.com/company/openaustralia-foundation',
			'planningalerts_url'         => 'https://www.planningalerts.org.au',
			'righttoknow_url'            => 'https://www.righttoknow.org.au',
			'theyvoteforyou_url'         => 'https://theyvoteforyou.org.au',
			'openaustralia_url'          => 'https://www.openaustralia.org.au',
			'acknowledgement'            => 'OpenAustralia Foundation acknowledges the traditional Owners of Country throughout Australia and acknowledges their continuing connection to land, waters and community. We pay our respects to the people, the cultures and the Elders past and present.',
			'raisely_embed'              => '',
			'contributor_exclude_logins' => '',
		);
	}
}

if ( ! function_exists( 'oaf_option' ) ) {
	/**
	 * Read a single theme option.
	 *
	 * A saved value is always returned, including an empty string, so an admin can
	 * intentionally blank a field (e.g. remove a social link). The default is used
	 * only when the key has never been saved.
	 *
	 * @param string $key Option key.
	 * @return string
	 */
	function oaf_option( $key ) {
		$opts = get_option( 'oaf_theme_options', array() );

		if ( is_array( $opts ) && array_key_exists( $key, $opts ) ) {
			return $opts[ $key ];
		}

		$defaults = oaf_default_options();
		return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
}

if ( ! function_exists( 'oaf_service_urls' ) ) {
	/**
	 * The sister-service URLs, keyed by service. Single source of which option keys
	 * are "the services", so the footer and collection patterns stay in sync.
	 *
	 * @return array<string,string> service slug => URL (may be an empty string).
	 */
	function oaf_service_urls() {
		return array(
			'planningalerts' => oaf_option( 'planningalerts_url' ),
			'righttoknow'    => oaf_option( 'righttoknow_url' ),
			'theyvoteforyou' => oaf_option( 'theyvoteforyou_url' ),
			'openaustralia'  => oaf_option( 'openaustralia_url' ),
		);
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

		// GitHub logins to hide from the Contributors grid: normalise to one
		// lower-case login per line (shared with inc/contributors.php).
		$exclude_raw                         = isset( $input['contributor_exclude_logins'] ) ? $input['contributor_exclude_logins'] : '';
		$exclude                             = function_exists( 'oaf_normalize_login_list' ) ? oaf_normalize_login_list( $exclude_raw ) : array();
		$clean['contributor_exclude_logins'] = implode( "\n", $exclude );

		// The Raisely embed may contain <script>/<iframe>, so it is stored raw
		// rather than run through wp_kses (which would strip the embed). Gate that
		// on `unfiltered_html` - the capability WordPress uses for storing raw
		// markup - not merely `manage_options`. On single-site the two coincide; on
		// multisite this stops a non-super-admin site admin injecting front-end
		// script. A save without the capability preserves the existing value.
		if ( current_user_can( 'unfiltered_html' ) && isset( $input['raisely_embed'] ) ) {
			$clean['raisely_embed'] = trim( $input['raisely_embed'] );
		} else {
			$existing               = get_option( 'oaf_theme_options', array() );
			$clean['raisely_embed'] = isset( $existing['raisely_embed'] ) ? $existing['raisely_embed'] : '';
		}

		return $clean;
	}
}
