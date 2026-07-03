<?php
/**
 * Run the official Theme Check plugin against this theme and exit non-zero if
 * any REQUIRED issues are found.
 *
 * Shared by CI (.github/workflows/php.yml) and the local `mise run theme-check`
 * task so both run identical checks. Must be executed through WP-CLI, e.g.
 * `wp eval-file bin/theme-check.php`, so WordPress and the plugin are loaded.
 *
 * Drives the plugin's run_themechecks() API directly rather than its admin
 * screen, and applies the exclude list here: locally the whole repo is mounted
 * as the theme (vendor/, node_modules/, this bin/ dir and all), so the scan
 * must skip non-theme files itself rather than relying on a pre-filtered copy.
 */

require_once WP_PLUGIN_DIR . '/theme-check/checkbase.php';
require_once WP_PLUGIN_DIR . '/theme-check/main.php';

$slug  = 'oaf-wp-blocktheme';
$theme = wp_get_theme( $slug );
$dir   = $theme->get_stylesheet_directory();

// Paths that are in the repo but NOT in the shipped theme. The source of truth
// is .gitattributes (export-ignore): CI scans a `git archive` build, so these
// are already absent there. Locally, wp-env mounts the whole repo, so this list
// must mirror .gitattributes to skip the same dev-only files/dirs by hand.
$skip_dirs  = array( '.git', '.github', 'vendor', 'node_modules', '.playwright-mcp', '.wp-core', 'bin' );
$skip_files = array( '.gitignore', '.gitattributes', '.gitkeep', '.wp-env.json', '.wp-setup.sh', 'phpcs.xml.dist', 'composer.json', 'composer.lock' );

// Gitignored artifact file types that a `git archive`/checkout never contains.
$skip_ext = array( 'zip', 'log' );

$php = $css = $other = array();
$filter = new RecursiveCallbackFilterIterator(
	new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
	function ( $current ) use ( $skip_dirs, $skip_files, $skip_ext ) {
		if ( $current->isDir() ) {
			return ! in_array( $current->getFilename(), $skip_dirs, true );
		}
		if ( in_array( $current->getFilename(), $skip_files, true ) ) {
			return false;
		}
		return ! in_array( strtolower( $current->getExtension() ), $skip_ext, true );
	}
);
$it = new RecursiveIteratorIterator( $filter );
foreach ( $it as $file ) {
	$path = $file->getPathname();
	switch ( strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
		case 'php': $php[ $path ] = file_get_contents( $path ); break;
		case 'css': $css[ $path ] = file_get_contents( $path ); break;
		default:    $other[ $path ] = ''; break;
	}
}

run_themechecks( $php, $css, $other, array( 'theme' => $theme, 'slug' => $slug ) );

// Accepted deviations: REQUIRED issues that are intentional for this bespoke,
// single-site theme (never distributed via WordPress.org). Each signature must
// name BOTH the flagged function and its file so a genuinely new register_*()
// call elsewhere still trips the gate. The custom post type, taxonomy and blocks
// are core theme functionality (people grid, patterns, avatars) rather than the
// "plugin territory" the .org guideline assumes.
$accepted_signatures = array(
	array( 'register_post_type', 'inc/people.php' ),
	array( 'register_taxonomy', 'inc/people.php' ),
	array( 'register_block_type', 'functions.php' ),
);

global $themechecks;
$required = 0;
$accepted = 0;
foreach ( (array) $themechecks as $check ) {
	if ( ! is_object( $check ) || ! method_exists( $check, 'getError' ) ) { continue; }
	foreach ( (array) $check->getError() as $e ) {
		$e = trim( html_entity_decode( wp_strip_all_tags( $e ) ) );
		if ( '' === $e ) { continue; }
		echo $e, "\n";
		if ( false === stripos( $e, 'REQUIRED' ) ) { continue; }
		$is_accepted = false;
		foreach ( $accepted_signatures as $sig ) {
			if ( false !== stripos( $e, $sig[0] ) && false !== stripos( $e, $sig[1] ) ) {
				$is_accepted = true;
				break;
			}
		}
		if ( $is_accepted ) { $accepted++; } else { $required++; }
	}
}
echo "\n== {$required} REQUIRED issue(s) ==\n";
echo "== {$accepted} accepted deviation(s) ==\n";
if ( $required > 0 ) { exit( 1 ); }
