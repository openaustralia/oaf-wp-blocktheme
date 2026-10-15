<?php
/**
 * Tests for inc/templates.php - which template a pattern page renders with.
 *
 * Whether a page's stored template is stale, and the hook that swaps it, need
 * WordPress: they are covered by the integration suite (TemplateHierarchyTest,
 * StalePageTemplateTest). This covers the decision that needs no WordPress.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

final class TemplatesTest extends TestCase {

	public function test_a_pattern_page_renders_with_its_own_template(): void {
		$this->assertSame( 'page-no-title', oaf_pattern_page_template( '<!-- wp:pattern {"slug":"oaf/page-donate"} /-->' ) );
	}

	public function test_slug_not_first_in_the_reference_still_counts(): void {
		$content = '<!-- wp:pattern {"className":"x","slug":"oaf/page-donate"} /-->';
		$this->assertSame( 'page-no-title', oaf_pattern_page_template( $content ) );
	}

	public function test_classic_content_has_no_pattern_template(): void {
		$this->assertSame( '', oaf_pattern_page_template( '<h2>Old page</h2><p>Text.</p>' ) );
	}

	public function test_detached_pattern_markup_has_no_pattern_template(): void {
		$content = '<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color">';
		$this->assertSame( '', oaf_pattern_page_template( $content ) );
	}

	public function test_an_unregistered_pattern_has_no_pattern_template(): void {
		$this->assertSame( '', oaf_pattern_page_template( '<!-- wp:pattern {"slug":"twentytwentyfive/hero"} /-->' ) );
	}
}
