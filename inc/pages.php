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

		foreach ( oaf_required_pages() as $slug => $page ) {
			$existing = get_page_by_path( $slug );
			if ( $existing instanceof WP_Post ) {
				$skipped[]    = $slug;
				$ids[ $slug ] = $existing->ID;
				continue;
			}

			$content = $page['pattern']
				? '<!-- wp:pattern {"slug":"' . $page['pattern'] . '"} /-->'
				: '';

			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $page['title'],
					'post_name'    => $slug,
					'post_content' => $content,
				)
			);

			if ( $id && ! is_wp_error( $id ) ) {
				$created[]    = $slug;
				$ids[ $slug ] = $id;
			}
		}

		if ( $set_front && isset( $ids['home'], $ids['blog'] ) ) {
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
		<?php
	}
}
