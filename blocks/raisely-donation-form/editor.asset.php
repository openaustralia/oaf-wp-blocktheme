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
	'version'      => '1.0.0',
);
