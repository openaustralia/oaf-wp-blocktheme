<?php
/**
 * Tests for inc/stats.php - the plugin-absent fallback path.
 *
 * The plugin-present path calls Alaveteli_Stats_Render::stat(), which belongs to
 * a separate plugin and is out of scope here. We only assert the theme's own
 * guard: when the plugin class is absent, the fallback copy is returned.
 *
 * @package oaf-wp-blocktheme
 */

use PHPUnit\Framework\TestCase;

final class StatsTest extends TestCase {

	public function test_returns_fallback_when_plugin_absent(): void {
		$this->assertFalse(
			class_exists( 'Alaveteli_Stats_Render' ),
			'Test assumes the stats plugin is not loaded in the unit harness.'
		);
		// esc_html is a pass-through stub, so this asserts the fallback branch is
		// taken, not that escaping happened.
		$this->assertSame( '12,345 requests', oaf_stat( 'visible_request_count', '12,345 requests' ) );
	}
}
