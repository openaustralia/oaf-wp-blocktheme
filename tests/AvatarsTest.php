<?php
/**
 * Tests for the pure helpers behind local avatars (inc/avatars.php) and the
 * initials helper they share with the People grid (inc/people.php).
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AvatarsTest extends TestCase {

	public function test_name_key_is_lowercased_and_trimmed(): void {
		$this->assertSame( 'ada lovelace', oaf_avatar_name_key( '  Ada Lovelace  ' ) );
	}

	#[DataProvider( 'initials_cases' )]
	public function test_initials( string $name, string $expected ): void {
		$this->assertSame( $expected, oaf_person_initials( $name ) );
	}

	public static function initials_cases(): array {
		return array(
			'two words'      => array( 'Ada Lovelace', 'AL' ),
			'single word'    => array( 'Cher', 'C' ),
			'three words'    => array( 'Ann Bob Carr', 'AB' ),
			'extra spaces'   => array( '  Grace   Hopper ', 'GH' ),
			'empty'          => array( '', '' ),
			'lowercased in'  => array( 'john doe', 'JD' ),
		);
	}

	public function test_colour_is_from_the_brand_palette(): void {
		$palette = oaf_person_palette();
		$this->assertContains( oaf_avatar_color_for_name( 'Ada Lovelace' ), $palette );
	}

	public function test_colour_is_deterministic_and_case_insensitive(): void {
		$this->assertSame(
			oaf_avatar_color_for_name( 'Ada Lovelace' ),
			oaf_avatar_color_for_name( '  ADA LOVELACE ' ),
			'Same person must always get the same colour, regardless of case/spacing.'
		);
	}

	public function test_initials_svg_is_a_valid_base64_svg_data_uri(): void {
		$uri = oaf_avatar_initials_svg( 'Ada Lovelace', 96 );

		$prefix = 'data:image/svg+xml;base64,';
		$this->assertStringStartsWith( $prefix, $uri );

		$decoded = base64_decode( substr( $uri, strlen( $prefix ) ), true );
		$this->assertNotFalse( $decoded, 'Avatar payload was not valid base64.' );
		$this->assertStringContainsString( '<svg', $decoded );
		$this->assertStringContainsString( '>AL<', $decoded, 'Rendered initials should appear in the SVG.' );
	}

	public function test_initials_svg_falls_back_to_question_mark_for_blank_name(): void {
		$uri     = oaf_avatar_initials_svg( '', 48 );
		$decoded = base64_decode( substr( $uri, strlen( 'data:image/svg+xml;base64,' ) ), true );
		$this->assertStringContainsString( '>?<', $decoded );
	}
}
