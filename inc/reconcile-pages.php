<?php
/**
 * Reconcile the required pages: bring each one to the state the theme expects.
 *
 * Write policy only. It returns plain results and prints nothing, so the admin
 * screen (inc/pages.php), the dev seed (`wp eval`) and the integration tests all
 * use it the same way. The pages themselves are defined in
 * inc/required-pages.php. Loaded in admin and under WP-CLI, never on the front end.
 *
 * Two modes:
 *
 * - `create`: make any missing page. A page that already exists keeps its
 *   content, title and status; only a stale template is repaired.
 * - `recreate`: overwrite a pattern page's content from its pattern (the old
 *   content is kept as a revision). Title, slug and status are kept.
 *
 * A result is `array{status:string,id:int,error?:array{code:string,message:string}}`
 * with status one of created, overwritten, repaired, unchanged, trashed, error.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_find_required_page' ) ) {
	/**
	 * Find an existing page for a required slug, across every status including trash.
	 *
	 * WordPress's get_page_by_path() ignores nothing by status but cannot find a
	 * trashed page, because it suffixes a trashed page's slug with `__trashed`. This looks
	 * for a live page first (any non-trash status), then for a trashed one, so the
	 * create/re-create flow neither mistakes a draft for "missing" nor duplicates a
	 * page that is only in the Trash.
	 *
	 * @param string $slug A key of oaf_required_pages().
	 * @return array{post:?WP_Post,trashed:bool}
	 */
	function oaf_find_required_page( $slug ) {
		// A top-level required page is top-level: a child page that happens to share
		// the slug (e.g. /about/donate/) must not be mistaken for it.
		$parent = array();
		if ( empty( oaf_required_pages()[ $slug ]['parent'] ) ) {
			$parent = array( 'post_parent' => 0 );
		}

		$live = new WP_Query(
			array_merge(
				array(
					'post_type'        => 'page',
					'name'             => $slug,
					'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future' ),
					'posts_per_page'   => 1,
					'no_found_rows'    => true,
					'suppress_filters' => false,
				),
				$parent
			)
		);
		if ( $live->have_posts() ) {
			return array(
				'post'    => $live->posts[0],
				'trashed' => false,
			);
		}

		$trashed = new WP_Query(
			array_merge(
				array(
					'post_type'      => 'page',
					'name'           => $slug . '__trashed',
					'post_status'    => 'trash',
					'posts_per_page' => 1,
					'no_found_rows'  => true,
				),
				$parent
			)
		);
		if ( $trashed->have_posts() ) {
			return array(
				'post'    => $trashed->posts[0],
				'trashed' => true,
			);
		}

		return array(
			'post'    => null,
			'trashed' => false,
		);
	}
}

if ( ! function_exists( 'oaf_reconcile_page' ) ) {
	/**
	 * Reconcile a single required page.
	 *
	 * @param string $slug A key of oaf_required_pages().
	 * @param string $mode 'create' or 'recreate'.
	 * @return array{status:string,id:int,error?:array{code:string,message:string}}
	 */
	function oaf_reconcile_page( $slug, $mode ) {
		$page     = oaf_required_pages()[ $slug ];
		$pattern  = $page['pattern'];
		$template = ! empty( $page['template'] ) ? $page['template'] : '';
		$found    = oaf_find_required_page( $slug );

		if ( $found['trashed'] ) {
			return array(
				'status' => 'trashed',
				'id'     => $found['post']->ID,
			);
		}

		$existing = $found['post'];
		if ( ! $existing instanceof WP_Post ) {
			return oaf_create_required_page( $slug, $page );
		}

		if ( 'recreate' === $mode && $pattern ) {
			// wp_update_post() merges the page's stored template back in and rejects
			// it if the theme no longer has it, so pass the template we want.
			$update = array(
				'ID'           => $existing->ID,
				'post_content' => '<!-- wp:pattern {"slug":"' . $pattern . '"} /-->',
			);
			if ( '' !== $template ) {
				$update['page_template'] = $template;
			} elseif ( '' !== oaf_stale_page_template( $existing ) ) {
				$update['page_template'] = 'default';
			}

			$id = wp_update_post( $update, true );
			if ( is_wp_error( $id ) ) {
				return oaf_reconcile_error( $existing->ID, $id );
			}

			return array(
				'status' => 'overwritten',
				'id'     => (int) $id,
			);
		}

		if ( '' !== oaf_stale_page_template( $existing ) ) {
			// Template only: content, title and status stay as they were. Saved through
			// wp_update_post() so WordPress's save hooks run and caches are cleared.
			$id = wp_update_post(
				array(
					'ID'            => $existing->ID,
					'page_template' => '' !== $template ? $template : 'default',
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				return oaf_reconcile_error( $existing->ID, $id );
			}

			return array(
				'status' => 'repaired',
				'id'     => $existing->ID,
			);
		}

		return array(
			'status' => 'unchanged',
			'id'     => $existing->ID,
		);
	}
}

if ( ! function_exists( 'oaf_create_required_page' ) ) {
	/**
	 * Create a missing required page, nested under its parent when it declares one.
	 *
	 * @param string $slug A key of oaf_required_pages().
	 * @param array  $page That page's definition.
	 * @return array{status:string,id:int,error?:array{code:string,message:string}}
	 */
	function oaf_create_required_page( $slug, $page ) {
		// Nest under a parent page (e.g. /donate/alternatives/) when the slug
		// declares one and that parent currently exists outside the Trash.
		$post_parent = 0;
		if ( ! empty( $page['parent'] ) ) {
			$parent_found = oaf_find_required_page( $page['parent'] );
			if ( $parent_found['post'] instanceof WP_Post && ! $parent_found['trashed'] ) {
				$post_parent = (int) $parent_found['post']->ID;
			}
		}

		$args = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $page['title'],
			'post_name'    => $slug,
			'post_content' => $page['pattern'] ? '<!-- wp:pattern {"slug":"' . $page['pattern'] . '"} /-->' : '',
			'post_parent'  => $post_parent,
		);
		if ( ! empty( $page['template'] ) ) {
			$args['page_template'] = $page['template'];
		}

		$id = wp_insert_post( $args, true );
		if ( is_wp_error( $id ) ) {
			return oaf_reconcile_error( 0, $id );
		}

		return array(
			'status' => 'created',
			'id'     => (int) $id,
		);
	}
}

if ( ! function_exists( 'oaf_reconcile_error' ) ) {
	/**
	 * An error result carrying WordPress's reason for the failure.
	 *
	 * @param int      $id    The page's ID, or 0 when it was never created.
	 * @param WP_Error $error What WordPress returned.
	 * @return array{status:string,id:int,error:array{code:string,message:string}}
	 */
	function oaf_reconcile_error( $id, $error ) {
		return array(
			'status' => 'error',
			'id'     => (int) $id,
			'error'  => array(
				'code'    => (string) $error->get_error_code(),
				'message' => $error->get_error_message(),
			),
		);
	}
}

if ( ! function_exists( 'oaf_reconcile_required_pages' ) ) {
	/**
	 * Reconcile the required pages.
	 *
	 * @param string        $mode      'create' or 'recreate'.
	 * @param string[]|null $slugs     Pages to reconcile, or null for every required page.
	 *                                 Unknown slugs are ignored.
	 * @param bool          $set_front When Home and Blog are both published, also point the
	 *                                 site's front page and posts page at them.
	 * @return array<string,array{status:string,id:int,error?:array{code:string,message:string}}>
	 *         Results keyed by page slug, in registry order.
	 */
	function oaf_reconcile_required_pages( $mode = 'create', $slugs = null, $set_front = false ) {
		$wanted  = null === $slugs ? array_keys( oaf_required_pages() ) : $slugs;
		$results = array();

		foreach ( array_keys( oaf_required_pages() ) as $slug ) {
			if ( in_array( $slug, $wanted, true ) ) {
				$results[ $slug ] = oaf_reconcile_page( $slug, $mode );
			}
		}

		// Only point the site at published Home/Blog pages - never at a draft,
		// private or trashed page, which would leave the front end blank.
		if ( $set_front
			&& ! empty( $results['home']['id'] ) && 'publish' === get_post_status( $results['home']['id'] )
			&& ! empty( $results['blog']['id'] ) && 'publish' === get_post_status( $results['blog']['id'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $results['home']['id'] );
			update_option( 'page_for_posts', $results['blog']['id'] );
		}

		return $results;
	}
}
