<?php
/**
 * Guard against the drift CLAUDE.md explicitly warns about: the enquiry-type
 * options offered in the contact-page pattern's field-select MUST match the
 * routing table in inc/contact-form.php. If they drift, a real enquiry type
 * silently falls through to the default inbox.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

final class ContactRouteSyncTest extends TestCase {

	/**
	 * Pull the options array out of the jetpack/field-select block comment in the
	 * contact pattern. Deliberately narrow: it targets the `"options":[...]`
	 * attribute on the field-select block, so cosmetic edits elsewhere in the
	 * pattern will not cause false failures.
	 *
	 * @return string[]
	 */
	private function pattern_enquiry_options(): array {
		$pattern = file_get_contents( OAF_THEME_DIR . '/patterns/page-contact.php' );
		$this->assertNotFalse( $pattern, 'Could not read patterns/page-contact.php' );

		$matched = preg_match(
			'/wp:jetpack\/field-select\s+(\{.*?"options"\s*:\s*(\[[^\]]*\]).*?\})\s*-->/s',
			$pattern,
			$m
		);
		$this->assertSame( 1, $matched, 'Could not locate the field-select options in the contact pattern.' );

		$options = json_decode( $m[2], true );
		$this->assertIsArray( $options, 'field-select options were not a JSON array.' );

		return $options;
	}

	public function test_pattern_options_match_routing_table_exactly(): void {
		$pattern_options = $this->pattern_enquiry_options();
		$route_keys      = array_keys( oaf_contact_routes() );

		sort( $pattern_options );
		sort( $route_keys );

		$this->assertSame(
			$route_keys,
			$pattern_options,
			'Contact pattern enquiry options and oaf_contact_routes() have drifted out of sync.'
		);
	}
}
