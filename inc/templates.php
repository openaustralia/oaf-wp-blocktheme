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
 * phone. This renders such pages with their own template (page-no-title)
 * instead, without touching the stored meta.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_stale_page_template' ) ) {
	/**
	 * The stored template of a page when the theme does not provide it.
	 *
	 * Pages imported from the old classic theme carry values such as
	 * `template-page-fullwidth.php`, which WordPress rejects when the page is saved.
	 * Used both here, to render around it, and by the reconcile code, to repair it.
	 *
	 * @param WP_Post $post A page.
	 * @return string The stale template slug, or '' when there is none.
	 */
	function oaf_stale_page_template( $post ) {
		$stored = (string) get_page_template_slug( $post );
		if ( '' === $stored || 'default' === $stored ) {
			return '';
		}

		return array_key_exists( $stored, wp_get_theme()->get_page_templates( $post ) ) ? '' : $stored;
	}
}

if ( ! function_exists( 'oaf_pattern_page_template' ) ) {
	/**
	 * The template a pattern page is meant to render with.
	 *
	 * @param string $content Raw post content.
	 * @return string The pattern page's own template, or '' when the content is not a
	 *                pattern page (or its page has no template).
	 */
	function oaf_pattern_page_template( $content ) {
		$key = oaf_pattern_page_for_content( $content );
		if ( null === $key ) {
			return '';
		}

		$template = oaf_required_pages()[ $key ]['template'];

		return $template ? $template : '';
	}
}

if ( ! function_exists( 'oaf_legacy_page_template_hierarchy' ) ) {
	/**
	 * Swap a stale template for the pattern page's own template.
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

		$stale = oaf_stale_page_template( $post );
		if ( '' === $stale ) {
			return $templates;
		}

		$fallback = oaf_pattern_page_template( $post->post_content );
		if ( '' === $fallback ) {
			return $templates;
		}

		// Take the stale slug's place, so the fallback ranks as an assigned
		// template would: ahead of page-{slug} and page-{id}.
		$index = array_search( $stale, $templates, true );
		if ( false === $index ) {
			array_unshift( $templates, $fallback );
		} else {
			$templates[ $index ] = $fallback;
		}

		return $templates;
	}
}
add_filter( 'page_template_hierarchy', 'oaf_legacy_page_template_hierarchy' );
