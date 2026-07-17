<?php
/**
 * Contributors: the volunteer open-source contributors shown on the People page.
 *
 * Fetches contributors for the OpenAustralia Foundation project repositories from
 * the GitHub API, merges them into a single de-duplicated list (one entry per
 * person, contributions summed across projects), filters out bot accounts, and
 * caches the result. Avatars are downloaded and self-hosted under the uploads
 * directory so that rendering the People page makes no third-party request and
 * leaks no visitor IP to GitHub. The `oaf/contributors` block renders the cache.
 *
 * @package oaf-wp-blocktheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oaf_contributor_repos' ) ) {
	/**
	 * The repositories whose contributors are thanked on the People page.
	 *
	 * Mirrors the org profile README (openaustralia/.github). All live under the
	 * `openaustralia` organisation. Filterable so the list can be trimmed or
	 * extended without editing the theme.
	 *
	 * @return string[] List of `owner/repo` slugs.
	 */
	function oaf_contributor_repos() {
		$repos = array(
			'openaustralia/planningalerts',
			'openaustralia/righttoknow',
			'openaustralia/theyvoteforyou',
			'openaustralia/openaustralia',
			'openaustralia/twfy',
			'openaustralia/openaustralia-parser',
			'openaustralia/perllib',
			'openaustralia/phplib',
			'openaustralia/rblib',
			'openaustralia/shlib',
			'openaustralia/morph',
		);

		/**
		 * Filter the list of repositories used for the contributors grid.
		 *
		 * @param string[] $repos List of `owner/repo` slugs.
		 */
		return (array) apply_filters( 'oaf_contributor_repos', $repos );
	}
}

if ( ! function_exists( 'oaf_contributor_exclude_logins' ) ) {
	/**
	 * GitHub logins to exclude from the grid, in addition to the automatic
	 * `[bot]` and account-type filtering. Matched case-insensitively.
	 *
	 * @return string[] Lower-case logins to exclude.
	 */
	function oaf_contributor_exclude_logins() {
		$logins = array(
			'dependabot',
			'dependabot-preview',
			'github-actions',
			'renovate',
			'renovate-bot',
			'imgbot',
			'weblate',
		);

		/**
		 * Filter the list of contributor logins to exclude from the grid.
		 *
		 * @param string[] $logins Logins to exclude (compared lower-case).
		 */
		$logins = (array) apply_filters( 'oaf_contributor_exclude_logins', $logins );

		return array_map( 'strtolower', $logins );
	}
}

if ( ! function_exists( 'oaf_contributors_token' ) ) {
	/**
	 * Optional GitHub token, used only to raise the API rate limit. Not required
	 * for public repositories at the weekly refresh cadence.
	 *
	 * @return string Token, or '' when none is configured.
	 */
	function oaf_contributors_token() {
		$token = defined( 'OAF_GITHUB_TOKEN' ) ? (string) OAF_GITHUB_TOKEN : '';

		/**
		 * Filter the GitHub token used for contributor requests.
		 *
		 * @param string $token Bearer token, or '' for unauthenticated requests.
		 */
		return (string) apply_filters( 'oaf_contributors_token', $token );
	}
}

if ( ! function_exists( 'oaf_get_contributors' ) ) {
	/**
	 * Return the cached, de-duplicated contributors for rendering.
	 *
	 * Never blocks the current request on the network: when the cache is empty or
	 * stale it schedules an asynchronous refresh (via WP-Cron) and returns
	 * whatever is currently cached. A short cooldown stops a failing fetch from
	 * being retried on every request.
	 *
	 * @return array<int,array{login:string,profile:string,avatar:string,contributions:int}>
	 */
	function oaf_get_contributors() {
		$cache   = get_option( 'oaf_contributors_cache', array() );
		$people  = ( isset( $cache['people'] ) && is_array( $cache['people'] ) ) ? $cache['people'] : array();
		$updated = isset( $cache['updated'] ) ? (int) $cache['updated'] : 0;

		/**
		 * Filter how long (seconds) the contributors cache is considered fresh.
		 *
		 * @param int $ttl Time to live in seconds.
		 */
		$ttl = (int) apply_filters( 'oaf_contributors_ttl', WEEK_IN_SECONDS );

		$is_empty = empty( $people );
		$is_stale = ( time() - $updated ) > $ttl;
		$busy     = get_transient( 'oaf_contributors_refreshing' ) || get_transient( 'oaf_contributors_cooldown' );

		if ( ( $is_empty || $is_stale ) && ! $busy ) {
			// Serve what we have now; refresh out of band so no visitor waits on
			// GitHub. WP-Cron picks this up on the next request's loopback.
			set_transient( 'oaf_contributors_refreshing', 1, 10 * MINUTE_IN_SECONDS );
			if ( ! wp_next_scheduled( 'oaf_refresh_contributors' ) ) {
				wp_schedule_single_event( time(), 'oaf_refresh_contributors' );
			}
		}

		return $people;
	}
}

if ( ! function_exists( 'oaf_refresh_contributors' ) ) {
	/**
	 * Fetch, merge, de-duplicate and cache contributors, downloading avatars.
	 *
	 * Runs on WP-Cron (see the scheduling below). If every repository request
	 * fails the previous cache is kept untouched, so a GitHub outage never blanks
	 * the page.
	 *
	 * @return void
	 */
	function oaf_refresh_contributors() {
		// Guard against a stampede and against retrying a hard failure every request.
		set_transient( 'oaf_contributors_refreshing', 1, 10 * MINUTE_IN_SECONDS );
		set_transient( 'oaf_contributors_cooldown', 1, HOUR_IN_SECONDS );

		$exclude = oaf_contributor_exclude_logins();

		// Remember previous avatars so we can reuse them if a download fails.
		$previous     = get_option( 'oaf_contributors_cache', array() );
		$prev_people  = ( isset( $previous['people'] ) && is_array( $previous['people'] ) ) ? $previous['people'] : array();
		$prev_avatars = array();
		foreach ( $prev_people as $prev_person ) {
			if ( ! empty( $prev_person['login'] ) ) {
				$prev_avatars[ strtolower( $prev_person['login'] ) ] = isset( $prev_person['avatar'] ) ? $prev_person['avatar'] : '';
			}
		}

		$merged      = array();
		$any_success = false;

		foreach ( oaf_contributor_repos() as $repo ) {
			$list = oaf_fetch_repo_contributors( $repo );
			if ( null === $list ) {
				continue;
			}
			$any_success = true;

			foreach ( $list as $contributor ) {
				if ( ! isset( $contributor['type'] ) || 'User' !== $contributor['type'] ) {
					continue;
				}
				$login = isset( $contributor['login'] ) ? (string) $contributor['login'] : '';
				if ( '' === $login || preg_match( '/\[bot\]$/i', $login ) ) {
					continue;
				}
				$key = strtolower( $login );
				if ( in_array( $key, $exclude, true ) ) {
					continue;
				}

				$contributions = isset( $contributor['contributions'] ) ? (int) $contributor['contributions'] : 0;

				if ( isset( $merged[ $key ] ) ) {
					$merged[ $key ]['contributions'] += $contributions;
					continue;
				}

				$merged[ $key ] = array(
					'login'         => $login,
					'profile'       => isset( $contributor['html_url'] ) ? esc_url_raw( $contributor['html_url'] ) : 'https://github.com/' . rawurlencode( $login ),
					'avatar_url'    => isset( $contributor['avatar_url'] ) ? esc_url_raw( $contributor['avatar_url'] ) : '',
					'contributions' => $contributions,
				);
			}
		}

		// Every request failed (e.g. rate-limited or offline): keep the old cache.
		if ( ! $any_success ) {
			delete_transient( 'oaf_contributors_refreshing' );
			return;
		}

		uasort( $merged, 'oaf_contributors_compare' );

		/**
		 * Filter the merged contributor list before avatars are downloaded.
		 *
		 * @param array<string,array> $merged Keyed by lower-case login.
		 */
		$merged = (array) apply_filters( 'oaf_contributors_people', $merged );

		$people = array();
		foreach ( $merged as $key => $person ) {
			$avatar = ( '' !== $person['avatar_url'] ) ? oaf_cache_contributor_avatar( $person['login'], $person['avatar_url'] ) : '';
			if ( '' === $avatar && isset( $prev_avatars[ $key ] ) ) {
				$avatar = $prev_avatars[ $key ];
			}

			$people[] = array(
				'login'         => $person['login'],
				'profile'       => $person['profile'],
				'avatar'        => $avatar,
				'contributions' => (int) $person['contributions'],
			);
		}

		update_option(
			'oaf_contributors_cache',
			array(
				'updated' => time(),
				'people'  => $people,
			),
			false
		);

		delete_transient( 'oaf_contributors_refreshing' );
	}
}

if ( ! function_exists( 'oaf_contributors_compare' ) ) {
	/**
	 * Sort contributors by contributions (desc), then login (asc) as a tiebreak.
	 *
	 * @param array $a First contributor.
	 * @param array $b Second contributor.
	 * @return int
	 */
	function oaf_contributors_compare( $a, $b ) {
		if ( $a['contributions'] === $b['contributions'] ) {
			return strcasecmp( $a['login'], $b['login'] );
		}
		return $b['contributions'] - $a['contributions'];
	}
}

if ( ! function_exists( 'oaf_fetch_repo_contributors' ) ) {
	/**
	 * Fetch the full contributor list for a single repository from the GitHub API.
	 *
	 * @param string $repo `owner/repo` slug.
	 * @return array<int,array>|null Contributor records, or null when the request failed.
	 */
	function oaf_fetch_repo_contributors( $repo ) {
		$headers = array(
			'Accept'               => 'application/vnd.github+json',
			'User-Agent'           => 'oaf-wp-blocktheme',
			'X-GitHub-Api-Version' => '2022-11-28',
		);
		$token   = oaf_contributors_token();
		if ( '' !== $token ) {
			$headers['Authorization'] = 'Bearer ' . $token;
		}

		/**
		 * Filter the maximum number of API pages fetched per repository.
		 *
		 * @param int $max_pages Page cap (100 contributors per page).
		 */
		$max_pages = (int) apply_filters( 'oaf_contributors_max_pages', 5 );

		$all  = array();
		$page = 1;

		do {
			$url = add_query_arg(
				array(
					'per_page' => 100,
					'page'     => $page,
					'anon'     => 'false',
				),
				'https://api.github.com/repos/' . $repo . '/contributors'
			);

			$response = wp_remote_get(
				$url,
				array(
					'timeout' => 15,
					'headers' => $headers,
				)
			);

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				return null;
			}

			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! is_array( $body ) ) {
				return null;
			}

			$all   = array_merge( $all, $body );
			$count = count( $body );
			++$page;
		} while ( 100 === $count && $page <= $max_pages );

		return $all;
	}
}

if ( ! function_exists( 'oaf_contributors_upload_dir' ) ) {
	/**
	 * The uploads sub-directory that holds cached avatars, created if needed.
	 *
	 * @return array{path:string,url:string}|array{} Path and URL, or empty on failure.
	 */
	function oaf_contributors_upload_dir() {
		$uploads = wp_upload_dir();
		if ( ! empty( $uploads['error'] ) ) {
			return array();
		}

		$path = trailingslashit( $uploads['basedir'] ) . 'oaf-contributors';
		if ( ! is_dir( $path ) && ! wp_mkdir_p( $path ) ) {
			return array();
		}

		return array(
			'path' => $path,
			'url'  => trailingslashit( $uploads['baseurl'] ) . 'oaf-contributors',
		);
	}
}

if ( ! function_exists( 'oaf_contributors_ext_from_mime' ) ) {
	/**
	 * Map an image MIME type to a file extension we are willing to store.
	 *
	 * @param string $mime Content-Type header value.
	 * @return string Extension without a dot, or '' if not an accepted image.
	 */
	function oaf_contributors_ext_from_mime( $mime ) {
		$mime = strtolower( trim( (string) strtok( (string) $mime, ';' ) ) );
		$map  = array(
			'image/jpeg' => 'jpg',
			'image/png'  => 'png',
			'image/gif'  => 'gif',
			'image/webp' => 'webp',
		);
		return isset( $map[ $mime ] ) ? $map[ $mime ] : '';
	}
}

if ( ! function_exists( 'oaf_cache_contributor_avatar' ) ) {
	/**
	 * Download a contributor's avatar and store it locally.
	 *
	 * @param string $login      GitHub login (used for the filename).
	 * @param string $avatar_url Remote avatar URL.
	 * @return string Local URL of the stored avatar, or '' on failure.
	 */
	function oaf_cache_contributor_avatar( $login, $avatar_url ) {
		$dir = oaf_contributors_upload_dir();
		if ( empty( $dir ) ) {
			return '';
		}

		// Request a fixed size so the stored file is small and crisp at 72px @2x.
		$url      = add_query_arg( 's', 144, $avatar_url );
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array( 'User-Agent' => 'oaf-wp-blocktheme' ),
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return '';
		}

		$body = wp_remote_retrieve_body( $response );
		$ext  = oaf_contributors_ext_from_mime( wp_remote_retrieve_header( $response, 'content-type' ) );
		if ( '' === $body || '' === $ext ) {
			return '';
		}

		$slug = sanitize_file_name( strtolower( $login ) );
		if ( '' === $slug ) {
			return '';
		}
		$file = $slug . '.' . $ext;

		global $wp_filesystem;
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();
		if ( ! $wp_filesystem || ! $wp_filesystem->put_contents( trailingslashit( $dir['path'] ) . $file, $body, FS_CHMOD_FILE ) ) {
			return '';
		}

		return trailingslashit( $dir['url'] ) . $file;
	}
}

if ( ! function_exists( 'oaf_schedule_contributors' ) ) {
	/**
	 * Ensure a weekly background refresh is scheduled. `weekly` is a core cron
	 * interval on WordPress 5.4+, and the theme requires 6.6+.
	 *
	 * @return void
	 */
	function oaf_schedule_contributors() {
		if ( ! wp_next_scheduled( 'oaf_refresh_contributors_weekly' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', 'oaf_refresh_contributors_weekly' );
		}
	}
}
add_action( 'init', 'oaf_schedule_contributors' );
add_action( 'oaf_refresh_contributors', 'oaf_refresh_contributors' );
add_action( 'oaf_refresh_contributors_weekly', 'oaf_refresh_contributors' );

if ( ! function_exists( 'oaf_unschedule_contributors' ) ) {
	/**
	 * Clear the scheduled refresh events when the theme is deactivated.
	 *
	 * @return void
	 */
	function oaf_unschedule_contributors() {
		wp_clear_scheduled_hook( 'oaf_refresh_contributors_weekly' );
		wp_clear_scheduled_hook( 'oaf_refresh_contributors' );
	}
}
add_action( 'switch_theme', 'oaf_unschedule_contributors' );
