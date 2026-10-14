<?php
/**
 * Tests for inc/templates.php - which pages fall back to their own template.
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

	public function test_slug_not_first_in_the_reference_still_falls_back(): void {
		$content = '<!-- wp:pattern {"className":"x","slug":"oaf/page-donate"} /-->';
		$this->assertSame( 'page-no-title', oaf_legacy_template_fallback( 'template-page-fullwidth.php', $content, self::KNOWN ) );
	}

	public function test_detached_pattern_markup_is_left_alone(): void {
		$content = '<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color">';
		$this->assertSame( '', oaf_legacy_template_fallback( 'template-page-fullwidth.php', $content, self::KNOWN ) );
	}

	public function test_unregistered_pattern_is_left_alone(): void {
		$content = '<!-- wp:pattern {"slug":"twentytwentyfive/hero"} /-->';
		$this->assertSame( '', oaf_legacy_template_fallback( 'template-page-fullwidth.php', $content, self::KNOWN ) );
	}
}
