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

// Local avatars (People images instead of Gravatar) for bylines and comments.
require_once get_template_directory() . '/inc/avatars.php';

// Live statistics (wp-alaveteli-stats plugin when present), used by patterns.
require_once get_template_directory() . '/inc/stats.php';

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
		load_theme_textdomain( 'oaf-wp-blocktheme', get_template_directory() . '/languages' );

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

// Fonts are self-hosted: the Fira Sans and Merriweather woff2 files in
// assets/fonts/ are registered via theme.json `fontFace`, which loads them on
// both the front end and in the editor. No webfont enqueue or preconnect here.

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
