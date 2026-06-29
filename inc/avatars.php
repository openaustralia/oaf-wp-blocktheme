<?php
/**
 * Local avatars: replace Gravatar with the People section's images.
 *
 * The site does not use Gravatar. This file overrides WordPress avatar
 * resolution so any avatar (post-author byline, comments, admin) resolves to a
 * matching `oaf_person` featured image, falling back to a generated initials
 * circle that mirrors the People grid. A Gravatar URL is never requested.
 *
 * People are matched to WordPress users by display name.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_person_palette' ) ) {
	/**
	 * Brand palette used for initials circles, shared with the People grid.
	 *
	 * @return string[] List of hex colours.
	 */
	function oaf_person_palette() {
		return array( '#800000', '#3a4e72', '#ca3f94', '#03827a', '#428bca' );
	}
}

if ( ! function_exists( 'oaf_avatar_name_key' ) ) {
	/**
	 * Normalise a name for case-insensitive matching.
	 *
	 * @param string $name Full name.
	 * @return string
	 */
	function oaf_avatar_name_key( $name ) {
		return mb_strtolower( trim( (string) $name ) );
	}
}

if ( ! function_exists( 'oaf_avatar_color_for_name' ) ) {
	/**
	 * Pick a palette colour deterministically from a name, so the same person
	 * always gets the same colour wherever their avatar appears.
	 *
	 * @param string $name Full name.
	 * @return string Hex colour.
	 */
	function oaf_avatar_color_for_name( $name ) {
		$palette = oaf_person_palette();
		$index   = abs( crc32( oaf_avatar_name_key( $name ) ) ) % count( $palette );
		return $palette[ $index ];
	}
}

if ( ! function_exists( 'oaf_avatar_initials_svg' ) ) {
	/**
	 * Build an initials-circle avatar as an inline SVG data URI.
	 *
	 * @param string $name Full name.
	 * @param int    $size Pixel size.
	 * @return string `data:image/svg+xml;base64,…` URI.
	 */
	function oaf_avatar_initials_svg( $name, $size = 96 ) {
		$size     = max( 1, (int) $size );
		$initials = oaf_person_initials( $name );
		if ( '' === $initials ) {
			$initials = '?';
		}
		$color     = oaf_avatar_color_for_name( $name );
		$half      = $size / 2;
		$font_size = round( $size * 0.4, 2 );

		$svg = sprintf(
			'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 %1$d %1$d" role="img" aria-label="%2$s">'
				. '<circle cx="%3$s" cy="%3$s" r="%3$s" fill="%4$s"/>'
				. '<text x="50%%" y="50%%" dy=".1em" text-anchor="middle" dominant-baseline="central"'
				. ' font-family="-apple-system,BlinkMacSystemFont,&apos;Segoe UI&apos;,Roboto,Helvetica,Arial,sans-serif"'
				. ' font-size="%5$s" font-weight="700" fill="#ffffff">%6$s</text>'
				. '</svg>',
			$size,
			htmlspecialchars( (string) $name, ENT_QUOTES, 'UTF-8' ),
			$half,
			$color,
			$font_size,
			htmlspecialchars( $initials, ENT_QUOTES, 'UTF-8' )
		);

		// Encoded for transport in an img `src`, not to obscure the markup.
		return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
}

if ( ! function_exists( 'oaf_people_name_map' ) ) {
	/**
	 * Map of normalised person name => post ID, built once per request.
	 *
	 * @return array<string,int>
	 */
	function oaf_people_name_map() {
		static $map = null;
		if ( null !== $map ) {
			return $map;
		}

		$map   = array();
		$query = new WP_Query(
			array(
				'post_type'      => 'oaf_person',
				'post_status'    => 'publish',
				'posts_per_page' => (int) apply_filters( 'oaf_people_grid_max', 200 ),
				'no_found_rows'  => true,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);

		foreach ( $query->posts as $person ) {
			$key = oaf_avatar_name_key( $person->post_title );
			if ( '' !== $key && ! isset( $map[ $key ] ) ) {
				$map[ $key ] = (int) $person->ID;
			}
		}
		wp_reset_postdata();

		return $map;
	}
}

if ( ! function_exists( 'oaf_find_person_id_by_name' ) ) {
	/**
	 * Find a person's post ID by display name.
	 *
	 * @param string $name Full name.
	 * @return int Post ID, or 0 if no match.
	 */
	function oaf_find_person_id_by_name( $name ) {
		$map = oaf_people_name_map();
		$key = oaf_avatar_name_key( $name );
		return isset( $map[ $key ] ) ? $map[ $key ] : 0;
	}
}

if ( ! function_exists( 'oaf_resolve_avatar_name' ) ) {
	/**
	 * Resolve the name behind whatever get_avatar_data() was given.
	 *
	 * @param mixed $id_or_email User ID, email, WP_User, WP_Post or WP_Comment.
	 * @return string Display name, or '' if it cannot be resolved.
	 */
	function oaf_resolve_avatar_name( $id_or_email ) {
		$user = false;

		if ( $id_or_email instanceof WP_User ) {
			$user = $id_or_email;
		} elseif ( $id_or_email instanceof WP_Post ) {
			$user = get_user_by( 'id', (int) $id_or_email->post_author );
		} elseif ( $id_or_email instanceof WP_Comment ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$user = get_user_by( 'id', (int) $id_or_email->user_id );
			}
			if ( ( ! $user || is_wp_error( $user ) ) && ! empty( $id_or_email->comment_author ) ) {
				return $id_or_email->comment_author;
			}
		} elseif ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( is_string( $id_or_email ) && str_contains( $id_or_email, '@' ) && ! str_contains( $id_or_email, 'gravatar.com' ) ) {
			$user = get_user_by( 'email', $id_or_email );
		}

		return ( $user && ! is_wp_error( $user ) ) ? $user->display_name : '';
	}
}

if ( ! function_exists( 'oaf_filter_avatar_data' ) ) {
	/**
	 * Resolve avatars to a local image, never Gravatar.
	 *
	 * Matching person with a featured image -> that photo; otherwise a generated
	 * initials circle. Setting 'url' short-circuits the Gravatar lookup.
	 *
	 * @param array $args        Avatar data arguments.
	 * @param mixed $id_or_email Avatar subject.
	 * @return array
	 */
	function oaf_filter_avatar_data( $args, $id_or_email ) {
		if ( isset( $args['url'] ) ) {
			return $args;
		}

		$name      = oaf_resolve_avatar_name( $id_or_email );
		$size      = isset( $args['size'] ) ? (int) $args['size'] : 96;
		$person_id = ( '' !== $name ) ? oaf_find_person_id_by_name( $name ) : 0;

		$url = '';
		if ( $person_id && has_post_thumbnail( $person_id ) ) {
			$url = get_the_post_thumbnail_url( $person_id, 'thumbnail' );
		}
		if ( ! $url ) {
			$url = oaf_avatar_initials_svg( $name, $size );
		}

		$args['url']          = $url;
		$args['found_avatar'] = true;

		return $args;
	}
}
add_filter( 'pre_get_avatar_data', 'oaf_filter_avatar_data', 10, 2 );

if ( ! function_exists( 'oaf_render_data_uri_avatar' ) ) {
	/**
	 * Re-emit data-URI avatars that core's esc_url() would otherwise strip.
	 *
	 * Core's get_avatar() escapes the src with esc_url(), which drops `data:`
	 * URIs because `data` is not an allowed protocol. Our initials circles are
	 * inline SVG data URIs, so for those we rebuild the <img> with esc_attr()
	 * instead. Real photo URLs pass esc_url() fine and are left untouched.
	 *
	 * @param string $avatar        Avatar HTML built by core (empty src for ours).
	 * @param mixed  $id_or_email   Avatar subject (unused).
	 * @param int    $size          Avatar size in pixels.
	 * @param string $default_value Default avatar (unused).
	 * @param string $alt           Alt text.
	 * @param array  $args          Processed avatar args, including our 'url'.
	 * @return string
	 */
	function oaf_render_data_uri_avatar( $avatar, $id_or_email, $size, $default_value, $alt, $args ) {
		if ( empty( $args['url'] ) || ! is_string( $args['url'] ) || ! str_starts_with( $args['url'], 'data:' ) ) {
			return $avatar;
		}
		$size = (int) $size;
		return sprintf(
			'<img alt="%1$s" src="%2$s" class="avatar avatar-%3$d photo" height="%3$d" width="%3$d" decoding="async" loading="lazy" />',
			esc_attr( $alt ),
			esc_attr( $args['url'] ),
			$size
		);
	}
}
add_filter( 'get_avatar', 'oaf_render_data_uri_avatar', 10, 6 );
