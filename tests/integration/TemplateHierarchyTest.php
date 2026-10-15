<?php
/**
 * Integration tests for the stale-template fallback (inc/templates.php), through
 * the real `page_template_hierarchy` filter and a real queried page.
 *
 * The unit suite covers the decision; this covers the hook that applies it.
 *
 * @package oaf-wp-blocktheme
 */

final class TemplateHierarchyTest extends IntegrationTestCase {

	private const STALE = 'template-page-fullwidth.php';

	private const PATTERN = '<!-- wp:pattern {"slug":"oaf/page-donate"} /-->';

	protected function tearDown(): void {
		wp_reset_query();
		parent::tearDown();
	}

	/**
	 * The block template WordPress picks for the page, resolved the way a real
	 * page request does it (get_page_template() -> locate_block_template()).
	 */
	private function resolved_template_for( int $id ): string {
		$query                   = new WP_Query( array( 'page_id' => $id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query']     = $query;
		$GLOBALS['wp_the_query'] = $query;
		$query->the_post();

		unset( $GLOBALS['_wp_current_template_id'] );
		get_page_template();

		return (string) $GLOBALS['_wp_current_template_id'];
	}

	public function test_a_pattern_page_with_a_stale_template_resolves_to_its_own_template(): void {
		$id = $this->make_page( array( 'post_name' => 'donate', 'post_content' => self::PATTERN ), self::STALE );

		$this->assertSame( get_stylesheet() . '//page-no-title', $this->resolved_template_for( $id ) );
		$this->assertSame( self::STALE, get_page_template_slug( $id ), 'The stored template must be left untouched.' );
	}

	public function test_a_classic_page_with_a_stale_template_falls_through_to_the_page_template(): void {
		$id = $this->make_page( array( 'post_name' => 'old-page', 'post_content' => '<p>Classic content</p>' ), self::STALE );

		$this->assertSame( get_stylesheet() . '//page', $this->resolved_template_for( $id ) );
	}

	public function test_a_pattern_page_with_a_known_template_keeps_it(): void {
		$id = $this->make_page( array( 'post_name' => 'donate', 'post_content' => self::PATTERN ), 'page-no-title' );

		$this->assertSame( get_stylesheet() . '//page-no-title', $this->resolved_template_for( $id ) );
	}

	public function test_a_pattern_page_with_no_template_uses_the_default_page_template(): void {
		$id = $this->make_page( array( 'post_name' => 'donate', 'post_content' => self::PATTERN ) );

		$this->assertSame( get_stylesheet() . '//page', $this->resolved_template_for( $id ) );
	}
}
