<?php
/**
 * Tests for inc/required-pages.php - the one definition of the required pages.
 *
 * The lookup tests cover which content counts as a pattern page. The guard tests
 * keep the definition honest against the files it describes (patterns/,
 * theme.json, the dev seed script), so it cannot drift from them unnoticed.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RequiredPagesTest extends TestCase {

	public function test_content_referencing_a_registered_pattern_is_that_pattern_page(): void {
		$this->assertSame( 'donate', oaf_pattern_page_for_content( '<!-- wp:pattern {"slug":"oaf/page-donate"} /-->' ) );
		$this->assertSame( 'alternatives', oaf_pattern_page_for_content( '<!-- wp:pattern {"slug":"oaf/page-donate-alternatives"} /-->' ) );
	}

	public function test_slug_need_not_be_the_first_attribute(): void {
		$this->assertSame( 'contact', oaf_pattern_page_for_content( '<!-- wp:pattern {"className":"x","slug":"oaf/page-contact"} /-->' ) );
	}

	public function test_nested_attributes_do_not_hide_the_slug(): void {
		$content = '<!-- wp:pattern {"slug":"oaf/page-donate","metadata":{"name":"Donate form"}} /-->';
		$this->assertSame( 'donate', oaf_pattern_page_for_content( $content ) );
	}

	public function test_a_registered_pattern_among_other_blocks_is_found(): void {
		$content = "<!-- wp:paragraph --><p>Hi</p><!-- /wp:paragraph -->\n<!-- wp:pattern {\"slug\":\"oaf/page-about\"} /-->";
		$this->assertSame( 'about', oaf_pattern_page_for_content( $content ) );
	}

	public function test_unregistered_pattern_is_not_a_pattern_page(): void {
		$this->assertNull( oaf_pattern_page_for_content( '<!-- wp:pattern {"slug":"twentytwentyfive/hero"} /-->' ) );
		$this->assertNull( oaf_pattern_page_for_content( '<!-- wp:pattern {"slug":"oaf/page-nope"} /-->' ) );
	}

	public function test_content_that_only_mentions_a_slug_is_not_a_pattern_page(): void {
		$this->assertNull( oaf_pattern_page_for_content( '<p>See oaf/page-donate and the oaf-hero band.</p>' ) );
		$this->assertNull( oaf_pattern_page_for_content( '<section class="alignfull oaf-pagehero"></section>' ) );
		$this->assertNull( oaf_pattern_page_for_content( '' ) );
	}

	public function test_home_and_blog_are_required_but_not_pattern_pages(): void {
		$this->assertArrayHasKey( 'home', oaf_required_pages() );
		$this->assertArrayHasKey( 'blog', oaf_required_pages() );
		$this->assertArrayNotHasKey( 'home', oaf_recreatable_pages() );
		$this->assertArrayNotHasKey( 'blog', oaf_recreatable_pages() );
	}

	#[DataProvider( 'pattern_pages' )]
	public function test_every_pattern_has_a_matching_pattern_file( string $key, string $pattern ): void {
		$file = OAF_THEME_DIR . '/patterns/' . substr( $pattern, strlen( 'oaf/' ) ) . '.php';

		$this->assertFileExists( $file, "Pattern page '$key' names $pattern but its file is missing." );
		$this->assertMatchesRegularExpression(
			'/^ \* Slug: ' . preg_quote( $pattern, '/' ) . '$/m',
			(string) file_get_contents( $file ),
			"$file does not declare Slug: $pattern."
		);
	}

	#[DataProvider( 'pattern_pages' )]
	public function test_every_template_is_declared_in_theme_json( string $key ): void {
		$theme_json = json_decode( (string) file_get_contents( OAF_THEME_DIR . '/theme.json' ), true );
		$declared   = array_column( $theme_json['customTemplates'], 'name' );
		$template   = oaf_required_pages()[ $key ]['template'];

		$this->assertContains( $template, $declared, "Pattern page '$key' uses template '$template', which theme.json does not declare." );
	}

	public function test_every_pattern_page_names_a_registered_parent_or_none(): void {
		foreach ( oaf_required_pages() as $key => $page ) {
			if ( isset( $page['parent'] ) ) {
				$this->assertArrayHasKey( $page['parent'], oaf_required_pages(), "Parent of '$key' is not a required page." );
			}
		}
	}

	public function test_every_pattern_file_is_registered(): void {
		$registered = array_column( oaf_required_pages(), 'pattern' );
		foreach ( glob( OAF_THEME_DIR . '/patterns/page-*.php' ) as $file ) {
			$slug = 'oaf/' . basename( $file, '.php' );
			$this->assertContains( $slug, $registered, "$slug has a pattern file but is not a required page." );
		}
	}

	public function test_dev_seed_script_keeps_no_page_list(): void {
		$seed = (string) file_get_contents( OAF_THEME_DIR . '/.wp-setup.sh' );

		$this->assertStringNotContainsString( 'oaf/page-', $seed, '.wp-setup.sh must create pages through oaf_reconcile_required_pages().' );
		$this->assertStringNotContainsString( 'page-no-title', $seed );
		$this->assertStringContainsString( 'oaf_reconcile_required_pages', $seed );
	}

	/**
	 * @return array<string,array{string,string}>
	 */
	public static function pattern_pages(): array {
		$out = array();
		foreach ( oaf_required_pages() as $key => $page ) {
			if ( ! empty( $page['pattern'] ) ) {
				$out[ $key ] = array( $key, $page['pattern'] );
			}
		}
		return $out;
	}
}
