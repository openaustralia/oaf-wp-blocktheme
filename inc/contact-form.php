<?php
/**
 * Contact form routing.
 *
 * The Contact page uses a single Jetpack multi-step form. A single form has one
 * static recipient, so routing by enquiry type is done here: the first step asks
 * "What is your enquiry about?" and these filters map the chosen option to the
 * right inbox and subject prefix via Jetpack's `contact_form_to` and
 * `contact_form_subject` filters. The matching option strings must stay in sync
 * with the `field-select` options in patterns/page-contact.php.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_contact_routes' ) ) {
	/**
	 * Enquiry-type option => [ recipient email, subject prefix ].
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	function oaf_contact_routes() {
		return array(
			'General enquiry'               => array( 'contact@oaf.org.au', '[OAF Contact]' ),
			'Right to Know'                 => array( 'contact@righttoknow.org.au', '[OAF Contact: Right to Know]' ),
			'They Vote for You'             => array( 'contact@theyvoteforyou.org.au', '[OAF Contact: They Vote for You]' ),
			'PlanningAlerts'                => array( 'contact@planningalerts.org.au', '[OAF Contact: PlanningAlerts]' ),
			'OpenAustralia.org.au'          => array( 'contact@openaustralia.org.au', '[OAF Contact: OpenAustralia.org.au]' ),
			'Media enquiry'                 => array( 'media@oaf.org.au', '[Media Contact]' ),
			'Government or law enforcement' => array( 'exec@oaf.org.au', '[OAF Contact: Gov/LEO]' ),
		);
	}
}

if ( ! function_exists( 'oaf_contact_match_route' ) ) {
	/**
	 * Find the route for a submission by scanning its values for a known
	 * enquiry-type option. Returns null when none match, so submissions from any
	 * other Jetpack form on the site pass through untouched.
	 *
	 * @param array $all_values Submitted contact-form values.
	 * @return array{0:string,1:string}|null
	 */
	function oaf_contact_match_route( $all_values ) {
		$routes = oaf_contact_routes();
		foreach ( (array) $all_values as $value ) {
			if ( is_string( $value ) && isset( $routes[ $value ] ) ) {
				return $routes[ $value ];
			}
		}
		return null;
	}
}

if ( ! function_exists( 'oaf_contact_form_to' ) ) {
	/**
	 * Route the submission to the inbox for the chosen enquiry type.
	 *
	 * @param string|array $to         Default recipient(s).
	 * @param array        $all_values Submitted contact-form values.
	 * @return string|array
	 */
	function oaf_contact_form_to( $to, $all_values ) {
		$route = oaf_contact_match_route( $all_values );
		return $route ? $route[0] : $to;
	}
}
add_filter( 'contact_form_to', 'oaf_contact_form_to', 10, 2 );

if ( ! function_exists( 'oaf_contact_form_subject' ) ) {
	/**
	 * Apply the subject prefix for the chosen enquiry type.
	 *
	 * @param string $subject    Default subject.
	 * @param array  $all_values Submitted contact-form values.
	 * @return string
	 */
	function oaf_contact_form_subject( $subject, $all_values ) {
		$route = oaf_contact_match_route( $all_values );
		return $route ? $route[1] : $subject;
	}
}
add_filter( 'contact_form_subject', 'oaf_contact_form_subject', 10, 2 );

if ( ! function_exists( 'oaf_contact_form_assets' ) ) {
	/**
	 * Enqueue the contact-form enhancement script on the page that uses the
	 * contact pattern. The page stores the pattern as a reference, so its blocks
	 * are not in post_content; detect the pattern slug (or an inline Jetpack form)
	 * instead of relying on has_block() alone.
	 */
	function oaf_contact_form_assets() {
		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$uses_pattern = false !== strpos( $post->post_content, 'oaf/page-contact' );
		if ( ! $uses_pattern && ! has_block( 'jetpack/contact-form', $post ) ) {
			return;
		}

		wp_enqueue_script(
			'oaf-contact-form',
			get_template_directory_uri() . '/assets/js/contact-form.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'oaf_contact_form_assets' );
