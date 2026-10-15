<?php
/**
 * PHPUnit bootstrap for the integration suite.
 *
 * Loads real WordPress from the wp-env *test* site and activates the theme, so
 * tests exercise wp_insert_post(), wp_update_post(), the template hierarchy and
 * so on rather than stubs. It is a plain PHPUnit run (no WP_UnitTestCase):
 * WordPress's bundled test library does not support PHPUnit 11.
 *
 * These tests delete pages. They refuse to run against anything but the wp-env
 * test database, so they can never wipe a development or production site.
 *
 * @package oaf-wp-blocktheme
 */

define( 'OAF_THEME_DIR', dirname( __DIR__, 2 ) );

$oaf_wp_load = getenv( 'OAF_WP_LOAD' ) ? getenv( 'OAF_WP_LOAD' ) : '/var/www/html/wp-load.php';
if ( ! is_readable( $oaf_wp_load ) ) {
	fwrite( STDERR, "WordPress not found at $oaf_wp_load.\nRun this suite inside the wp-env test container: composer run test:integration\n" );
	exit( 1 );
}

// These tests delete pages: check which database this WordPress will use before
// booting it, not after (booting can run plugins and upgrades). wp-env's
// wp-config.php reads the database name from this variable.
if ( 'tests-wordpress' !== getenv( 'WORDPRESS_DB_NAME' ) ) {
	fwrite( STDERR, "Refusing to run: WORDPRESS_DB_NAME is not the wp-env test database (tests-wordpress).\n" );
	exit( 1 );
}

$_SERVER['HTTP_HOST'] = 'localhost:8889';
require $oaf_wp_load;

if ( ! defined( 'DB_NAME' ) || 'tests-wordpress' !== DB_NAME ) {
	fwrite( STDERR, "Refusing to run: DB_NAME is not the wp-env test database (tests-wordpress).\n" );
	exit( 1 );
}

// The theme must already be active when WordPress boots, so that functions.php
// and its hooks load. `composer run test:integration` activates it first.
if ( get_stylesheet() !== basename( OAF_THEME_DIR ) ) {
	fwrite( STDERR, 'The theme is not active on the test site. Run: wp-env run tests-cli wp theme activate ' . basename( OAF_THEME_DIR ) . "\n" );
	exit( 1 );
}

require_once OAF_THEME_DIR . '/inc/reconcile-pages.php';
require_once __DIR__ . '/IntegrationTestCase.php';
