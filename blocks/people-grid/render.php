<?php
/**
 * Server render for the People Grid block.
 *
 * Queries the `oaf_person` post type (optionally filtered to one group) and
 * outputs the theme's people-grid markup. Avatar = featured image when set,
 * otherwise a coloured initials circle (colour cycled from the brand palette).
 *
 * @package oaf-wp-blocktheme
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oaf_group = isset( $attributes['group'] ) ? sanitize_title( $attributes['group'] ) : '';

$oaf_args = array(
	'post_type'      => 'oaf_person',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => array(
		'menu_order' => 'ASC',
		'title'      => 'ASC',
	),
);

if ( '' !== $oaf_group ) {
	$oaf_args['tax_query'] = array(
		array(
			'taxonomy' => 'oaf_person_group',
			'field'    => 'slug',
			'terms'    => $oaf_group,
		),
	);
}

$oaf_people  = new WP_Query( $oaf_args );
$oaf_wrapper = get_block_wrapper_attributes( array( 'class' => 'oaf-people-grid' ) );

if ( ! $oaf_people->have_posts() ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf(
			'<div %1$s><p>%2$s</p></div>',
			$oaf_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped wrapper.
			esc_html__( 'No people in this group yet. Add them under People.', 'oaf-wp-blocktheme' )
		);
	}
	return;
}

$oaf_palette = array( '#800000', '#3a4e72', '#ca3f94', '#03827a', '#428bca' );

echo '<div ' . $oaf_wrapper . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped wrapper.

foreach ( $oaf_people->posts as $oaf_index => $oaf_person ) {
	$oaf_name = get_the_title( $oaf_person );
	$oaf_role = get_post_meta( $oaf_person->ID, '_oaf_role', true );
	$oaf_bio  = wp_strip_all_tags( $oaf_person->post_content );

	if ( has_post_thumbnail( $oaf_person ) ) {
		$oaf_avatar = '<span class="oaf-avatar oaf-avatar--photo">'
			. get_the_post_thumbnail( $oaf_person->ID, 'thumbnail', array( 'alt' => esc_attr( $oaf_name ) ) )
			. '</span>';
	} else {
		$oaf_color  = $oaf_palette[ $oaf_index % count( $oaf_palette ) ];
		$oaf_avatar = sprintf(
			'<span class="oaf-avatar" style="background:%1$s" aria-hidden="true">%2$s</span>',
			esc_attr( $oaf_color ),
			esc_html( oaf_person_initials( $oaf_name ) )
		);
	}

	echo '<div class="oaf-person">';
	echo $oaf_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts / core thumbnail.
	echo '<div>';
	printf( '<div class="oaf-person__name">%s</div>', esc_html( $oaf_name ) );
	if ( '' !== $oaf_role ) {
		printf( '<div class="oaf-person__role">%s</div>', esc_html( $oaf_role ) );
	}
	if ( '' !== $oaf_bio ) {
		printf( '<p class="oaf-person__bio">%s</p>', esc_html( $oaf_bio ) );
	}
	echo '</div></div>';
}

echo '</div>';

wp_reset_postdata();
