<?php
/**
 * Tests for inc/contact-form.php - enquiry-type routing.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

final class ContactFormTest extends TestCase {

	public function test_every_route_has_a_recipient_and_subject_prefix(): void {
		foreach ( oaf_contact_routes() as $option => $route ) {
			$this->assertIsArray( $route, "Route for '$option' must be [recipient, subject]." );
			$this->assertCount( 2, $route );
			$this->assertStringContainsString( '@', $route[0], "Recipient for '$option' must be an email." );
			$this->assertNotSame( '', $route[1], "Subject prefix for '$option' must be set." );
		}
	}

	public function test_match_route_finds_the_route_by_submitted_value(): void {
		$route = oaf_contact_match_route(
			array(
				'name'     => 'Someone',
				'enquiry'  => 'Media enquiry',
				'message'  => 'hi',
			)
		);
		$this->assertSame( array( 'media@oaf.org.au', '[Media Contact]' ), $route );
	}

	public function test_match_route_returns_null_for_foreign_forms(): void {
		$this->assertNull( oaf_contact_match_route( array( 'name' => 'x', 'msg' => 'not an enquiry type' ) ) );
	}

	public function test_form_to_overrides_default_recipient_on_match(): void {
		$to = oaf_contact_form_to( 'default@example.test', array( 'q' => 'Right to Know' ) );
		$this->assertSame( 'contact@righttoknow.org.au', $to );
	}

	public function test_form_to_passes_default_through_when_no_match(): void {
		$to = oaf_contact_form_to( 'default@example.test', array( 'q' => 'unrelated' ) );
		$this->assertSame( 'default@example.test', $to );
	}

	public function test_form_subject_overrides_on_match_and_passes_through_otherwise(): void {
		$this->assertSame(
			'[OAF Contact: PlanningAlerts]',
			oaf_contact_form_subject( 'Default subject', array( 'q' => 'PlanningAlerts' ) )
		);
		$this->assertSame(
			'Default subject',
			oaf_contact_form_subject( 'Default subject', array( 'q' => 'unrelated' ) )
		);
	}
}
