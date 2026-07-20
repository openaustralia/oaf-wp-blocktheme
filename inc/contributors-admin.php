<?php
/**
 * Contributors admin controls (Appearance -> OAF Theme).
 *
 * The manual "Refresh now" action and the last-update status shown beneath the
 * theme settings form. The refresh worker, cache and status live in the
 * always-loaded inc/contributors.php; this file is admin-only.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_handle_refresh_contributors' ) ) {
	/**
	 * Handle the "Refresh now" form submission: fetch contributors synchronously and
	 * report the outcome. Follows the same nonce -> work -> transient -> redirect
	 * pattern as the required-pages actions.
	 */
	function oaf_handle_refresh_contributors() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'oaf-wp-blocktheme' ) );
		}
		check_admin_referer( 'oaf_refresh_contributors' );

		// Clear the coordination locks so a deliberate manual run is never skipped.
		delete_transient( 'oaf_contributors_refreshing' );
		delete_transient( 'oaf_contributors_cooldown' );

		$status = function_exists( 'oaf_refresh_contributors' ) ? oaf_refresh_contributors( 'manual' ) : array();

		set_transient( 'oaf_refresh_contributors_result', $status, MINUTE_IN_SECONDS );

		wp_safe_redirect( add_query_arg( 'page', 'oaf-theme', admin_url( 'themes.php' ) ) );
		exit;
	}
}
add_action( 'admin_post_oaf_refresh_contributors', 'oaf_handle_refresh_contributors' );

if ( ! function_exists( 'oaf_render_contributors_section' ) ) {
	/**
	 * Render the Contributors action + status block on the theme admin screen.
	 */
	function oaf_render_contributors_section() {
		$result = get_transient( 'oaf_refresh_contributors_result' );
		if ( is_array( $result ) && ! empty( $result ) ) {
			delete_transient( 'oaf_refresh_contributors_result' );
			if ( ! empty( $result['ok'] ) ) {
				echo '<div class="notice notice-success is-dismissible"><p>';
				/* translators: %d: number of contributors now shown. */
				printf( esc_html__( 'Contributors refreshed: %d shown.', 'oaf-wp-blocktheme' ), (int) $result['count'] );
				echo '</p></div>';
			} else {
				echo '<div class="notice notice-error is-dismissible"><p>';
				echo esc_html( ! empty( $result['reason'] ) ? $result['reason'] : __( 'The refresh did not complete.', 'oaf-wp-blocktheme' ) );
				echo '</p></div>';
			}
		}

		$cache   = get_option( 'oaf_contributors_cache', array() );
		$updated = isset( $cache['updated'] ) ? (int) $cache['updated'] : 0;
		$status  = function_exists( 'oaf_contributors_status' ) ? oaf_contributors_status() : array();
		$next    = wp_next_scheduled( 'oaf_refresh_contributors_weekly' );
		?>
		<h2><?php esc_html_e( 'Contributors', 'oaf-wp-blocktheme' ); ?></h2>
		<p><?php esc_html_e( 'The People page thanks volunteer contributors pulled from GitHub. The list refreshes automatically each week; use this to update it now, for example after changing the excluded usernames above.', 'oaf-wp-blocktheme' ); ?></p>

		<p>
			<?php
			if ( $updated ) {
				echo esc_html(
					sprintf(
						/* translators: 1: human-readable time difference; 2: number of contributors. */
						__( 'Last updated %1$s ago (%2$d contributors).', 'oaf-wp-blocktheme' ),
						human_time_diff( $updated ),
						isset( $status['count'] ) ? (int) $status['count'] : 0
					)
				);
			} else {
				esc_html_e( 'Not fetched yet.', 'oaf-wp-blocktheme' );
			}
			?>
		</p>
		<?php
		if ( ! empty( $status['time'] ) ) {
			echo '<p>';
			if ( ! empty( $status['ok'] ) ) {
				echo esc_html(
					sprintf(
						/* translators: 1: human-readable time difference; 2: trigger source. */
						__( 'Last attempt %1$s ago: succeeded (%2$s).', 'oaf-wp-blocktheme' ),
						human_time_diff( (int) $status['time'] ),
						'manual' === $status['source'] ? __( 'manual', 'oaf-wp-blocktheme' ) : __( 'automatic', 'oaf-wp-blocktheme' )
					)
				);
			} else {
				echo esc_html(
					sprintf(
						/* translators: 1: human-readable time difference; 2: failure reason. */
						__( 'Last attempt %1$s ago: failed - %2$s', 'oaf-wp-blocktheme' ),
						human_time_diff( (int) $status['time'] ),
						! empty( $status['reason'] ) ? $status['reason'] : __( 'unknown error.', 'oaf-wp-blocktheme' )
					)
				);
			}
			echo '</p>';
		}

		if ( $next ) {
			echo '<p>';
			echo esc_html(
				sprintf(
					/* translators: %s: human-readable time difference. */
					__( 'Next automatic refresh in about %s.', 'oaf-wp-blocktheme' ),
					human_time_diff( $next )
				)
			);
			echo '</p>';
		}
		?>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="oaf_refresh_contributors" />
			<?php wp_nonce_field( 'oaf_refresh_contributors' ); ?>
			<?php submit_button( __( 'Refresh now', 'oaf-wp-blocktheme' ), 'secondary', 'oaf_refresh_contributors_submit', false ); ?>
		</form>
		<?php
	}
}
