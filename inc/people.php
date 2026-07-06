<?php
/**
 * People: a custom post type for staff and board members.
 *
 * Registers the `oaf_person` post type (Admin -> People), an `oaf_person_group`
 * taxonomy (Team / Board), a Role field, and helpers to seed the default groups
 * and the original design's people on first setup. The People page renders these
 * via the `oaf/people-grid` block.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_register_people' ) ) {
	/**
	 * Register the People post type and its Group taxonomy.
	 */
	function oaf_register_people() {
		register_post_type(
			'oaf_person',
			array(
				'labels'        => array(
					'name'          => __( 'People', 'oaf-wp-blocktheme' ),
					'singular_name' => __( 'Person', 'oaf-wp-blocktheme' ),
					'menu_name'     => __( 'People', 'oaf-wp-blocktheme' ),
					'all_items'     => __( 'All People', 'oaf-wp-blocktheme' ),
					'add_new'       => __( 'Add New', 'oaf-wp-blocktheme' ),
					'add_new_item'  => __( 'Add New Person', 'oaf-wp-blocktheme' ),
					'edit_item'     => __( 'Edit Person', 'oaf-wp-blocktheme' ),
					'new_item'      => __( 'New Person', 'oaf-wp-blocktheme' ),
					'view_item'     => __( 'View Person', 'oaf-wp-blocktheme' ),
					'search_items'  => __( 'Search People', 'oaf-wp-blocktheme' ),
					'not_found'     => __( 'No people found.', 'oaf-wp-blocktheme' ),
				),
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-groups',
				'menu_position' => 25,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'has_archive'   => false,
				'rewrite'       => false,
			)
		);

		register_taxonomy(
			'oaf_person_group',
			'oaf_person',
			array(
				'labels'            => array(
					'name'          => __( 'Groups', 'oaf-wp-blocktheme' ),
					'singular_name' => __( 'Group', 'oaf-wp-blocktheme' ),
					'menu_name'     => __( 'Groups', 'oaf-wp-blocktheme' ),
					'all_items'     => __( 'All Groups', 'oaf-wp-blocktheme' ),
					'edit_item'     => __( 'Edit Group', 'oaf-wp-blocktheme' ),
					'add_new_item'  => __( 'Add New Group', 'oaf-wp-blocktheme' ),
					'search_items'  => __( 'Search Groups', 'oaf-wp-blocktheme' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => false,
			)
		);

		oaf_seed_person_terms();
	}
}
add_action( 'init', 'oaf_register_people' );

if ( ! function_exists( 'oaf_person_groups' ) ) {
	/**
	 * The default groups, keyed by slug.
	 *
	 * @return array<string,string>
	 */
	function oaf_person_groups() {
		return array(
			'team'               => __( 'Team', 'oaf-wp-blocktheme' ),
			'board-of-directors' => __( 'Board of directors', 'oaf-wp-blocktheme' ),
		);
	}
}

if ( ! function_exists( 'oaf_seed_person_terms' ) ) {
	/**
	 * Ensure the default Team / Board groups exist (runs once).
	 */
	function oaf_seed_person_terms() {
		if ( get_option( 'oaf_person_terms_seeded' ) ) {
			return;
		}
		foreach ( oaf_person_groups() as $slug => $name ) {
			if ( ! term_exists( $slug, 'oaf_person_group' ) ) {
				wp_insert_term( $name, 'oaf_person_group', array( 'slug' => $slug ) );
			}
		}
		update_option( 'oaf_person_terms_seeded', 1 );
	}
}

if ( ! function_exists( 'oaf_register_person_meta' ) ) {
	/**
	 * Register the Role meta and its edit box.
	 */
	function oaf_register_person_meta() {
		add_meta_box(
			'oaf_person_role',
			__( 'Role', 'oaf-wp-blocktheme' ),
			'oaf_person_role_box',
			'oaf_person',
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'oaf_register_person_meta' );

if ( ! function_exists( 'oaf_person_role_box' ) ) {
	/**
	 * Render the Role meta box.
	 *
	 * @param WP_Post $post Current person.
	 */
	function oaf_person_role_box( $post ) {
		wp_nonce_field( 'oaf_person_role_save', 'oaf_person_role_nonce' );
		$role = get_post_meta( $post->ID, '_oaf_role', true );
		printf(
			'<p><label class="screen-reader-text" for="oaf_person_role_field">%1$s</label>'
				. '<input type="text" id="oaf_person_role_field" name="oaf_person_role" value="%2$s" class="widefat" placeholder="%3$s" /></p>',
			esc_html__( 'Role', 'oaf-wp-blocktheme' ),
			esc_attr( $role ),
			esc_attr__( 'e.g. Chief Executive Officer', 'oaf-wp-blocktheme' )
		);
	}
}

if ( ! function_exists( 'oaf_save_person_role' ) ) {
	/**
	 * Persist the Role meta on save.
	 *
	 * @param int $post_id Person ID.
	 */
	function oaf_save_person_role( $post_id ) {
		if ( ! isset( $_POST['oaf_person_role_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['oaf_person_role_nonce'] ), 'oaf_person_role_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$role = isset( $_POST['oaf_person_role'] ) ? sanitize_text_field( wp_unslash( $_POST['oaf_person_role'] ) ) : '';
		update_post_meta( $post_id, '_oaf_role', $role );
	}
}
add_action( 'save_post_oaf_person', 'oaf_save_person_role' );

if ( ! function_exists( 'oaf_person_columns' ) ) {
	/**
	 * Add Photo and Role columns to the People list table. The Photo and Role
	 * cells double as the data source that Quick Edit reads from via JS, and the
	 * Role column is what triggers the `quick_edit_custom_box` hook below.
	 *
	 * @param array<string,string> $columns Existing columns.
	 * @return array<string,string>
	 */
	function oaf_person_columns( $columns ) {
		$reordered = array();
		foreach ( $columns as $key => $label ) {
			if ( 'title' === $key ) {
				$reordered['oaf_photo'] = __( 'Photo', 'oaf-wp-blocktheme' );
			}
			$reordered[ $key ] = $label;
			if ( 'title' === $key ) {
				$reordered['oaf_role'] = __( 'Role', 'oaf-wp-blocktheme' );
			}
		}
		return $reordered;
	}
}
add_filter( 'manage_oaf_person_posts_columns', 'oaf_person_columns' );

if ( ! function_exists( 'oaf_person_column_content' ) ) {
	/**
	 * Render the Photo and Role columns. Quick Edit reads the Role text and the
	 * thumbnail attachment id (only present when a photo is set) from here.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Person ID.
	 */
	function oaf_person_column_content( $column, $post_id ) {
		if ( 'oaf_photo' === $column ) {
			if ( has_post_thumbnail( $post_id ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail() returns safe core markup.
				echo get_the_post_thumbnail( $post_id, array( 40, 40 ) );
				printf( '<span class="oaf-thumb-id hidden">%d</span>', (int) get_post_thumbnail_id( $post_id ) );
			} else {
				echo '&mdash;';
			}
		} elseif ( 'oaf_role' === $column ) {
			$role = get_post_meta( $post_id, '_oaf_role', true );
			if ( '' === $role ) {
				echo '&mdash;';
			} else {
				printf( '<span class="oaf-role-text">%s</span>', esc_html( $role ) );
			}
		}
	}
}
add_action( 'manage_oaf_person_posts_custom_column', 'oaf_person_column_content', 10, 2 );

if ( ! function_exists( 'oaf_person_quick_edit_box' ) ) {
	/**
	 * Render the Role field and featured-image control inside Quick Edit.
	 *
	 * Fires once per custom column; we render everything under the Role column so
	 * the fieldset appears a single time. The values are blank here and populated
	 * from the row by assets/js/quick-edit-person.js when the editor opens.
	 *
	 * @param string $column    Column key.
	 * @param string $post_type Post type slug.
	 */
	function oaf_person_quick_edit_box( $column, $post_type ) {
		if ( 'oaf_person' !== $post_type || 'oaf_role' !== $column ) {
			return;
		}
		wp_nonce_field( 'oaf_person_quick_edit', 'oaf_person_quick_edit_nonce' );
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<label class="inline-edit-group">
					<span class="title"><?php esc_html_e( 'Role', 'oaf-wp-blocktheme' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" name="oaf_person_role" class="oaf-quick-role" value="" />
					</span>
				</label>
				<div class="inline-edit-group oaf-quick-photo">
					<span class="title"><?php esc_html_e( 'Photo', 'oaf-wp-blocktheme' ); ?></span>
					<span class="oaf-quick-photo-preview"></span>
					<button type="button" class="button oaf-quick-photo-set"><?php esc_html_e( 'Set photo', 'oaf-wp-blocktheme' ); ?></button>
					<button type="button" class="button-link oaf-quick-photo-remove"><?php esc_html_e( 'Remove', 'oaf-wp-blocktheme' ); ?></button>
					<input type="hidden" name="oaf_thumbnail_id" class="oaf-quick-photo-id" value="" />
				</div>
			</div>
		</fieldset>
		<?php
	}
}
add_action( 'quick_edit_custom_box', 'oaf_person_quick_edit_box', 10, 2 );

if ( ! function_exists( 'oaf_save_person_quick_edit' ) ) {
	/**
	 * Persist the Role and featured image from Quick Edit.
	 *
	 * Quick Edit's inline-save routes through edit_post(), which handles neither
	 * post meta nor the featured image, so we save both here. The thumbnail uses
	 * core's convention: a positive id sets it, -1 removes it, and an empty value
	 * (e.g. if the JS never ran) is left untouched so photos are never lost.
	 *
	 * @param int $post_id Person ID.
	 */
	function oaf_save_person_quick_edit( $post_id ) {
		if ( ! isset( $_POST['oaf_person_quick_edit_nonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_POST['oaf_person_quick_edit_nonce'] ), 'oaf_person_quick_edit' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['oaf_person_role'] ) ) {
			update_post_meta( $post_id, '_oaf_role', sanitize_text_field( wp_unslash( $_POST['oaf_person_role'] ) ) );
		}
		if ( isset( $_POST['oaf_thumbnail_id'] ) ) {
			$thumb = (int) wp_unslash( $_POST['oaf_thumbnail_id'] );
			if ( $thumb > 0 ) {
				set_post_thumbnail( $post_id, $thumb );
			} elseif ( -1 === $thumb ) {
				delete_post_thumbnail( $post_id );
			}
		}
	}
}
add_action( 'save_post_oaf_person', 'oaf_save_person_quick_edit' );

if ( ! function_exists( 'oaf_person_admin_assets' ) ) {
	/**
	 * Enqueue the media library and Quick Edit script on the People list screen.
	 *
	 * @param string $hook Current admin page.
	 */
	function oaf_person_admin_assets( $hook ) {
		if ( 'edit.php' !== $hook ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || 'oaf_person' !== $screen->post_type ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script(
			'oaf-quick-edit-person',
			get_template_directory_uri() . '/assets/js/quick-edit-person.js',
			array( 'jquery', 'inline-edit-post' ),
			wp_get_theme()->get( 'Version' ),
			true
		);
		wp_localize_script(
			'oaf-quick-edit-person',
			'oafQuickEditPerson',
			array(
				'mediaTitle'  => __( 'Select photo', 'oaf-wp-blocktheme' ),
				'mediaButton' => __( 'Use this photo', 'oaf-wp-blocktheme' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'oaf_person_admin_assets' );

if ( ! function_exists( 'oaf_person_initials' ) ) {
	/**
	 * Up to two uppercase initials derived from a name.
	 *
	 * @param string $name Full name.
	 * @return string
	 */
	function oaf_person_initials( $name ) {
		$initials = '';
		foreach ( preg_split( '/\s+/', trim( $name ) ) as $part ) {
			if ( '' === $part ) {
				continue;
			}
			$initials .= mb_strtoupper( mb_substr( $part, 0, 1 ) );
			if ( mb_strlen( $initials ) >= 2 ) {
				break;
			}
		}
		return $initials;
	}
}

if ( ! function_exists( 'oaf_seed_people' ) ) {
	/**
	 * Seed the original design's people, but only when none exist yet, so the
	 * People page renders identically after switching to the dynamic block.
	 *
	 * @return int Number of people created.
	 */
	function oaf_seed_people() {
		$existing = get_posts(
			array(
				'post_type'      => 'oaf_person',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $existing ) ) {
			return 0;
		}

		oaf_seed_person_terms();

		$people = array(
			array( 'team', 'Ben Fairless', 'Chief Executive Officer', 'Leading the foundation, looking after the people who build the tools and services, and finding sustainable ways to fund work that belongs to everyone.' ),
			array( 'team', 'James Polley', 'Chief Financial Officer', 'Financial management, budgeting, and reporting; making sure there is enough in the bank to keep the lights on.' ),
			array( 'team', 'Ian Heggie', 'Senior Developer', 'Software expert who keeps services running smoothly, while developing new features to further our mission.' ),
			array( 'team', 'Emanuele Muratore', 'Planning Alerts Lead', 'Overseeing the Planning Alerts API service and working to ensure business customers get the most value from our planning data.' ),
			array( 'team', 'Brenda Wallace', 'Civil Disobedience Kiwi', 'Turning tangles of complexity into clean simplicity, drawing on years building civic technology and government services across the ditch.' ),
			array( 'board-of-directors', 'Matthew Landauer', 'Co-Founder and Director', 'Co-founded the OpenAustralia Foundation in 2007. Developed early initiatives and code. Responsible for directing the foundation for most of its early years.' ),
			array( 'board-of-directors', 'Katherine Szuminska', 'Co-Founder and Director', 'Co-founded the OpenAustralia Foundation in 2007. Established constitution and charity status, negotiated content licensing with Parliament. Shaped design and content, and represented OAF on open government internationally.' ),
			array( 'board-of-directors', 'Donna Benjamin', 'Chair of the Board', 'Open-source community leadership and governance from Red Hat, the Drupal Association and Linux Australia.' ),
			array( 'board-of-directors', 'Sae Ra Germaine', 'Director', 'Leadership in technology and the open-source community at CAVAL, VALA and Linux Australia.' ),
		);

		$order = array();
		$count = 0;
		foreach ( $people as $person ) {
			list( $group, $name, $role, $bio ) = $person;

			$order[ $group ] = isset( $order[ $group ] ) ? $order[ $group ] + 1 : 1;

			$id = wp_insert_post(
				array(
					'post_type'    => 'oaf_person',
					'post_status'  => 'publish',
					'post_title'   => $name,
					// Store the bio as a Paragraph block, not raw text, so the
					// block editor opens it cleanly instead of as a Classic block.
					'post_content' => '<!-- wp:paragraph -->' . "\n" . '<p>' . $bio . '</p>' . "\n" . '<!-- /wp:paragraph -->',
					'menu_order'   => $order[ $group ],
				)
			);

			if ( $id && ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_oaf_role', $role );
				wp_set_object_terms( $id, $group, 'oaf_person_group' );
				++$count;
			}
		}

		return $count;
	}
}
