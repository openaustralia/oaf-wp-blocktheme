<?php
/**
 * Live statistics, sourced from the wp-alaveteli-stats plugin when present.
 *
 * The plugin fetches and caches Alaveteli (Right to Know) figures and exposes
 * them through Alaveteli_Stats_Render::stat(), which is its own shared output
 * path. The theme reuses that path so the markup and number formatting match,
 * and falls back to the hard-coded copy when the plugin is not active.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_stat' ) ) {
	/**
	 * Render a single statistic, preferring the live plugin value.
	 *
	 * When the plugin is active its stat() handles every not-ready case
	 * (unconfigured, no cached data, unknown key) by returning the fallback,
	 * so the only thing the theme guards against is the plugin being absent.
	 * Both branches return an escaped HTML string ready to echo.
	 *
	 * @param string $key      Plugin statistic key, e.g. visible_request_count.
	 * @param string $fallback Hard-coded text shown when no live value exists.
	 * @return string Escaped HTML.
	 */
	function oaf_stat( $key, $fallback ) {
		if ( class_exists( 'Alaveteli_Stats_Render' ) ) {
			return Alaveteli_Stats_Render::stat( $key, array( 'fallback' => $fallback ) );
		}

		return esc_html( $fallback );
	}
}
