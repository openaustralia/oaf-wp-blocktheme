<?php
/**
 * The required pages: every page the theme expects a site to have.
 *
 * One definition, read by the reconcile code, the admin screen, the template
 * fallback, the contact-form script and (through `wp eval`) the dev seed.
 * Loaded on every request, so it holds data and pure lookups only; the code that
 * writes pages lives in inc/reconcile-pages.php and loads in admin and WP-CLI.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_required_pages' ) ) {
	/**
	 * The minimum set of pages the theme expects, keyed by slug.
	 *
	 * `pattern` is the block pattern inserted as the page content, or null for
	 * pages whose body is supplied by a template (home, blog). `template` is the
	 * custom page template to assign (via the `_wp_page_template` meta); the
	 * pattern-backed pages supply their own hero, so they use the `page-no-title`
	 * canvas template to avoid the title hero that `page.html` adds. `parent`, when
	 * set, is the slug of another required page to nest this one under; it is
	 * applied as `post_parent` when the page is first created.
	 *
	 * @return array<string,array{title:string,pattern:?string,template:?string,parent?:string}>
	 */
	function oaf_required_pages() {
		return array(
			'home'         => array(
				'title'    => __( 'Home', 'oaf-wp-blocktheme' ),
				'pattern'  => null,
				'template' => null,
			),
			'blog'         => array(
				'title'    => __( 'Blog', 'oaf-wp-blocktheme' ),
				'pattern'  => null,
				'template' => null,
			),
			'about'        => array(
				'title'    => __( 'About', 'oaf-wp-blocktheme' ),
				'pattern'  => 'oaf/page-about',
				'template' => 'page-no-title',
			),
			'collection'   => array(
				'title'    => __( 'Collection', 'oaf-wp-blocktheme' ),
				'pattern'  => 'oaf/page-collection',
				'template' => 'page-no-title',
			),
			'people'       => array(
				'title'    => __( 'People', 'oaf-wp-blocktheme' ),
				'pattern'  => 'oaf/page-people',
				'template' => 'page-no-title',
			),
			'contact'      => array(
				'title'    => __( 'Contact', 'oaf-wp-blocktheme' ),
				'pattern'  => 'oaf/page-contact',
				'template' => 'page-no-title',
			),
			'donate'       => array(
				'title'    => __( 'Donate', 'oaf-wp-blocktheme' ),
				'pattern'  => 'oaf/page-donate',
				'template' => 'page-no-title',
			),
			'alternatives' => array(
				'title'    => __( 'Other ways to give', 'oaf-wp-blocktheme' ),
				'pattern'  => 'oaf/page-donate-alternatives',
				'template' => 'page-no-title',
				'parent'   => 'donate',
			),
			'licence'      => array(
				'title'    => __( 'Licence', 'oaf-wp-blocktheme' ),
				'pattern'  => 'oaf/page-licence',
				'template' => 'page-no-title',
			),
		);
	}
}

if ( ! function_exists( 'oaf_recreatable_pages' ) ) {
	/**
	 * The pattern-backed pages that can be re-created from their pattern.
	 *
	 * Excludes home/blog, whose body comes from templates (null pattern) and so
	 * would gain no content from a re-create.
	 *
	 * @return array<string,string> slug => title.
	 */
	function oaf_recreatable_pages() {
		$out = array();
		foreach ( oaf_required_pages() as $slug => $page ) {
			if ( ! empty( $page['pattern'] ) ) {
				$out[ $slug ] = $page['title'];
			}
		}
		return $out;
	}
}

if ( ! function_exists( 'oaf_pattern_page_for_content' ) ) {
	/**
	 * The pattern page whose pattern a page's content references, if any.
	 *
	 * Matches the pattern reference "Create required pages" writes
	 * (`<!-- wp:pattern {"slug":"oaf/page-donate"} /-->`), wherever `slug` sits in
	 * the block attributes. Content that merely mentions a slug, or references a
	 * pattern that is not registered, does not match.
	 *
	 * @param string $content Raw post content.
	 * @return string|null The required page key (e.g. 'donate'), or null.
	 */
	function oaf_pattern_page_for_content( $content ) {
		static $by_pattern = null;

		if ( ! preg_match_all( '/<!-- wp:pattern (\{.*?\}) \/-->/s', (string) $content, $matches ) ) {
			return null;
		}

		if ( null === $by_pattern ) {
			$by_pattern = array();
			foreach ( oaf_required_pages() as $key => $page ) {
				if ( ! empty( $page['pattern'] ) ) {
					$by_pattern[ $page['pattern'] ] = $key;
				}
			}
		}

		foreach ( $matches[1] as $json ) {
			$attrs = json_decode( $json, true );
			if ( is_array( $attrs ) && isset( $attrs['slug'] ) && isset( $by_pattern[ $attrs['slug'] ] ) ) {
				return $by_pattern[ $attrs['slug'] ];
			}
		}

		return null;
	}
}
