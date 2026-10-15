<?php
/**
 * The "Required pages" section of Appearance -> OAF Theme.
 *
 * Admin screen only: the two form handlers and the page that shows their result.
 * What the buttons do to pages lives in inc/reconcile-pages.php, which returns
 * plain results; the pages themselves are defined in inc/required-pages.php.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_pages_notices' ) ) {
	/**
	 * The admin notices for the result of a "Create" or "Re-create" run.
	 *
	 * @param string $mode    'create' or 'recreate'.
	 * @param array  $results Results from oaf_reconcile_required_pages(), keyed by slug.
	 * @param int    $people  Example people seeded alongside a create run.
	 * @return array<int,array{type:string,html:string}> Notices; html is already escaped.
	 */
	function oaf_pages_notices( $mode, $results, $people = 0 ) {
		$by = array();
		foreach ( $results as $slug => $result ) {
			$by[ $result['status'] ][] = $slug;
		}
		$list = static function ( $slugs ) {
			return '<strong>' . esc_html( implode( ', ', $slugs ) ) . '</strong>';
		};

		$notices = array();
		$ok      = array();

		if ( 'recreate' === $mode ) {
			if ( ! $results ) {
				$ok[] = esc_html__( 'No pages were selected to re-create.', 'oaf-wp-blocktheme' );
			}
			if ( ! empty( $by['overwritten'] ) ) {
				/* translators: %s: comma-separated list of page slugs. */
				$ok[] = sprintf( esc_html__( 'Re-created: %s. The old content was kept as a page revision.', 'oaf-wp-blocktheme' ), $list( $by['overwritten'] ) );
			}
			if ( ! empty( $by['created'] ) ) {
				/* translators: %s: comma-separated list of page slugs. */
				$ok[] = sprintf( esc_html__( 'Created (they were missing): %s.', 'oaf-wp-blocktheme' ), $list( $by['created'] ) );
			}
		} else {
			if ( ! empty( $by['created'] ) ) {
				/* translators: %s: comma-separated list of page slugs. */
				$ok[] = sprintf( esc_html__( 'Created: %s.', 'oaf-wp-blocktheme' ), $list( $by['created'] ) );
			}
			if ( ! empty( $by['repaired'] ) ) {
				/* translators: %s: comma-separated list of page slugs. */
				$ok[] = sprintf( esc_html__( 'Old-theme page template replaced (content untouched): %s.', 'oaf-wp-blocktheme' ), $list( $by['repaired'] ) );
			}
			if ( ! empty( $by['unchanged'] ) ) {
				/* translators: %s: comma-separated list of page slugs. */
				$ok[] = sprintf( esc_html__( 'Already existed (skipped): %s.', 'oaf-wp-blocktheme' ), $list( $by['unchanged'] ) );
			}
			if ( empty( $by['created'] ) && empty( $by['repaired'] ) && empty( $by['trashed'] ) && empty( $by['error'] ) ) {
				$ok[] = esc_html__( 'No new pages were needed.', 'oaf-wp-blocktheme' );
			}
			if ( $people ) {
				/* translators: %d: number of people created. */
				$ok[] = sprintf( esc_html__( 'Seeded %d example people.', 'oaf-wp-blocktheme' ), (int) $people );
			}
		}
		if ( $ok ) {
			$notices[] = array(
				'type' => 'success',
				'html' => implode( ' ', $ok ),
			);
		}

		$bad = array();
		if ( ! empty( $by['trashed'] ) ) {
			$bad[] = sprintf(
				/* translators: 1: comma-separated list of page slugs, 2: what to do next. */
				esc_html__( 'In the Trash, so skipped: %1$s. Restore each page first, then %2$s.', 'oaf-wp-blocktheme' ),
				$list( $by['trashed'] ),
				'recreate' === $mode ? esc_html__( 're-create it', 'oaf-wp-blocktheme' ) : esc_html__( 'run this again', 'oaf-wp-blocktheme' )
			);
		}
		if ( ! empty( $by['error'] ) ) {
			$failed = array();
			foreach ( $by['error'] as $slug ) {
				$code     = isset( $results[ $slug ]['error']['code'] ) ? $results[ $slug ]['error']['code'] : '';
				$failed[] = '' !== $code ? $slug . ' (' . $code . ')' : $slug;
			}
			$bad[] = sprintf(
				/* translators: 1: "created" or "re-created", 2: comma-separated list of pages with the reason WordPress gave. */
				esc_html__( 'Could not be %1$s: %2$s.', 'oaf-wp-blocktheme' ),
				'recreate' === $mode ? esc_html__( 're-created', 'oaf-wp-blocktheme' ) : esc_html__( 'created', 'oaf-wp-blocktheme' ),
				$list( $failed )
			);
		}
		if ( $bad ) {
			$notices[] = array(
				'type' => 'error',
				'html' => implode( ' ', $bad ),
			);
		}

		return $notices;
	}
}

if ( ! function_exists( 'oaf_finish_pages_action' ) ) {
	/**
	 * Keep a run's result for the next page load, then return to the settings screen.
	 *
	 * @param string $mode    'create' or 'recreate'.
	 * @param array  $results Results from oaf_reconcile_required_pages().
	 * @param int    $people  Example people seeded alongside a create run.
	 */
	function oaf_finish_pages_action( $mode, $results, $people = 0 ) {
		set_transient(
			'oaf_pages_result',
			array(
				'mode'    => $mode,
				'results' => $results,
				'people'  => $people,
			),
			MINUTE_IN_SECONDS
		);

		wp_safe_redirect( add_query_arg( 'page', 'oaf-theme', admin_url( 'themes.php' ) ) );
		exit;
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

		$results = oaf_reconcile_required_pages( 'create', null, ! empty( $_POST['oaf_set_front'] ) );

		// Seed the example people too, so the People page is populated on first setup.
		$people = function_exists( 'oaf_seed_people' ) ? oaf_seed_people() : 0;

		oaf_finish_pages_action( 'create', $results, $people );
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

		oaf_finish_pages_action( 'recreate', oaf_reconcile_required_pages( 'recreate', array_intersect( $requested, $allowed ) ) );
	}
}
add_action( 'admin_post_oaf_recreate_pages', 'oaf_handle_recreate_pages' );

if ( ! function_exists( 'oaf_render_pages_section' ) ) {
	/**
	 * Render the "Required pages" section of the theme admin screen, including the
	 * result notice from the last run.
	 */
	function oaf_render_pages_section() {
		$outcome = get_transient( 'oaf_pages_result' );
		if ( is_array( $outcome ) && isset( $outcome['mode'], $outcome['results'] ) ) {
			delete_transient( 'oaf_pages_result' );
			foreach ( oaf_pages_notices( $outcome['mode'], $outcome['results'], (int) $outcome['people'] ) as $notice ) {
				printf(
					'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
					esc_attr( $notice['type'] ),
					wp_kses( $notice['html'], array( 'strong' => array() ) )
				);
			}
		}
		?>
		<h2><?php esc_html_e( 'Required pages', 'oaf-wp-blocktheme' ); ?></h2>
		<p><?php esc_html_e( 'Create the standard OAF pages (About, Collection, People, Contact, Donate, Licence) plus Home and Blog, and seed the example Team and Board people. Pages and people that already exist are left as they are (apart from replacing a page template left over from the old theme), so this is safe to run more than once.', 'oaf-wp-blocktheme' ); ?></p>
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
				$oaf_found  = oaf_find_required_page( $oaf_slug );
				$oaf_page   = $oaf_found['post'];
				$oaf_status = $oaf_found['trashed'] ? 'trash' : ( $oaf_page ? $oaf_page->post_status : 'missing' );

				if ( $oaf_found['trashed'] ) {
					$oaf_state = __( 'In the Trash - restore it first', 'oaf-wp-blocktheme' );
				} elseif ( ! $oaf_page ) {
					$oaf_state = __( 'Missing - will be created', 'oaf-wp-blocktheme' );
				} elseif ( 'publish' === $oaf_status ) {
					/* translators: %s: page slug. */
					$oaf_state = sprintf( __( 'Published - /%s/', 'oaf-wp-blocktheme' ), $oaf_slug );
				} else {
					/* translators: 1: post status (e.g. draft), 2: page slug. */
					$oaf_state = sprintf( __( '%1$s - /%2$s/', 'oaf-wp-blocktheme' ), $oaf_status, $oaf_slug );
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
