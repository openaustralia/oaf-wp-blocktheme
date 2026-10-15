<?php
/**
 * Integration tests for deciding that a page's stored template is stale: it is
 * not one the active theme provides. Shared by the render-time fallback and the
 * reconcile repair, so it is tested once, on real WordPress.
 *
 * @package oaf-wp-blocktheme
 */

final class StalePageTemplateTest extends IntegrationTestCase {

	public function test_a_template_the_theme_does_not_provide_is_stale(): void {
		$id = $this->make_page( array(), 'template-page-fullwidth.php' );

		$this->assertSame( 'template-page-fullwidth.php', oaf_stale_page_template( get_post( $id ) ) );
	}

	public function test_a_template_the_theme_provides_is_not_stale(): void {
		$id = $this->make_page( array(), 'page-no-title' );

		$this->assertSame( '', oaf_stale_page_template( get_post( $id ) ) );
	}

	public function test_no_template_is_not_stale(): void {
		$id = $this->make_page( array() );

		$this->assertSame( '', oaf_stale_page_template( get_post( $id ) ) );
	}

	public function test_the_default_template_is_not_stale(): void {
		$id = $this->make_page( array(), 'default' );

		$this->assertSame( '', oaf_stale_page_template( get_post( $id ) ) );
	}
}
