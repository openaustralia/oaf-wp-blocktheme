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

// Directories that are never part of the shipped theme (mirror the CI rsync
// excludes), plus this bin/ dir which only exists to run this check.
$skip_dirs = array( '.git', '.github', 'vendor', 'node_modules', '.playwright-mcp', '.wp-core', 'bin' );

// Gitignored artifact file types that CI's checkout never sees. Tracked
// dotfiles (.gitignore, .wp-env.json, .wp-setup.sh) are deliberately NOT
// skipped: they ship, so Theme Check flags them in CI and must here too.
$skip_ext = array( 'zip', 'log' );

$php = $css = $other = array();
$filter = new RecursiveCallbackFilterIterator(
	new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
	function ( $current ) use ( $skip_dirs, $skip_ext ) {
		if ( $current->isDir() ) {
			return ! in_array( $current->getFilename(), $skip_dirs, true );
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

global $themechecks;
$required = 0;
foreach ( (array) $themechecks as $check ) {
	if ( ! is_object( $check ) || ! method_exists( $check, 'getError' ) ) { continue; }
	foreach ( (array) $check->getError() as $e ) {
		$e = trim( html_entity_decode( wp_strip_all_tags( $e ) ) );
		if ( '' === $e ) { continue; }
		echo $e, "\n";
		if ( false !== stripos( $e, 'REQUIRED' ) ) { $required++; }
	}
}
echo "\n== {$required} REQUIRED issue(s) ==\n";
if ( $required > 0 ) { exit( 1 ); }
