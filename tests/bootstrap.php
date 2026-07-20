<?php
/**
 * PHPUnit bootstrap for the OAF block theme.
 *
 * This is a *unit* harness, deliberately not a full WordPress test install
 * (no wp-env, no Docker, no DB). It defines lightweight no-op / pass-through
 * stubs for the handful of WordPress functions the tested modules touch, then
 * loads the individual `inc/*.php` files so their pure helpers can be exercised
 * in isolation.
 *
 * IMPORTANT: because the WP functions are stubs, tests must assert on the
 * theme's *own* logic (routing, branching, defaults) and never on WordPress
 * behaviour we have replaced. e.g. do not assert that esc_url_raw() sanitises a
 * URL - that is WordPress's job and here it is a pass-through.
 *
 * @package oaf-wp-blocktheme
 */

define( 'ABSPATH', __DIR__ . '/../' );
define( 'OAF_THEME_DIR', dirname( __DIR__ ) );

/*
 * Test-controllable state. Tests reset these in setUp() rather than sharing
 * state between cases.
 */
$GLOBALS['oaf_test_options'] = array();
$GLOBALS['oaf_test_caps']    = array();

// --- Hook registration: no-ops so file-scope add_action/add_filter calls in
// the loaded modules do not fatal. ---
if ( ! function_exists( 'add_action' ) ) {
	function add_action( ...$args ) {}
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( ...$args ) {}
}
if ( ! function_exists( 'do_action' ) ) {
	function do_action( ...$args ) {}
}
if ( ! function_exists( 'apply_filters' ) ) {
	// Return the value unchanged (second arg), mirroring "no filters attached".
	function apply_filters( $tag, $value = null, ...$args ) {
		return $value;
	}
}

// --- i18n: return the string unchanged. ---
if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}
if ( ! function_exists( '_e' ) ) {
	function _e( $text, $domain = 'default' ) {
		echo $text; // phpcs:ignore
	}
}
if ( ! function_exists( '_x' ) ) {
	function _x( $text, $context, $domain = 'default' ) {
		return $text;
	}
}
if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = 'default' ) {
		return $text;
	}
}
if ( ! function_exists( 'esc_attr__' ) ) {
	function esc_attr__( $text, $domain = 'default' ) {
		return $text;
	}
}

// --- Escaping / sanitising: PASS-THROUGH stubs. We are not testing WordPress's
// escaping here (see note above); these only let the code run so we can assert
// on which branch a value flows through. ---
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return $text;
	}
}
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return $text;
	}
}
if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ) {
		return $url;
	}
}
if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return $url;
	}
}
if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return is_string( $str ) ? trim( $str ) : $str;
	}
}

// --- Options: backed by $GLOBALS['oaf_test_options'] so tests control state. ---
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $default_value = false ) {
		return array_key_exists( $name, $GLOBALS['oaf_test_options'] )
			? $GLOBALS['oaf_test_options'][ $name ]
			: $default_value;
	}
}
if ( ! function_exists( 'update_option' ) ) {
	function update_option( $name, $value ) {
		$GLOBALS['oaf_test_options'][ $name ] = $value;
		return true;
	}
}

// --- Capabilities: backed by $GLOBALS['oaf_test_caps']. ---
if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $cap ) {
		return ! empty( $GLOBALS['oaf_test_caps'][ $cap ] );
	}
}

if ( ! function_exists( 'get_template_directory' ) ) {
	function get_template_directory() {
		return OAF_THEME_DIR;
	}
}
if ( ! function_exists( 'get_template_directory_uri' ) ) {
	function get_template_directory_uri() {
		return 'https://example.test/wp-content/themes/oaf-wp-blocktheme';
	}
}

// --- Load the modules under test. people.php must precede avatars.php because
// avatars' initials SVG calls oaf_person_initials(). functions.php is
// intentionally NOT loaded (it resolves paths and gates admin-only requires). ---
require_once OAF_THEME_DIR . '/inc/options.php';
require_once OAF_THEME_DIR . '/inc/contact-form.php';
require_once OAF_THEME_DIR . '/inc/stats.php';
require_once OAF_THEME_DIR . '/inc/people.php';
require_once OAF_THEME_DIR . '/inc/avatars.php';
