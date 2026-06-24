<?php
/**
 * Required-page creation.
 *
 * Provides the idempotent "Create required pages" action used by the theme admin
 * screen. Pages hold a pattern reference (the same content model as the dev
 * `.wp-setup.sh` seed) so the pattern PHP keeps running on each page.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_required_pages' ) ) {
	/**
	 * The minimum set of pages the theme expects, keyed by slug.
	 *
	 * `pattern` is the block pattern inserted as the page content, or null for
	 * pages whose body is supplied by a template (home, blog).
	 *
	 * @return array<string,array{title:string,pattern:?string}>
	 */
	function oaf_required_pages() {
		return array(
			'home'       => array(
				'title'   => __( 'Home', 'oaf-wp-blocktheme' ),
				'pattern' => null,
			),
			'blog'       => array(
				'title'   => __( 'Blog', 'oaf-wp-blocktheme' ),
				'pattern' => null,
			),
			'about'      => array(
				'title'   => __( 'About', 'oaf-wp-blocktheme' ),
				'pattern' => 'oaf/page-about',
			),
			'collection' => array(
				'title'   => __( 'Collection', 'oaf-wp-blocktheme' ),
				'pattern' => 'oaf/page-collection',
			),
			'people'     => array(
				'title'   => __( 'People', 'oaf-wp-blocktheme' ),
				'pattern' => 'oaf/page-people',
			),
			'contact'    => array(
				'title'   => __( 'Contact', 'oaf-wp-blocktheme' ),
				'pattern' => 'oaf/page-contact',
			),
			'donate'     => array(
				'title'   => __( 'Donate', 'oaf-wp-blocktheme' ),
				'pattern' => 'oaf/page-donate',
			),
		);
	}
}

if ( ! function_exists( 'oaf_recreatable_pages' ) ) {
	/**
	 * The pattern-backed pages that can be re-created from their pattern.
	 *
	 * Excludes home/blog, whose body comes from templates (null pattern) and so
	 * would gain no content from a re-create.
	 *
	 * @return array<string,string> slug => title.
	 */
	function oaf_recreatable_pages() {
		$out = array();
		foreach ( oaf_required_pages() as $slug => $page ) {
			if ( ! empty( $page['pattern'] ) ) {
				$out[ $slug ] = $page['title'];
			}
		}
		return $out;
	}
}

if ( ! function_exists( 'oaf_write_page' ) ) {
	/**
	 * Insert a required page, or overwrite an existing one's content in place.
	 *
	 * Looks the page up by its top-level slug. When missing, it is created with the
	 * pattern body (as the create flow always has). When it exists and $overwrite is
	 * true, only post_content is replaced - title, slug, status and menu_order are
	 * preserved, and wp_update_post() keeps the previous content as a page revision.
	 * When it exists and $overwrite is false, it is left untouched.
	 *
	 * @param string $slug      A key of oaf_required_pages().
	 * @param bool   $overwrite Replace an existing page's content.
	 * @return array{status:string,id:int} status: created|overwritten|skipped|invalid.
	 */
	function oaf_write_page( $slug, $overwrite = false ) {
		$pages = oaf_required_pages();
		if ( ! isset( $pages[ $slug ] ) ) {
			return array(
				'status' => 'invalid',
				'id'     => 0,
			);
		}

		$content = $pages[ $slug ]['pattern']
			? '<!-- wp:pattern {"slug":"' . $pages[ $slug ]['pattern'] . '"} /-->'
			: '';

		$existing = get_page_by_path( $slug );

		if ( $existing instanceof WP_Post ) {
			if ( ! $overwrite ) {
				return array(
					'status' => 'skipped',
					'id'     => $existing->ID,
				);
			}

			$id = wp_update_post(
				array(
					'ID'           => $existing->ID,
					'post_content' => $content,
				),
				true
			);

			return array(
				'status' => is_wp_error( $id ) ? 'skipped' : 'overwritten',
				'id'     => is_wp_error( $id ) ? $existing->ID : (int) $id,
			);
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $pages[ $slug ]['title'],
				'post_name'    => $slug,
				'post_content' => $content,
			)
		);

		return ( $id && ! is_wp_error( $id ) )
			? array(
				'status' => 'created',
				'id'     => (int) $id,
			)
			: array(
				'status' => 'skipped',
				'id'     => 0,
			);
	}
}

if ( ! function_exists( 'oaf_create_required_pages' ) ) {
	/**
	 * Create any missing required pages. Existing slugs are left untouched.
	 *
	 * @param bool $set_front Whether to also set Home as the front page and Blog
	 *                        as the posts page.
	 * @return array{created:string[],skipped:string[]}
	 */
	function oaf_create_required_pages( $set_front = false ) {
		$created = array();
		$skipped = array();
		$ids     = array();

		foreach ( array_keys( oaf_required_pages() ) as $slug ) {
			$result       = oaf_write_page( $slug, false );
			$ids[ $slug ] = $result['id'];

			if ( 'created' === $result['status'] ) {
				$created[] = $slug;
			} elseif ( 'skipped' === $result['status'] ) {
				$skipped[] = $slug;
			}
		}

		if ( $set_front && ! empty( $ids['home'] ) && ! empty( $ids['blog'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $ids['home'] );
			update_option( 'page_for_posts', $ids['blog'] );
		}

		return array(
			'created' => $created,
			'skipped' => $skipped,
		);
	}
}

if ( ! function_exists( 'oaf_handle_create_pages' ) ) {
	/**
	 * Handle the "Create required pages" form submission.
	 */
	function oaf_handle_create_pages() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'oaf-wp-blocktheme' ) );
		}
		check_admin_referer( 'oaf_create_pages' );

		$result = oaf_create_required_pages( ! empty( $_POST['oaf_set_front'] ) );

		// Seed the example people too, so the People page is populated on first setup.
		if ( function_exists( 'oaf_seed_people' ) ) {
			$result['people'] = oaf_seed_people();
		}

		set_transient( 'oaf_pages_result', $result, MINUTE_IN_SECONDS );

		wp_safe_redirect( add_query_arg( 'page', 'oaf-theme', admin_url( 'themes.php' ) ) );
		exit;
	}
}
add_action( 'admin_post_oaf_create_pages', 'oaf_handle_create_pages' );

if ( ! function_exists( 'oaf_handle_recreate_pages' ) ) {
	/**
	 * Handle the "Re-create selected" form submission: overwrite the chosen pages'
	 * content with their theme pattern. Only the pattern-backed pages are accepted.
	 */
	function oaf_handle_recreate_pages() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'oaf-wp-blocktheme' ) );
		}
		check_admin_referer( 'oaf_recreate_pages' );

		$requested = isset( $_POST['oaf_recreate'] ) ? (array) wp_unslash( $_POST['oaf_recreate'] ) : array();
		$requested = array_map( 'sanitize_key', $requested );
		$allowed   = array_keys( oaf_recreatable_pages() );

		$done = array();
		foreach ( array_intersect( $requested, $allowed ) as $slug ) {
			$result = oaf_write_page( $slug, true );
			if ( in_array( $result['status'], array( 'overwritten', 'created' ), true ) ) {
				$done[] = $slug;
			}
		}

		set_transient( 'oaf_recreate_result', array( 'done' => $done ), MINUTE_IN_SECONDS );

		wp_safe_redirect( add_query_arg( 'page', 'oaf-theme', admin_url( 'themes.php' ) ) );
		exit;
	}
}
add_action( 'admin_post_oaf_recreate_pages', 'oaf_handle_recreate_pages' );

if ( ! function_exists( 'oaf_render_pages_section' ) ) {
	/**
	 * Render the "Required pages" section of the theme admin screen, including the
	 * result notice from the last run.
	 */
	function oaf_render_pages_section() {
		$result = get_transient( 'oaf_pages_result' );
		if ( is_array( $result ) ) {
			delete_transient( 'oaf_pages_result' );
			$created = ! empty( $result['created'] ) ? implode( ', ', $result['created'] ) : '';
			$skipped = ! empty( $result['skipped'] ) ? implode( ', ', $result['skipped'] ) : '';
			echo '<div class="notice notice-success is-dismissible"><p>';
			if ( '' !== $created ) {
				/* translators: %s: comma-separated list of page slugs. */
				printf( esc_html__( 'Created: %s.', 'oaf-wp-blocktheme' ), '<strong>' . esc_html( $created ) . '</strong>' );
				echo ' ';
			}
			if ( '' !== $skipped ) {
				/* translators: %s: comma-separated list of page slugs. */
				printf( esc_html__( 'Already existed (skipped): %s.', 'oaf-wp-blocktheme' ), '<strong>' . esc_html( $skipped ) . '</strong>' );
			}
			if ( '' === $created ) {
				echo ' ' . esc_html__( 'No new pages were needed.', 'oaf-wp-blocktheme' );
			}
			if ( ! empty( $result['people'] ) ) {
				echo ' ';
				/* translators: %d: number of people created. */
				printf( esc_html__( 'Seeded %d example people.', 'oaf-wp-blocktheme' ), (int) $result['people'] );
			}
			echo '</p></div>';
		}

		$recreate = get_transient( 'oaf_recreate_result' );
		if ( is_array( $recreate ) ) {
			delete_transient( 'oaf_recreate_result' );
			$done = ! empty( $recreate['done'] ) ? implode( ', ', $recreate['done'] ) : '';
			echo '<div class="notice notice-success is-dismissible"><p>';
			if ( '' !== $done ) {
				/* translators: %s: comma-separated list of page slugs. */
				printf( esc_html__( 'Re-created: %s. The old content was kept as a page revision.', 'oaf-wp-blocktheme' ), '<strong>' . esc_html( $done ) . '</strong>' );
			} else {
				esc_html_e( 'No pages were selected to re-create.', 'oaf-wp-blocktheme' );
			}
			echo '</p></div>';
		}
		?>
		<h2><?php esc_html_e( 'Required pages', 'oaf-wp-blocktheme' ); ?></h2>
		<p><?php esc_html_e( 'Create the standard OAF pages (About, Collection, People, Contact, Donate) plus Home and Blog, and seed the example Team and Board people. Pages and people that already exist are left untouched, so this is safe to run more than once.', 'oaf-wp-blocktheme' ); ?></p>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="oaf_create_pages" />
			<?php wp_nonce_field( 'oaf_create_pages' ); ?>
			<p>
				<label>
					<input type="checkbox" name="oaf_set_front" value="1" checked="checked" />
					<?php esc_html_e( 'Also set Home as the front page and Blog as the posts page', 'oaf-wp-blocktheme' ); ?>
				</label>
			</p>
			<?php submit_button( __( 'Create required pages', 'oaf-wp-blocktheme' ), 'primary', 'oaf_create_pages_submit', false ); ?>
		</form>

		<hr />

		<h2><?php esc_html_e( 'Re-create pages from theme patterns', 'oaf-wp-blocktheme' ); ?></h2>
		<p><?php esc_html_e( 'Replace these pages\' content with the theme\'s pattern, even when the page already exists. This overwrites the current content; the old content is kept as a page revision so you can restore it. The page title, URL and published/draft status are left unchanged.', 'oaf-wp-blocktheme' ); ?></p>
		<form id="oaf-recreate-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="oaf_recreate_pages" />
			<?php wp_nonce_field( 'oaf_recreate_pages' ); ?>
			<?php
			foreach ( oaf_recreatable_pages() as $oaf_slug => $oaf_title ) {
				$oaf_page   = get_page_by_path( $oaf_slug );
				$oaf_status = $oaf_page ? $oaf_page->post_status : 'missing';

				if ( ! $oaf_page ) {
					$oaf_state = __( 'Missing — will be created', 'oaf-wp-blocktheme' );
				} elseif ( 'publish' === $oaf_status ) {
					/* translators: %s: page slug. */
					$oaf_state = sprintf( __( 'Published — /%s/', 'oaf-wp-blocktheme' ), $oaf_slug );
				} else {
					/* translators: 1: post status (e.g. draft), 2: page slug. */
					$oaf_state = sprintf( __( '%1$s — /%2$s/', 'oaf-wp-blocktheme' ), $oaf_status, $oaf_slug );
				}
				?>
				<p>
					<label>
						<input type="checkbox" name="oaf_recreate[]" value="<?php echo esc_attr( $oaf_slug ); ?>" data-status="<?php echo esc_attr( $oaf_status ); ?>" data-title="<?php echo esc_attr( $oaf_title ); ?>" />
						<strong><?php echo esc_html( $oaf_title ); ?></strong>
						<span class="description">(<?php echo esc_html( $oaf_state ); ?>)</span>
					</label>
				</p>
				<?php
			}
			submit_button( __( 'Re-create selected', 'oaf-wp-blocktheme' ), 'secondary', 'oaf_recreate_submit', false );
			?>
		</form>
		<script>
		( function () {
			var form = document.getElementById( 'oaf-recreate-form' );
			if ( ! form ) {
				return;
			}
			form.addEventListener( 'submit', function ( e ) {
				var checked = form.querySelectorAll( 'input[name="oaf_recreate[]"]:checked' );
				if ( ! checked.length ) {
					e.preventDefault();
					window.alert( <?php echo wp_json_encode( __( 'Select at least one page to re-create.', 'oaf-wp-blocktheme' ) ); ?> );
					return;
				}
				var published = [];
				Array.prototype.forEach.call( checked, function ( cb ) {
					if ( 'publish' === cb.getAttribute( 'data-status' ) ) {
						published.push( cb.getAttribute( 'data-title' ) );
					}
				} );
				if ( published.length ) {
					var msg = <?php echo wp_json_encode( __( 'This will overwrite the content of these published pages:', 'oaf-wp-blocktheme' ) ); ?>
						+ '\n\n  • ' + published.join( '\n  • ' )
						+ '\n\n' + <?php echo wp_json_encode( __( 'The old content is kept as a page revision. Continue?', 'oaf-wp-blocktheme' ) ); ?>;
					if ( ! window.confirm( msg ) ) {
						e.preventDefault();
					}
				}
			} );
		}() );
		</script>
		<?php
	}
}
