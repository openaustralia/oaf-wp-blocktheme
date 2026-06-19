<?php
/**
 * OpenAustralia Foundation block theme functions.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Editable theme options (used by patterns on the front end too).
require_once get_template_directory() . '/inc/options.php';

// People post type + taxonomy (registered on the front end and in admin).
require_once get_template_directory() . '/inc/people.php';

// Admin-only: required-page creation and the theme settings screen.
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/pages.php';
	require_once get_template_directory() . '/inc/admin.php';
}

if ( ! function_exists( 'oaf_setup' ) ) {
	/**
	 * Theme supports. Most FSE supports are implicit, these are the extras.
	 */
	function oaf_setup() {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'post-thumbnails' );

		// Load style.css into the Site Editor / block editor so it matches the front end.
		add_editor_style( 'style.css' );
	}
}
add_action( 'after_setup_theme', 'oaf_setup' );

if ( ! function_exists( 'oaf_enqueue_assets' ) ) {
	/**
	 * Enqueue the theme stylesheet on the front end.
	 */
	function oaf_enqueue_assets() {
		wp_enqueue_style(
			'oaf-style',
			get_stylesheet_uri(),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'oaf_enqueue_assets' );

if ( ! function_exists( 'oaf_fonts_url' ) ) {
	/**
	 * Google Fonts stylesheet: Fira Sans (body) and Merriweather (serif), in the
	 * weights and styles the design uses. Headings use a system Helvetica stack,
	 * so no webfont is requested for them.
	 */
	function oaf_fonts_url() {
		return 'https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Merriweather:ital,wght@0,400;1,400&display=swap';
	}
}

if ( ! function_exists( 'oaf_enqueue_fonts' ) ) {
	/**
	 * Load the fonts from Google's CDN. enqueue_block_assets fires on both the
	 * front end and in the block/site editor, so the type matches in both.
	 */
	function oaf_enqueue_fonts() {
		wp_enqueue_style( 'oaf-fonts', oaf_fonts_url(), array(), null );
	}
}
add_action( 'enqueue_block_assets', 'oaf_enqueue_fonts' );

if ( ! function_exists( 'oaf_resource_hints' ) ) {
	/**
	 * Preconnect to the Google Fonts hosts so the webfont request starts sooner.
	 */
	function oaf_resource_hints( $hints, $relation_type ) {
		if ( 'preconnect' === $relation_type ) {
			$hints[] = 'https://fonts.googleapis.com';
			$hints[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}
		return $hints;
	}
}
add_filter( 'wp_resource_hints', 'oaf_resource_hints', 10, 2 );

if ( ! function_exists( 'oaf_register_block_assets' ) ) {
	/**
	 * Register the pattern category and custom button block styles used by the
	 * masthead, hero and donate band ("reverse" white-on-maroon and "ghost").
	 */
	function oaf_register_block_assets() {
		register_block_pattern_category(
			'oaf',
			array( 'label' => __( 'OpenAustralia Foundation', 'oaf-wp-blocktheme' ) )
		);

		register_block_style(
			'core/button',
			array(
				'name'  => 'oaf-rev',
				'label' => __( 'Reverse (white on maroon)', 'oaf-wp-blocktheme' ),
			)
		);
		register_block_style(
			'core/button',
			array(
				'name'  => 'oaf-ghost',
				'label' => __( 'Ghost (outline on maroon)', 'oaf-wp-blocktheme' ),
			)
		);
	}
}
add_action( 'init', 'oaf_register_block_assets' );

if ( ! function_exists( 'oaf_register_blocks' ) ) {
	/**
	 * Register theme blocks from their block.json metadata.
	 */
	function oaf_register_blocks() {
		register_block_type( get_template_directory() . '/blocks/raisely-donation-form' );
		register_block_type( get_template_directory() . '/blocks/people-grid' );
	}
}
add_action( 'init', 'oaf_register_blocks' );
