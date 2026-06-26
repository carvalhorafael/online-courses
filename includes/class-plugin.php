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

	private Online_Courses_Blocks $blocks;

	private Online_Courses_GitHub_Updater $github_updater;

	private function __construct() {
		$this->content_domain = new Online_Courses_Content_Domain();
		$this->blocks         = new Online_Courses_Blocks();
		$this->github_updater = new Online_Courses_GitHub_Updater( ONLINE_COURSES_FILE, ONLINE_COURSES_VERSION );
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
		$this->blocks->register_hooks();
		$this->github_updater->register_hooks();
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

	public function blocks(): Online_Courses_Blocks {
		return $this->blocks;
	}

	public function github_updater(): Online_Courses_GitHub_Updater {
		return $this->github_updater;
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
