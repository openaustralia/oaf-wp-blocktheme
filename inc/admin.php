<?php
/**
 * Theme admin screen (Appearance -> OAF Theme).
 *
 * Hosts the "Create required pages" action and a Settings API form for the
 * editable global components (organisation details, social links, sister-service
 * URLs, acknowledgement text and the Raisely donation embed).
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_admin_menu' ) ) {
	/**
	 * Add the theme screen under Appearance.
	 */
	function oaf_admin_menu() {
		add_theme_page(
			__( 'OAF Theme', 'oaf-wp-blocktheme' ),
			__( 'OAF Theme', 'oaf-wp-blocktheme' ),
			'manage_options',
			'oaf-theme',
			'oaf_render_admin_page'
		);
	}
}
add_action( 'admin_menu', 'oaf_admin_menu' );

if ( ! function_exists( 'oaf_register_settings' ) ) {
	/**
	 * Register the option, sections and fields with the Settings API.
	 */
	function oaf_register_settings() {
		register_setting(
			'oaf_theme',
			'oaf_theme_options',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'oaf_sanitize_options',
			)
		);

		add_settings_section( 'oaf_org', __( 'Organisation details', 'oaf-wp-blocktheme' ), '__return_false', 'oaf-theme' );
		oaf_add_field( 'org_name', __( 'Organisation name', 'oaf-wp-blocktheme' ), 'oaf_org' );
		oaf_add_field( 'abn', __( 'ABN', 'oaf-wp-blocktheme' ), 'oaf_org' );
		oaf_add_field( 'acnc_url', __( 'ACNC profile URL', 'oaf-wp-blocktheme' ), 'oaf_org', 'url' );
		oaf_add_field( 'abr_url', __( 'ABN register (ABR) URL', 'oaf-wp-blocktheme' ), 'oaf_org', 'url' );

		add_settings_section( 'oaf_social', __( 'Social profile links', 'oaf-wp-blocktheme' ), '__return_false', 'oaf-theme' );
		oaf_add_field( 'github_url', __( 'GitHub URL', 'oaf-wp-blocktheme' ), 'oaf_social', 'url' );
		oaf_add_field( 'bluesky_url', __( 'Bluesky URL', 'oaf-wp-blocktheme' ), 'oaf_social', 'url' );
		oaf_add_field( 'mastodon_url', __( 'Mastodon URL', 'oaf-wp-blocktheme' ), 'oaf_social', 'url' );
		oaf_add_field( 'linkedin_url', __( 'LinkedIn URL', 'oaf-wp-blocktheme' ), 'oaf_social', 'url' );

		add_settings_section( 'oaf_services', __( 'Sister-service URLs', 'oaf-wp-blocktheme' ), '__return_false', 'oaf-theme' );
		oaf_add_field( 'planningalerts_url', __( 'Planning Alerts URL', 'oaf-wp-blocktheme' ), 'oaf_services', 'url' );
		oaf_add_field( 'righttoknow_url', __( 'Right to Know URL', 'oaf-wp-blocktheme' ), 'oaf_services', 'url' );
		oaf_add_field( 'theyvoteforyou_url', __( 'They Vote for You URL', 'oaf-wp-blocktheme' ), 'oaf_services', 'url' );
		oaf_add_field( 'openaustralia_url', __( 'OpenAustralia.org.au URL', 'oaf-wp-blocktheme' ), 'oaf_services', 'url' );

		add_settings_section( 'oaf_ack', __( 'Acknowledgement of Country', 'oaf-wp-blocktheme' ), '__return_false', 'oaf-theme' );
		oaf_add_field( 'acknowledgement', __( 'Acknowledgement text', 'oaf-wp-blocktheme' ), 'oaf_ack', 'textarea' );

		add_settings_section(
			'oaf_raisely',
			__( 'Raisely donation form', 'oaf-wp-blocktheme' ),
			'oaf_raisely_section_intro',
			'oaf-theme'
		);
		oaf_add_field(
			'raisely_embed',
			__( 'Raisely embed code', 'oaf-wp-blocktheme' ),
			'oaf_raisely',
			'textarea',
			__( 'Paste the embed snippet from your Raisely campaign. It appears wherever the "Raisely Donation Form" block is placed (the Donate page by default).', 'oaf-wp-blocktheme' )
		);
	}
}
add_action( 'admin_init', 'oaf_register_settings' );

if ( ! function_exists( 'oaf_raisely_section_intro' ) ) {
	/**
	 * Short note above the Raisely field.
	 */
	function oaf_raisely_section_intro() {
		echo '<p>' . esc_html__( 'This code may contain scripts and is saved as-is, so only people allowed to publish unfiltered HTML can change it. Only paste embed code you trust from your own Raisely dashboard.', 'oaf-wp-blocktheme' ) . '</p>';
	}
}

if ( ! function_exists( 'oaf_add_field' ) ) {
	/**
	 * Register one settings field.
	 *
	 * @param string $key     Option key.
	 * @param string $label   Field label.
	 * @param string $section Section id.
	 * @param string $type    'text' (default), 'url' or 'textarea'.
	 * @param string $desc    Optional description shown under the field.
	 */
	function oaf_add_field( $key, $label, $section, $type = 'text', $desc = '' ) {
		add_settings_field(
			'oaf_field_' . $key,
			$label,
			'oaf_render_field',
			'oaf-theme',
			$section,
			array(
				'key'       => $key,
				'type'      => $type,
				'desc'      => $desc,
				'label_for' => 'oaf_field_' . $key,
			)
		);
	}
}

if ( ! function_exists( 'oaf_render_field' ) ) {
	/**
	 * Render a single settings field.
	 *
	 * @param array $args Field args from oaf_add_field().
	 */
	function oaf_render_field( $args ) {
		$key   = $args['key'];
		$id    = 'oaf_field_' . $key;
		$name  = 'oaf_theme_options[' . $key . ']';
		$value = oaf_option( $key );

		if ( 'textarea' === $args['type'] ) {
			printf(
				'<textarea id="%1$s" name="%2$s" class="large-text code" rows="5">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" class="regular-text" value="%4$s" />',
				'url' === $args['type'] ? 'url' : 'text',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
		}

		if ( ! empty( $args['desc'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
		}
	}
}

if ( ! function_exists( 'oaf_render_admin_page' ) ) {
	/**
	 * Render the theme admin screen.
	 */
	function oaf_render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'OAF Theme', 'oaf-wp-blocktheme' ); ?></h1>

			<?php
			// This screen lives under Appearance, so the "Settings saved" notice
			// is not shown automatically; render it ourselves.
			settings_errors();
			oaf_render_pages_section();
			?>

			<hr />

			<form action="options.php" method="post">
				<?php
				settings_fields( 'oaf_theme' );
				do_settings_sections( 'oaf-theme' );
				submit_button( __( 'Save settings', 'oaf-wp-blocktheme' ) );
				?>
			</form>
		</div>
		<?php
	}
}
