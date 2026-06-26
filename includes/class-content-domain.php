<?php
/**
 * Online Courses content domain.
 *
 * @package Online_Courses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Online_Courses_Content_Domain {
	public const POST_TYPE             = 'course';
	public const TAXONOMY              = 'course_category';
	public const CHECKOUT_URL_META_KEY = '_online_courses_checkout_url';
	public const COURSES_PAGE_PATH     = 'cursos';
	public const META_BOX_ID           = 'online-courses-checkout';
	public const META_BOX_NONCE_ACTION = 'online_courses_save_checkout_settings';
	public const META_BOX_NONCE_NAME   = 'online_courses_checkout_nonce';

	public function register_hooks(): void {
		add_action( 'init', array( $this, 'register_content_types' ) );
		add_action( 'init', array( $this, 'register_meta' ), 11 );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_meta_box' ) );
	}

	public function register_content_types(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'has_archive'        => self::COURSES_PAGE_PATH,
				'hierarchical'       => false,
				'labels'             => $this->post_type_labels(),
				'menu_icon'          => 'dashicons-welcome-learn-more',
				'public'             => true,
				'publicly_queryable' => true,
				'query_var'          => true,
				'rewrite'            => array(
					'slug'       => self::COURSES_PAGE_PATH,
					'with_front' => false,
				),
				'show_in_rest'       => true,
				'show_ui'            => true,
				'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'template'           => $this->course_editor_template(),
				'template_lock'      => false,
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			array( self::POST_TYPE ),
			array(
				'hierarchical'      => true,
				'labels'            => $this->taxonomy_labels(),
				'public'            => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug'       => self::COURSES_PAGE_PATH . '/categoria',
					'with_front' => false,
				),
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'show_ui'           => true,
			)
		);
	}

	public function register_meta(): void {
		register_post_meta(
			self::POST_TYPE,
			self::CHECKOUT_URL_META_KEY,
			array(
				'auth_callback'     => static function ( $allowed, $meta_key, $post_id = 0 ) {
					unset( $allowed, $meta_key );

					$post_id = (int) $post_id;

					if ( $post_id > 0 ) {
						return current_user_can( 'edit_post', $post_id );
					}

					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => array( self::class, 'sanitize_checkout_url' ),
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
			)
		);
	}

	/**
	 * Sanitizes a checkout URL for storage.
	 *
	 * @param mixed $value Raw URL value.
	 */
	public static function sanitize_checkout_url( $value ): string {
		if ( ! is_string( $value ) ) {
			return '';
		}

		$raw_url   = trim( $value );
		$raw_parts = parse_url( $raw_url );

		if ( false === $raw_parts || empty( $raw_parts['scheme'] ) || empty( $raw_parts['host'] ) ) {
			return '';
		}

		$url = esc_url_raw( $raw_url, array( 'http', 'https' ) );

		if ( '' === $url ) {
			return '';
		}

		$parts  = parse_url( $url );
		$scheme = is_array( $parts ) && isset( $parts['scheme'] ) ? strtolower( (string) $parts['scheme'] ) : '';

		if ( false === $parts || ! in_array( $scheme, array( 'http', 'https' ), true ) || empty( $parts['host'] ) ) {
			return '';
		}

		return $url;
	}

	public function register_meta_box(): void {
		add_meta_box(
			self::META_BOX_ID,
			__( 'Course checkout', 'online-courses' ),
			array( $this, 'render_meta_box' ),
			self::POST_TYPE,
			'side',
			'default'
		);
	}

	public function render_meta_box( WP_Post $post ): void {
		$checkout_url = get_post_meta( $post->ID, self::CHECKOUT_URL_META_KEY, true );

		wp_nonce_field( self::META_BOX_NONCE_ACTION, self::META_BOX_NONCE_NAME );
		?>
		<p>
			<label for="online-courses-checkout-url"><?php esc_html_e( 'Checkout URL', 'online-courses' ); ?></label>
			<input
				class="widefat"
				id="online-courses-checkout-url"
				name="online_courses_checkout_url"
				type="url"
				value="<?php echo esc_attr( esc_url( $checkout_url ) ); ?>"
				placeholder="https://"
			>
		</p>
		<?php
	}

	public function save_meta_box( int $post_id ): void {
		$nonce = isset( $_POST[ self::META_BOX_NONCE_NAME ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::META_BOX_NONCE_NAME ] ) ) : '';

		if ( ! $nonce || ! wp_verify_nonce( $nonce, self::META_BOX_NONCE_ACTION ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$checkout_url = isset( $_POST['online_courses_checkout_url'] )
			? self::sanitize_checkout_url( wp_unslash( $_POST['online_courses_checkout_url'] ) )
			: '';

		if ( '' === $checkout_url ) {
			delete_post_meta( $post_id, self::CHECKOUT_URL_META_KEY );
			return;
		}

		update_post_meta( $post_id, self::CHECKOUT_URL_META_KEY, $checkout_url );
	}

	/**
	 * Returns the default editable block structure for new courses.
	 *
	 * @return array<int, array<int, mixed>>
	 */
	private function course_editor_template(): array {
		return array(
			array( Online_Courses_Blocks::LEARNING_OUTCOMES_BLOCK ),
			array( Online_Courses_Blocks::COURSE_CURRICULUM_BLOCK ),
			array( Online_Courses_Blocks::REQUIREMENTS_BLOCK ),
			array(
				'core/heading',
				array(
					'content' => __( 'About this course', 'online-courses' ),
					'level'   => 2,
				),
			),
			array(
				'core/paragraph',
				array(
					'content' => __( 'Explain what the course covers, how the student should use it and what makes it useful.', 'online-courses' ),
				),
			),
			array( Online_Courses_Blocks::AUDIENCE_FIT_BLOCK ),
			array( Online_Courses_Blocks::INSTRUCTOR_BIO_BLOCK ),
		);
	}

	/**
	 * @return array<string, string>
	 */
	private function post_type_labels(): array {
		return array(
			'name'                  => _x( 'Courses', 'Post type general name', 'online-courses' ),
			'singular_name'         => _x( 'Course', 'Post type singular name', 'online-courses' ),
			'menu_name'             => _x( 'Courses', 'Admin menu text', 'online-courses' ),
			'name_admin_bar'        => _x( 'Course', 'Add new on toolbar', 'online-courses' ),
			'add_new'               => __( 'Add new', 'online-courses' ),
			'add_new_item'          => __( 'Add course', 'online-courses' ),
			'all_items'             => __( 'All courses', 'online-courses' ),
			'archives'              => __( 'Courses', 'online-courses' ),
			'edit_item'             => __( 'Edit course', 'online-courses' ),
			'featured_image'        => __( 'Course image', 'online-courses' ),
			'filter_items_list'     => __( 'Filter courses', 'online-courses' ),
			'items_list'            => __( 'Courses list', 'online-courses' ),
			'items_list_navigation' => __( 'Courses list navigation', 'online-courses' ),
			'new_item'              => __( 'New course', 'online-courses' ),
			'not_found'             => __( 'No courses found.', 'online-courses' ),
			'not_found_in_trash'    => __( 'No courses found in Trash.', 'online-courses' ),
			'remove_featured_image' => __( 'Remove course image', 'online-courses' ),
			'search_items'          => __( 'Search courses', 'online-courses' ),
			'set_featured_image'    => __( 'Set course image', 'online-courses' ),
			'uploaded_to_this_item' => __( 'Uploaded to this course', 'online-courses' ),
			'use_featured_image'    => __( 'Use as course image', 'online-courses' ),
			'view_item'             => __( 'View course', 'online-courses' ),
		);
	}

	/**
	 * @return array<string, string>
	 */
	private function taxonomy_labels(): array {
		return array(
			'name'              => _x( 'Course categories', 'taxonomy general name', 'online-courses' ),
			'singular_name'     => _x( 'Course category', 'taxonomy singular name', 'online-courses' ),
			'add_new_item'      => __( 'Add course category', 'online-courses' ),
			'all_items'         => __( 'All categories', 'online-courses' ),
			'back_to_items'     => __( 'Back to categories', 'online-courses' ),
			'edit_item'         => __( 'Edit category', 'online-courses' ),
			'menu_name'         => __( 'Categories', 'online-courses' ),
			'new_item_name'     => __( 'New category name', 'online-courses' ),
			'not_found'         => __( 'No categories found.', 'online-courses' ),
			'parent_item'       => __( 'Parent category', 'online-courses' ),
			'parent_item_colon' => __( 'Parent category:', 'online-courses' ),
			'search_items'      => __( 'Search categories', 'online-courses' ),
			'update_item'       => __( 'Update category', 'online-courses' ),
			'view_item'         => __( 'View category', 'online-courses' ),
		);
	}
}
