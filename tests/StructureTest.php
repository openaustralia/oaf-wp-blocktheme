<?php
/**
 * Structural sanity checks. These catch the things `php -l` cannot: malformed
 * theme.json / block.json, block asset targets that point at missing files, and
 * a style.css Version header that Git Updater would refuse to ship.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StructureTest extends TestCase {

	public function test_theme_json_is_valid_and_schema_v3(): void {
		$data = $this->read_json( OAF_THEME_DIR . '/theme.json' );
		$this->assertSame( 3, $data['version'], 'theme.json must stay on schema version 3.' );
		$this->assertArrayHasKey( 'settings', $data );
	}

	#[DataProvider( 'block_dirs' )]
	public function test_block_json_is_valid_and_targets_exist( string $block_dir ): void {
		$block = $this->read_json( "$block_dir/block.json" );

		$this->assertArrayHasKey( 'name', $block );
		$this->assertStringStartsWith( 'oaf/', $block['name'], 'Theme blocks must use the oaf/ namespace.' );

		// Every `file:./...` asset reference must resolve to a real file.
		foreach ( array( 'render', 'editorScript', 'script', 'viewScript', 'style' ) as $field ) {
			if ( empty( $block[ $field ] ) ) {
				continue;
			}
			$target = preg_replace( '/^file:\.\//', '', $block[ $field ] );
			$this->assertFileExists( "$block_dir/$target", "$field target missing for {$block['name']}." );
		}
	}

	public static function block_dirs(): array {
		return array(
			'people-grid'           => array( __DIR__ . '/../blocks/people-grid' ),
			'raisely-donation-form' => array( __DIR__ . '/../blocks/raisely-donation-form' ),
		);
	}

	public function test_style_css_version_header_is_semver(): void {
		$header = file_get_contents( OAF_THEME_DIR . '/style.css' );
		$this->assertNotFalse( $header );

		$matched = preg_match( '/^\s*Version:\s*(\d+\.\d+\.\d+)\s*$/m', $header, $m );
		$this->assertSame(
			1,
			$matched,
			'style.css needs a "Version: X.Y.Z" header - Git Updater keys on it to offer updates.'
		);
	}

	public function test_style_css_declares_git_updater_headers(): void {
		$header = file_get_contents( OAF_THEME_DIR . '/style.css' );
		$this->assertMatchesRegularExpression( '/GitHub Theme URI:\s*openaustralia\/oaf-wp-blocktheme/', $header );
		$this->assertMatchesRegularExpression( '/Primary Branch:\s*main/', $header );
	}

	private function read_json( string $path ): array {
		$this->assertFileExists( $path );
		$raw  = file_get_contents( $path );
		$data = json_decode( $raw, true );
		$this->assertSame(
			JSON_ERROR_NONE,
			json_last_error(),
			"Invalid JSON in $path: " . json_last_error_msg()
		);
		return $data;
	}
}
