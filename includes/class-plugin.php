<?php
/**
 * Main plugin bootstrap.
 *
 * @package Online_Courses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Online_Courses_Plugin {
	private static ?Online_Courses_Plugin $instance = null;

	private bool $booted = false;

	private Online_Courses_Content_Domain $content_domain;

	private function __construct() {
		$this->content_domain = new Online_Courses_Content_Domain();
	}

	public static function instance(): Online_Courses_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		add_action( 'init', array( $this, 'load_textdomain' ) );
		$this->content_domain->register_hooks();
	}

	public function load_textdomain(): void {
		load_plugin_textdomain(
			'online-courses',
			false,
			dirname( ONLINE_COURSES_BASENAME ) . '/languages'
		);
	}

	public function content_domain(): Online_Courses_Content_Domain {
		return $this->content_domain;
	}

	public static function activate(): void {
		$domain = new Online_Courses_Content_Domain();
		$domain->register_content_types();
		flush_rewrite_rules();
	}

	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
