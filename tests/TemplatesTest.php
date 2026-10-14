<?php
/**
 * Tests for inc/templates.php - which pages fall back to page-no-title.
 *
 * Only the decision is tested. The hierarchy filter itself reads the queried
 * page through WordPress and is checked end to end in a browser.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

final class TemplatesTest extends TestCase {

	private const KNOWN = array( 'page-no-title' );

	private const PATTERN_REF = '<!-- wp:pattern {"slug":"oaf/page-donate"} /-->';

	public function test_legacy_template_on_pattern_page_falls_back(): void {
		$this->assertSame(
			'page-no-title',
			oaf_legacy_template_fallback( 'template-page-fullwidth.php', self::PATTERN_REF, self::KNOWN )
		);
	}

	public function test_legacy_template_on_expanded_pattern_markup_falls_back(): void {
		$content = '<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color">';
		$this->assertSame( 'page-no-title', oaf_legacy_template_fallback( 'template-page-fullwidth.php', $content, self::KNOWN ) );
	}

	public function test_legacy_template_on_classic_content_is_left_alone(): void {
		$this->assertSame( '', oaf_legacy_template_fallback( 'template-page-fullwidth.php', '<h2>Old page</h2><p>Text.</p>', self::KNOWN ) );
	}

	public function test_known_template_is_left_alone(): void {
		$this->assertSame( '', oaf_legacy_template_fallback( 'page-no-title', self::PATTERN_REF, self::KNOWN ) );
	}

	public function test_no_assigned_template_is_left_alone(): void {
		$this->assertSame( '', oaf_legacy_template_fallback( '', self::PATTERN_REF, self::KNOWN ) );
		$this->assertSame( '', oaf_legacy_template_fallback( 'default', self::PATTERN_REF, self::KNOWN ) );
	}

	public function test_pattern_reference_with_slug_not_first_is_recognised(): void {
		$this->assertTrue( oaf_page_supplies_own_hero( '<!-- wp:pattern {"className":"x","slug":"oaf/page-donate"} /-->' ) );
	}

	public function test_hero_class_named_in_prose_is_not_treated_as_a_hero(): void {
		$this->assertFalse( oaf_page_supplies_own_hero( '<p>The oaf-hero band sits at the top of the home page.</p>' ) );
	}

	public function test_non_oaf_pattern_is_not_treated_as_a_hero(): void {
		$this->assertFalse( oaf_page_supplies_own_hero( '<!-- wp:pattern {"slug":"twentytwentyfive/hero"} /-->' ) );
	}
}
