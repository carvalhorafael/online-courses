<?php
/**
 * Unit test bootstrap.
 *
 * @package Online_Courses
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( string $value, ?array $protocols = null ): string {
		$value = filter_var( $value, FILTER_SANITIZE_URL );

		if ( null === $protocols || '' === $value ) {
			return $value;
		}

		$scheme = parse_url( $value, PHP_URL_SCHEME );

		if ( ! is_string( $scheme ) || ! in_array( $scheme, $protocols, true ) ) {
			return '';
		}

		return $value;
	}
}

require_once dirname( __DIR__ ) . '/includes/class-content-domain.php';
