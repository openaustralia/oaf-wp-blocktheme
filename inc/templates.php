<?php
/**
 * Fallback for pages still assigned a template this theme does not have.
 *
 * Pages imported from the old classic theme carry `_wp_page_template` values
 * such as `template-page-fullwidth.php`. A block theme cannot find that
 * template, so WordPress falls through to page.html, which wraps the content in
 * its own title hero and padded white band. For a pattern page (which brings
 * its own hero and full-width bands) that means a duplicate h1 and compounded
 * padding - on the Donate page it squeezed the Raisely form to ~107px on a
 * phone. This renders such pages with page-no-title instead, without touching
 * the stored meta.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_page_supplies_own_hero' ) ) {
	/**
	 * Whether page content brings its own hero, as the oaf/page-* patterns do.
	 *
	 * Matches a pattern reference (how "Create required pages" stores them) as
	 * well as the expanded pattern markup, in case a page was saved after its
	 * pattern was detached.
	 *
	 * @param string $content Raw post content.
	 * @return bool
	 */
	function oaf_page_supplies_own_hero( $content ) {
		return 1 === preg_match(
			'/<!-- wp:pattern \{[^}]*"slug":"oaf\/page-|class="[^"]*\boaf-(?:page)?hero\b/',
			(string) $content
		);
	}
}

if ( ! function_exists( 'oaf_legacy_template_fallback' ) ) {
	/**
	 * The template to render a page with instead of its assigned one, if any.
	 *
	 * @param string   $assigned Assigned template slug ('' or 'default' for none).
	 * @param string   $content  Raw post content.
	 * @param string[] $known    Template slugs this theme (or the Site Editor) provides.
	 * @return string 'page-no-title', or '' to leave the hierarchy alone.
	 */
	function oaf_legacy_template_fallback( $assigned, $content, $known ) {
		if ( '' === $assigned || 'default' === $assigned || in_array( $assigned, $known, true ) ) {
			return '';
		}

		return oaf_page_supplies_own_hero( $content ) ? 'page-no-title' : '';
	}
}

if ( ! function_exists( 'oaf_legacy_page_template_hierarchy' ) ) {
	/**
	 * Swap an unknown assigned template for page-no-title on pattern pages.
	 *
	 * @param string[] $templates Template candidates, highest priority first.
	 * @return string[]
	 */
	function oaf_legacy_page_template_hierarchy( $templates ) {
		if ( ! is_page() ) {
			return $templates;
		}

		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return $templates;
		}

		$assigned = (string) get_page_template_slug( $post );
		if ( '' === $assigned || 'default' === $assigned ) {
			return $templates;
		}

		$known    = array_keys( wp_get_theme()->get_page_templates( $post ) );
		$fallback = oaf_legacy_template_fallback( $assigned, $post->post_content, $known );
		if ( '' === $fallback ) {
			return $templates;
		}

		// Take the stale slug's place, so the fallback ranks as an assigned
		// template would: ahead of page-{slug} and page-{id}.
		$index = array_search( $assigned, $templates, true );
		if ( false === $index ) {
			array_unshift( $templates, $fallback );
		} else {
			$templates[ $index ] = $fallback;
		}

		return $templates;
	}
}
add_filter( 'page_template_hierarchy', 'oaf_legacy_page_template_hierarchy' );
