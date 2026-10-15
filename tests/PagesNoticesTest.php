<?php
/**
 * Tests for the admin notices built from a reconcile result (inc/pages.php).
 *
 * The notices are the one part of the Required pages screen that is plain logic;
 * the handlers and the form need WordPress and are covered by the integration
 * suite and by hand. esc_html() is a pass-through stub here, so these assert
 * which notices appear and what they say, not escaping.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

final class PagesNoticesTest extends TestCase {

	/**
	 * @param array<string,string|array> $statuses slug => status, or slug => result.
	 * @return array<string,array{status:string,id:int}>
	 */
	private function results( array $statuses ): array {
		$out = array();
		foreach ( $statuses as $slug => $value ) {
			$out[ $slug ] = is_array( $value ) ? $value : array(
				'status' => $value,
				'id'     => 1,
			);
		}
		return $out;
	}

	private function text( array $notices, string $type ): string {
		foreach ( $notices as $notice ) {
			if ( $type === $notice['type'] ) {
				return strip_tags( $notice['html'] );
			}
		}
		return '';
	}

	public function test_create_lists_what_was_created_and_what_already_existed(): void {
		$notices = oaf_pages_notices( 'create', $this->results( array( 'about' => 'created', 'donate' => 'unchanged', 'people' => 'unchanged' ) ) );

		$this->assertCount( 1, $notices );
		$this->assertSame( 'Created: about. Already existed (skipped): donate, people.', $this->text( $notices, 'success' ) );
	}

	public function test_create_says_when_nothing_was_needed(): void {
		$notices = oaf_pages_notices( 'create', $this->results( array( 'about' => 'unchanged' ) ) );

		$this->assertSame( 'Already existed (skipped): about. No new pages were needed.', $this->text( $notices, 'success' ) );
	}

	public function test_create_reports_a_repaired_template(): void {
		$notices = oaf_pages_notices( 'create', $this->results( array( 'donate' => 'repaired' ) ) );

		$this->assertStringContainsString( 'Old-theme page template replaced (content untouched): donate.', $this->text( $notices, 'success' ) );
		$this->assertStringNotContainsString( 'No new pages were needed', $this->text( $notices, 'success' ) );
	}

	public function test_create_reports_seeded_people(): void {
		$notices = oaf_pages_notices( 'create', $this->results( array( 'about' => 'created' ) ), 9 );

		$this->assertStringContainsString( 'Seeded 9 example people.', $this->text( $notices, 'success' ) );
	}

	public function test_a_failure_is_not_reported_as_nothing_needed(): void {
		$notices = oaf_pages_notices( 'create', $this->results( array( 'about' => array( 'status' => 'error', 'id' => 0, 'error' => array( 'code' => 'empty_content', 'message' => 'x' ) ) ) ) );

		$this->assertCount( 1, $notices );
		$this->assertSame( 'error', $notices[0]['type'] );
	}

	public function test_an_error_names_the_page_and_the_reason(): void {
		$result  = array( 'status' => 'error', 'id' => 5, 'error' => array( 'code' => 'invalid_page_template', 'message' => 'Invalid page template.' ) );
		$notices = oaf_pages_notices( 'recreate', $this->results( array( 'donate' => $result ) ) );

		$this->assertSame( 'Could not be re-created: donate (invalid_page_template).', $this->text( $notices, 'error' ) );
	}

	public function test_create_error_wording_differs_from_recreate(): void {
		$result  = array( 'status' => 'error', 'id' => 0, 'error' => array( 'code' => 'empty_content', 'message' => 'x' ) );
		$notices = oaf_pages_notices( 'create', $this->results( array( 'about' => $result ) ) );

		$this->assertSame( 'Could not be created: about (empty_content).', $this->text( $notices, 'error' ) );
	}

	public function test_an_error_without_a_reason_still_names_the_page(): void {
		$notices = oaf_pages_notices( 'create', $this->results( array( 'about' => array( 'status' => 'error', 'id' => 0 ) ) ) );

		$this->assertSame( 'Could not be created: about.', $this->text( $notices, 'error' ) );
	}

	public function test_a_trashed_page_says_what_to_do_next(): void {
		$create   = oaf_pages_notices( 'create', $this->results( array( 'people' => 'trashed' ) ) );
		$recreate = oaf_pages_notices( 'recreate', $this->results( array( 'people' => 'trashed' ) ) );

		$this->assertSame( 'In the Trash, so skipped: people. Restore each page first, then run this again.', $this->text( $create, 'error' ) );
		$this->assertSame( 'In the Trash, so skipped: people. Restore each page first, then re-create it.', $this->text( $recreate, 'error' ) );
	}

	public function test_recreate_lists_what_was_overwritten(): void {
		$notices = oaf_pages_notices( 'recreate', $this->results( array( 'about' => 'overwritten', 'donate' => 'overwritten' ) ) );

		$this->assertSame( 'Re-created: about, donate. The old content was kept as a page revision.', $this->text( $notices, 'success' ) );
	}

	public function test_recreate_also_reports_a_missing_page_that_was_created(): void {
		$notices = oaf_pages_notices( 'recreate', $this->results( array( 'about' => 'overwritten', 'donate' => 'created' ) ) );

		$this->assertSame( 'Re-created: about. The old content was kept as a page revision. Created (they were missing): donate.', $this->text( $notices, 'success' ) );
	}

	public function test_recreate_with_nothing_selected_says_so(): void {
		$notices = oaf_pages_notices( 'recreate', array() );

		$this->assertSame( 'No pages were selected to re-create.', $this->text( $notices, 'success' ) );
	}

	public function test_success_and_failure_are_separate_notices(): void {
		$notices = oaf_pages_notices( 'recreate', $this->results( array( 'about' => 'overwritten', 'people' => 'trashed' ) ) );

		$this->assertSame( array( 'success', 'error' ), array_column( $notices, 'type' ) );
	}
}
