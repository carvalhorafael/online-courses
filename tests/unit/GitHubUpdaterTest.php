<?php
/**
 * GitHub updater unit tests.
 *
 * @package Online_Courses
 */

use PHPUnit\Framework\TestCase;

final class GitHubUpdaterTest extends TestCase {
	public function test_latest_release_normalizes_expected_package_asset(): void {
		$updater = new Online_Courses_GitHub_Updater(
			'/tmp/online-courses/online-courses.php',
			'0.1.0',
			static function (): array {
				return array(
					'response' => array(
						'code' => 200,
					),
					'body'     => json_encode(
						array(
							'tag_name'     => 'v0.2.0',
							'html_url'     => 'https://github.com/carvalhorafael/online-courses/releases/tag/v0.2.0',
							'published_at' => '2026-06-19T00:00:00Z',
							'body'         => 'Release notes.',
							'assets'       => array(
								array(
									'name'                 => 'online-courses-0.2.0.zip',
									'browser_download_url' => 'https://github.com/carvalhorafael/online-courses/releases/download/v0.2.0/online-courses-0.2.0.zip',
								),
							),
						)
					),
				);
			}
		);

		$release = $updater->latest_release();

		$this->assertIsArray( $release );
		$this->assertSame( '0.2.0', $release['version'] );
		$this->assertSame( 'https://github.com/carvalhorafael/online-courses/releases/download/v0.2.0/online-courses-0.2.0.zip', $release['package_url'] );
	}

	public function test_latest_release_ignores_uninstallable_release_payloads(): void {
		$payloads = array(
			array(
				'tag_name'   => 'v0.2.0',
				'draft'      => true,
				'assets'     => array(),
			),
			array(
				'tag_name'   => 'v0.2.0',
				'prerelease' => true,
				'assets'     => array(),
			),
			array(
				'tag_name' => 'v0.2.0',
				'assets'   => array(
					array(
						'name'                 => 'wrong-plugin-0.2.0.zip',
						'browser_download_url' => 'https://example.com/wrong-plugin-0.2.0.zip',
					),
				),
			),
		);

		foreach ( $payloads as $payload ) {
			$updater = new Online_Courses_GitHub_Updater(
				'/tmp/online-courses/online-courses.php',
				'0.1.0',
				static function () use ( $payload ): array {
					return array(
						'response' => array(
							'code' => 200,
						),
						'body'     => json_encode( $payload ),
					);
				}
			);

			$this->assertNull( $updater->latest_release() );
		}
	}

	public function test_update_uri_filter_returns_update_for_this_plugin_only(): void {
		$updater = new Online_Courses_GitHub_Updater(
			'/tmp/online-courses/online-courses.php',
			'0.1.0',
			static function (): array {
				return array(
					'response' => array(
						'code' => 200,
					),
					'body'     => json_encode(
						array(
							'tag_name' => 'v0.2.0',
							'assets'   => array(
								array(
									'name'                 => 'online-courses-0.2.0.zip',
									'browser_download_url' => 'https://github.com/carvalhorafael/online-courses/releases/download/v0.2.0/online-courses-0.2.0.zip',
								),
							),
						)
					),
				);
			}
		);

		$update = $updater->filter_update_from_update_uri(
			false,
			array( 'UpdateURI' => 'https://github.com/carvalhorafael/online-courses' ),
			'online-courses/online-courses.php',
			array()
		);

		$this->assertIsArray( $update );
		$this->assertSame( '0.2.0', $update['new_version'] );
		$this->assertSame( 'https://github.com/carvalhorafael/online-courses/releases/download/v0.2.0/online-courses-0.2.0.zip', $update['package'] );

		$this->assertFalse(
			$updater->filter_update_from_update_uri(
				false,
				array( 'UpdateURI' => 'https://github.com/carvalhorafael/other-plugin' ),
				'other-plugin/other-plugin.php',
				array()
			)
		);
	}
}
