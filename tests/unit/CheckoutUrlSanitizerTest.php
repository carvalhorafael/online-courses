<?php
/**
 * Checkout URL sanitizer unit tests.
 *
 * @package Online_Courses
 */

final class CheckoutUrlSanitizerTest extends Online_Courses_Test_Case {
	public function test_accepts_http_and_https_urls(): void {
		$this->assertSame(
			'https://checkout.example.com/course?offer=1',
			Online_Courses_Content_Domain::sanitize_checkout_url( ' https://checkout.example.com/course?offer=1 ' )
		);

		$this->assertSame(
			'http://checkout.example.com/course',
			Online_Courses_Content_Domain::sanitize_checkout_url( 'http://checkout.example.com/course' )
		);
	}

	public function test_rejects_non_url_values_and_unsupported_protocols(): void {
		$this->assertSame( '', Online_Courses_Content_Domain::sanitize_checkout_url( 'not-a-url' ) );
		$this->assertSame( '', Online_Courses_Content_Domain::sanitize_checkout_url( 'javascript:alert(1)' ) );
		$this->assertSame( '', Online_Courses_Content_Domain::sanitize_checkout_url( array( 'https://example.com' ) ) );
	}
}
