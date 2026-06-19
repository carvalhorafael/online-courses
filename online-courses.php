<?php
/**
 * Plugin Name: Online Courses
 * Description: Registers the reusable Online Courses content domain for WordPress sites.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Rafael Carvalho
 * Plugin URI: https://github.com/carvalhorafael/online-courses
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://github.com/carvalhorafael/online-courses
 * Text Domain: online-courses
 * Domain Path: /languages
 *
 * @package Online_Courses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ONLINE_COURSES_VERSION', '0.1.0' );
define( 'ONLINE_COURSES_FILE', __FILE__ );
define( 'ONLINE_COURSES_DIR', plugin_dir_path( __FILE__ ) );
define( 'ONLINE_COURSES_BASENAME', plugin_basename( __FILE__ ) );

require_once ONLINE_COURSES_DIR . 'includes/class-content-domain.php';
require_once ONLINE_COURSES_DIR . 'includes/class-plugin.php';

/**
 * Returns the plugin singleton.
 */
function online_courses(): Online_Courses_Plugin {
	return Online_Courses_Plugin::instance();
}

/**
 * Returns the canonical course post type.
 */
function online_courses_post_type(): string {
	return Online_Courses_Content_Domain::POST_TYPE;
}

/**
 * Returns the canonical course category taxonomy.
 */
function online_courses_taxonomy(): string {
	return Online_Courses_Content_Domain::TAXONOMY;
}

/**
 * Returns the canonical checkout URL meta key.
 */
function online_courses_checkout_url_meta_key(): string {
	return Online_Courses_Content_Domain::CHECKOUT_URL_META_KEY;
}

/**
 * Returns the sanitized checkout URL for a course.
 *
 * @param int|WP_Post|null $post Course post, ID, or null for the current post.
 */
function online_courses_get_checkout_url( $post = null ): string {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post || online_courses_post_type() !== $post->post_type ) {
		return '';
	}

	return Online_Courses_Content_Domain::sanitize_checkout_url(
		get_post_meta( $post->ID, online_courses_checkout_url_meta_key(), true )
	);
}

register_activation_hook( __FILE__, array( 'Online_Courses_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Online_Courses_Plugin', 'deactivate' ) );

online_courses()->boot();
