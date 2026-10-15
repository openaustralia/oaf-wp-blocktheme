<?php
/**
 * Integration tests for writing the required pages, on real WordPress.
 *
 * The unit suite cannot reach this code: it is the wp_insert_post() and
 * wp_update_post() calls that failed in production (a stale _wp_page_template
 * made WordPress reject the save).
 *
 * @package oaf-wp-blocktheme
 */

final class RequiredPageWritesTest extends IntegrationTestCase {

	private const STALE = 'template-page-fullwidth.php';

	public function test_a_missing_pattern_page_is_created_with_its_pattern_and_template(): void {
		$result = oaf_write_page( 'donate', false );

		$this->assertSame( 'created', $result['status'] );
		$page = get_post( $result['id'] );
		$this->assertSame( 'publish', $page->post_status );
		$this->assertSame( 'donate', $page->post_name );
		$this->assertSame( '<!-- wp:pattern {"slug":"oaf/page-donate"} /-->', $page->post_content );
		$this->assertSame( 'page-no-title', get_page_template_slug( $page ) );
	}

	public function test_home_is_created_empty_with_no_template(): void {
		$result = oaf_write_page( 'home', false );

		$this->assertSame( 'created', $result['status'] );
		$this->assertSame( '', get_post( $result['id'] )->post_content );
		$this->assertSame( '', (string) get_page_template_slug( $result['id'] ) );
	}

	public function test_other_ways_to_give_nests_under_an_existing_donate_page(): void {
		$donate = oaf_write_page( 'donate', false );
		$child  = oaf_write_page( 'alternatives', false );

		$this->assertSame( 'created', $child['status'] );
		$this->assertSame( $donate['id'], (int) get_post( $child['id'] )->post_parent );
	}

	public function test_an_existing_page_is_left_alone_without_overwrite(): void {
		$id = $this->make_page(
			array(
				'post_name'    => 'about',
				'post_title'   => 'About us',
				'post_content' => 'Edited by hand',
			)
		);

		$result = oaf_write_page( 'about', false );

		$this->assertSame( 'skipped', $result['status'] );
		$this->assertSame( $id, $result['id'] );
		$this->assertSame( 'Edited by hand', get_post( $id )->post_content );
	}

	public function test_overwrite_replaces_content_and_keeps_title_slug_and_status(): void {
		$id = $this->make_page(
			array(
				'post_name'   => 'about',
				'post_title'  => 'About us',
				'post_status' => 'draft',
			),
			'page-no-title'
		);

		$result = oaf_write_page( 'about', true );

		$this->assertSame( 'overwritten', $result['status'] );
		$page = get_post( $id );
		$this->assertSame( '<!-- wp:pattern {"slug":"oaf/page-about"} /-->', $page->post_content );
		$this->assertSame( 'About us', $page->post_title );
		$this->assertSame( 'about', $page->post_name );
		$this->assertSame( 'draft', $page->post_status );
	}

	public function test_overwriting_a_page_with_a_stale_template_repairs_the_template(): void {
		// The production failure: legacy Donate carries the classic theme's template.
		$id = $this->make_page( array( 'post_name' => 'donate' ), self::STALE );
		$this->assertSame( self::STALE, get_page_template_slug( $id ) );

		$result = oaf_write_page( 'donate', true );

		$this->assertSame( 'overwritten', $result['status'], 'WordPress rejected the stale template.' );
		$this->assertSame( 'page-no-title', get_page_template_slug( $id ) );
	}

	public function test_a_trashed_page_is_reported_and_not_duplicated(): void {
		$id = $this->make_page( array( 'post_name' => 'people' ) );
		wp_trash_post( $id );

		$result = oaf_write_page( 'people', true );

		$this->assertSame( 'trashed', $result['status'] );
		$this->assertSame( $id, $result['id'] );
		$this->assertCount( 1, get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'trash' ), 'numberposts' => -1, 'fields' => 'ids' ) ) );
	}

	public function test_creating_all_required_pages_points_the_site_at_home_and_blog(): void {
		$result = oaf_create_required_pages( true );

		$this->assertSame( array_keys( oaf_required_pages() ), $result['created'] );
		$this->assertSame( array(), $result['errors'] );
		$home = get_page_by_path( 'home' );
		$blog = get_page_by_path( 'blog' );
		$this->assertSame( 'page', get_option( 'show_on_front' ) );
		$this->assertSame( $home->ID, (int) get_option( 'page_on_front' ) );
		$this->assertSame( $blog->ID, (int) get_option( 'page_for_posts' ) );
	}

	public function test_creating_again_skips_every_existing_page(): void {
		oaf_create_required_pages( false );

		$again = oaf_create_required_pages( false );

		$this->assertSame( array(), $again['created'] );
		$this->assertSame( array_keys( oaf_required_pages() ), $again['skipped'] );
	}
}
