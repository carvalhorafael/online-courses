<?php
/**
 * Plugin bootstrap integration tests.
 *
 * @package Online_Courses
 */

final class PluginBootstrapTest extends WP_UnitTestCase {
	public function test_singleton_exposes_services(): void {
		$this->assertInstanceOf( Online_Courses_Plugin::class, online_courses() );
		$this->assertInstanceOf( Online_Courses_Content_Domain::class, online_courses()->content_domain() );
		$this->assertInstanceOf( Online_Courses_GitHub_Updater::class, online_courses()->github_updater() );
	}
}
