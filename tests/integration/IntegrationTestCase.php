<?php
/**
 * Base class for integration tests: starts and ends every test with no pages.
 *
 * Safe only because bootstrap.php refuses any database but the wp-env test one.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$this->delete_all_pages();
	}

	protected function tearDown(): void {
		$this->delete_all_pages();
		update_option( 'show_on_front', 'posts' );
		update_option( 'page_on_front', 0 );
		update_option( 'page_for_posts', 0 );
		parent::tearDown();
	}

	private function delete_all_pages(): void {
		$ids = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
				'numberposts'      => -1,
				'fields'           => 'ids',
				'suppress_filters' => true,
			)
		);
		foreach ( $ids as $id ) {
			wp_delete_post( (int) $id, true );
		}
	}

	/**
	 * Insert a page, optionally with a stored page template, bypassing validation
	 * the way imported legacy pages arrive (meta written directly).
	 *
	 * @param array<string,mixed> $args     wp_insert_post() arguments.
	 * @param string              $template Value for _wp_page_template.
	 * @return int Page ID.
	 */
	protected function make_page( array $args, string $template = '' ): int {
		$id = wp_insert_post(
			array_merge(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => 'Test page',
					'post_content' => 'Original content',
				),
				$args
			),
			true
		);
		$this->assertIsInt( $id );

		if ( '' !== $template ) {
			update_post_meta( $id, '_wp_page_template', $template );
		}

		return $id;
	}
}
