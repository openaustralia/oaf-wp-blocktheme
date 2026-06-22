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
				'labels'       => array(
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
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-groups',
				'menu_position' => 25,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'has_archive'  => false,
				'rewrite'      => false,
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
			esc_attr__( 'e.g. Acting Executive Officer', 'oaf-wp-blocktheme' )
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
			array( 'team', 'Ben Fairless', 'Acting Executive Officer', 'Looks after day-to-day operations, partnerships and the books. Has contributed to OAF’s services for many years.' ),
			array( 'team', 'James Polley', 'Contributor', 'Helps keep OAF’s services online and running reliably.' ),
			array( 'team', 'Ian Heggie', 'Contributor', 'Works on the systems behind OAF’s public services.' ),
			array( 'team', 'Mackay Ash', 'Contributor', 'Helps maintain and improve OAF’s services.' ),
			array( 'team', 'Brenda Wallace', 'Contributor', 'Supports OAF’s work across its public services.' ),
			array( 'board-of-directors', 'Matthew Landauer', 'Director & co-founder', 'Co-founded the foundation in 2007 after the launch of TheyWorkForYou in the UK. Built the first OpenAustralia prototype.' ),
			array( 'board-of-directors', 'Katherine Szuminska', 'Director & co-founder', 'Co-founded the foundation and has guided its public-interest mission since the beginning.' ),
			array( 'board-of-directors', 'Donna Benjamin', 'Director', 'Brings open-source community and governance experience to the board.' ),
			array( 'board-of-directors', 'Sae Ra Germaine', 'Director', 'Brings not-for-profit governance and community leadership experience to the board.' ),
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
				$count++;
			}
		}

		return $count;
	}
}
