<?php
/**
 * Hand-written asset manifest for editor.js (no build step).
 *
 * Declares the global `wp` packages the editor script relies on so WordPress
 * enqueues them as dependencies.
 *
 * @package oaf-wp-blocktheme
 */

return array(
	'dependencies' => array(
		'wp-blocks',
		'wp-element',
		'wp-block-editor',
		'wp-server-side-render',
		'wp-i18n',
	),
	// Derive the cache-busting version from editor.js's mtime so a change to the
	// script (or a fresh Git Updater deploy) invalidates the browser cache.
	'version'      => file_exists( __DIR__ . '/editor.js' ) ? (string) filemtime( __DIR__ . '/editor.js' ) : '1.1.0',
);
