<?php
/**
 * Tests for inc/options.php - defaults, the accessor, and the sanitiser's
 * routing/gating logic.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

final class OptionsTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['oaf_test_options'] = array();
		$GLOBALS['oaf_test_caps']    = array();
	}

	public function test_default_options_cover_every_service_url(): void {
		$defaults = oaf_default_options();
		foreach ( array( 'planningalerts_url', 'righttoknow_url', 'theyvoteforyou_url', 'openaustralia_url' ) as $key ) {
			$this->assertArrayHasKey( $key, $defaults );
			$this->assertNotSame( '', $defaults[ $key ] );
		}
	}

	public function test_option_falls_back_to_default_when_never_saved(): void {
		$this->assertSame( 'OpenAustralia Foundation', oaf_option( 'org_name' ) );
	}

	public function test_saved_value_wins_over_default(): void {
		$GLOBALS['oaf_test_options']['oaf_theme_options'] = array( 'org_name' => 'Custom Name' );
		$this->assertSame( 'Custom Name', oaf_option( 'org_name' ) );
	}

	/**
	 * The documented subtlety: an intentionally-blanked field (empty string that
	 * WAS saved) must win over the default, so an admin can remove a social link.
	 */
	public function test_saved_empty_string_wins_over_default(): void {
		$GLOBALS['oaf_test_options']['oaf_theme_options'] = array( 'github_url' => '' );
		$this->assertSame( '', oaf_option( 'github_url' ) );
	}

	public function test_unknown_key_returns_empty_string(): void {
		$this->assertSame( '', oaf_option( 'no_such_key' ) );
	}

	public function test_service_urls_expose_exactly_the_four_services(): void {
		$this->assertSame(
			array( 'planningalerts', 'righttoknow', 'theyvoteforyou', 'openaustralia' ),
			array_keys( oaf_service_urls() )
		);
	}

	public function test_sanitise_routes_url_and_text_keys_and_ignores_unknown(): void {
		$clean = oaf_sanitize_options(
			array(
				'org_name'   => '  ACME  ',
				'github_url' => '  https://example.test  ',
				'not_a_real_field' => 'dropped',
			)
		);

		// url keys are trimmed (esc_url_raw is a pass-through in tests).
		$this->assertSame( 'https://example.test', $clean['github_url'] );
		// text keys are present.
		$this->assertArrayHasKey( 'org_name', $clean );
		// unknown keys never make it into the stored array.
		$this->assertArrayNotHasKey( 'not_a_real_field', $clean );
	}

	public function test_missing_input_keys_become_empty_strings(): void {
		$clean = oaf_sanitize_options( array() );
		$this->assertSame( '', $clean['github_url'] );
		$this->assertSame( '', $clean['org_name'] );
	}

	public function test_raisely_embed_stored_raw_when_user_has_unfiltered_html(): void {
		$GLOBALS['oaf_test_caps']['unfiltered_html'] = true;
		$embed = '<script src="https://raisely.example/embed.js"></script>';

		$clean = oaf_sanitize_options( array( 'raisely_embed' => "  $embed  " ) );

		$this->assertSame( $embed, $clean['raisely_embed'] );
	}

	/**
	 * Without unfiltered_html the existing stored embed must be preserved, never
	 * overwritten by the submitted value - this is the multisite XSS guard.
	 */
	public function test_raisely_embed_preserves_existing_when_capability_missing(): void {
		$GLOBALS['oaf_test_caps']['unfiltered_html']      = false;
		$GLOBALS['oaf_test_options']['oaf_theme_options'] = array( 'raisely_embed' => '<b>existing</b>' );

		$clean = oaf_sanitize_options( array( 'raisely_embed' => '<script>injected()</script>' ) );

		$this->assertSame( '<b>existing</b>', $clean['raisely_embed'] );
	}
}
