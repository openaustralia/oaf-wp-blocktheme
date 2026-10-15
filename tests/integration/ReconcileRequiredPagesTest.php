<?php
/**
 * Integration tests for reconciling the required pages, on real WordPress.
 *
 * The unit suite cannot reach this code: it is the wp_insert_post() and
 * wp_update_post() calls that failed in production (a stale _wp_page_template
 * made WordPress reject the save).
 *
 * @package oaf-wp-blocktheme
 */

final class ReconcileRequiredPagesTest extends IntegrationTestCase {

	private const STALE = 'template-page-fullwidth.php';

	private const ALL_STATUSES = array( 'publish', 'draft', 'trash' );

	/**
	 * Reconcile one page and return its result.
	 *
	 * @return array{status:string,id:int,error?:array{code:string,message:string}}
	 */
	private function reconcile_one( string $slug, string $mode ): array {
		$results = oaf_reconcile_required_pages( $mode, array( $slug ) );
		$this->assertSame( array( $slug ), array_keys( $results ) );

		return $results[ $slug ];
	}

	// ---- create mode: make what is missing, repair only a stale template ----

	public function test_a_missing_pattern_page_is_created_with_its_pattern_and_template(): void {
		$result = $this->reconcile_one( 'donate', 'create' );

		$this->assertSame( 'created', $result['status'] );
		$page = get_post( $result['id'] );
		$this->assertSame( 'publish', $page->post_status );
		$this->assertSame( 'donate', $page->post_name );
		$this->assertSame( '<!-- wp:pattern {"slug":"oaf/page-donate"} /-->', $page->post_content );
		$this->assertSame( 'page-no-title', get_page_template_slug( $page ) );
	}

	public function test_home_is_created_empty_with_no_template(): void {
		$result = $this->reconcile_one( 'home', 'create' );

		$this->assertSame( 'created', $result['status'] );
		$this->assertSame( '', get_post( $result['id'] )->post_content );
		$this->assertSame( '', (string) get_page_template_slug( $result['id'] ) );
	}

	public function test_other_ways_to_give_nests_under_an_existing_donate_page(): void {
		$donate = $this->reconcile_one( 'donate', 'create' );
		$child  = $this->reconcile_one( 'alternatives', 'create' );

		$this->assertSame( 'created', $child['status'] );
		$this->assertSame( $donate['id'], (int) get_post( $child['id'] )->post_parent );
	}

	public function test_an_existing_page_is_left_unchanged(): void {
		$id = $this->make_page(
			array(
				'post_name'    => 'about',
				'post_title'   => 'About us',
				'post_content' => 'Edited by hand',
			),
			'page-no-title'
		);
		$modified = get_post( $id )->post_modified_gmt;

		$result = $this->reconcile_one( 'about', 'create' );

		$this->assertSame( 'unchanged', $result['status'] );
		$this->assertSame( $id, $result['id'] );
		$this->assertSame( 'Edited by hand', get_post( $id )->post_content );
		$this->assertSame( $modified, get_post( $id )->post_modified_gmt );
	}

	public function test_a_stale_template_on_an_existing_pattern_page_is_repaired_without_touching_content(): void {
		$pattern = '<!-- wp:pattern {"slug":"oaf/page-donate"} /-->';
		$id      = $this->make_page(
			array(
				'post_name'    => 'donate',
				'post_title'   => 'Give',
				'post_content' => $pattern,
			),
			self::STALE
		);

		$result = $this->reconcile_one( 'donate', 'create' );

		$this->assertSame( 'repaired', $result['status'] );
		$this->assertSame( 'page-no-title', get_page_template_slug( $id ) );
		$this->assertSame( $pattern, get_post( $id )->post_content );
		$this->assertSame( 'Give', get_post( $id )->post_title );
	}

	public function test_a_stale_template_on_a_page_with_ordinary_content_gets_the_default_template(): void {
		// An imported page with its own content, not a pattern: page-no-title would
		// hide its title, so it must fall back to the default template instead.
		$id = $this->make_page(
			array(
				'post_name'    => 'about',
				'post_title'   => 'About us',
				'post_content' => 'Edited by hand',
			),
			self::STALE
		);

		$result = $this->reconcile_one( 'about', 'create' );

		$this->assertSame( 'repaired', $result['status'] );
		$this->assertSame( '', (string) get_page_template_slug( $id ) );
		$this->assertSame( 'Edited by hand', get_post( $id )->post_content );
		$this->assertSame( 'About us', get_post( $id )->post_title );
	}

	public function test_a_stale_template_on_a_page_with_no_theme_template_is_cleared(): void {
		$id = $this->make_page( array( 'post_name' => 'home' ), self::STALE );

		$result = $this->reconcile_one( 'home', 'create' );

		$this->assertSame( 'repaired', $result['status'] );
		$this->assertSame( '', (string) get_page_template_slug( $id ) );
	}

	public function test_a_child_page_sharing_a_slug_is_not_mistaken_for_the_required_page(): void {
		$about  = $this->make_page( array( 'post_name' => 'about' ) );
		$nested = $this->make_page( array( 'post_name' => 'donate', 'post_parent' => $about, 'post_content' => 'Not the donate page' ) );

		$result = $this->reconcile_one( 'donate', 'recreate' );

		$this->assertSame( 'created', $result['status'] );
		$this->assertNotSame( $nested, $result['id'] );
		$this->assertSame( 'Not the donate page', get_post( $nested )->post_content );
	}

	public function test_a_trashed_page_is_reported_and_not_duplicated(): void {
		$id = $this->make_page( array( 'post_name' => 'people' ) );
		wp_trash_post( $id );

		$result = $this->reconcile_one( 'people', 'create' );

		$this->assertSame( 'trashed', $result['status'] );
		$this->assertSame( $id, $result['id'] );
		$this->assertCount( 1, get_posts( array( 'post_type' => 'page', 'post_status' => self::ALL_STATUSES, 'numberposts' => -1, 'fields' => 'ids' ) ) );
	}

	public function test_creating_every_page_points_the_site_at_home_and_blog(): void {
		$results = oaf_reconcile_required_pages( 'create', null, true );

		$this->assertSame( array_keys( oaf_required_pages() ), array_keys( $results ) );
		$this->assertSame( array( 'created' ), array_values( array_unique( array_column( $results, 'status' ) ) ) );
		$this->assertSame( 'page', get_option( 'show_on_front' ) );
		$this->assertSame( $results['home']['id'], (int) get_option( 'page_on_front' ) );
		$this->assertSame( $results['blog']['id'], (int) get_option( 'page_for_posts' ) );
	}

	public function test_creating_again_leaves_every_page_unchanged(): void {
		oaf_reconcile_required_pages( 'create' );

		$again = oaf_reconcile_required_pages( 'create' );

		$this->assertSame( array( 'unchanged' ), array_values( array_unique( array_column( $again, 'status' ) ) ) );
	}

	public function test_only_the_requested_pages_are_reconciled_and_unknown_slugs_are_ignored(): void {
		$results = oaf_reconcile_required_pages( 'create', array( 'about', 'not-a-page' ) );

		$this->assertSame( array( 'about' ), array_keys( $results ) );
		$this->assertNull( get_page_by_path( 'donate' ) );
	}

	// ---- recreate mode: overwrite content from the pattern ----

	public function test_recreate_replaces_content_and_keeps_title_slug_and_status(): void {
		$id = $this->make_page(
			array(
				'post_name'   => 'about',
				'post_title'  => 'About us',
				'post_status' => 'draft',
			),
			'page-no-title'
		);

		$result = $this->reconcile_one( 'about', 'recreate' );

		$this->assertSame( 'overwritten', $result['status'] );
		$page = get_post( $id );
		$this->assertSame( '<!-- wp:pattern {"slug":"oaf/page-about"} /-->', $page->post_content );
		$this->assertSame( 'About us', $page->post_title );
		$this->assertSame( 'about', $page->post_name );
		$this->assertSame( 'draft', $page->post_status );
	}

	public function test_recreating_a_page_with_a_stale_template_repairs_the_template(): void {
		// The production failure: legacy Donate carries the classic theme's template.
		$id = $this->make_page( array( 'post_name' => 'donate' ), self::STALE );
		$this->assertSame( self::STALE, get_page_template_slug( $id ) );

		$result = $this->reconcile_one( 'donate', 'recreate' );

		$this->assertSame( 'overwritten', $result['status'], 'WordPress rejected the stale template.' );
		$this->assertSame( 'page-no-title', get_page_template_slug( $id ) );
	}

	public function test_recreate_never_blanks_a_page_that_has_no_pattern(): void {
		$id = $this->make_page( array( 'post_name' => 'home', 'post_content' => 'Keep me' ) );

		$result = $this->reconcile_one( 'home', 'recreate' );

		$this->assertSame( 'unchanged', $result['status'] );
		$this->assertSame( 'Keep me', get_post( $id )->post_content );
	}

	// ---- failures say why ----

	public function test_a_failed_overwrite_reports_the_reason(): void {
		$id = $this->make_page( array( 'post_name' => 'about' ), 'page-no-title' );
		add_filter( 'wp_insert_post_empty_content', '__return_true' );

		$result = $this->reconcile_one( 'about', 'recreate' );

		$this->assertSame( 'error', $result['status'] );
		$this->assertSame( $id, $result['id'] );
		$this->assertSame( 'empty_content', $result['error']['code'] );
		$this->assertNotSame( '', $result['error']['message'] );
	}

	public function test_a_failed_create_reports_the_reason(): void {
		add_filter( 'wp_insert_post_empty_content', '__return_true' );

		$result = $this->reconcile_one( 'about', 'create' );

		$this->assertSame( 'error', $result['status'] );
		$this->assertSame( 0, $result['id'] );
		$this->assertSame( 'empty_content', $result['error']['code'] );
	}
}
