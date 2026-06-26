<?php
/**
 * Online Courses editor blocks.
 *
 * @package Online_Courses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Online_Courses_Blocks {
	public const LEARNING_OUTCOMES_BLOCK = 'online-courses/learning-outcomes';
	public const COURSE_CURRICULUM_BLOCK = 'online-courses/course-curriculum';
	public const REQUIREMENTS_BLOCK      = 'online-courses/requirements';
	public const AUDIENCE_FIT_BLOCK      = 'online-courses/audience-fit';
	public const INSTRUCTOR_BIO_BLOCK    = 'online-courses/instructor-bio';
	private const EDITOR_SCRIPT_HANDLE   = 'online-courses-editor-blocks';

	public function register_hooks(): void {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	public function register_blocks(): void {
		$this->register_editor_script();

		register_block_type(
			self::LEARNING_OUTCOMES_BLOCK,
			array(
				'api_version'     => 2,
				'attributes'      => array(
					'eyebrow'     => array(
						'type'    => 'string',
						'default' => __( 'Learning outcomes', 'online-courses' ),
					),
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'What you will be able to apply.', 'online-courses' ),
					),
					'description' => array(
						'type'    => 'string',
						'default' => __( 'Describe the practical results students should get from this course.', 'online-courses' ),
					),
					'items'       => array(
						'type'    => 'array',
						'default' => array(
							__( 'Clarify the first practical result students will be able to apply.', 'online-courses' ),
							__( 'Describe the second result students should achieve.', 'online-courses' ),
							__( 'Show the third outcome that makes the course valuable.', 'online-courses' ),
						),
					),
				),
				'editor_script'   => self::EDITOR_SCRIPT_HANDLE,
				'render_callback' => array( $this, 'render_learning_outcomes' ),
			)
		);

		register_block_type(
			self::COURSE_CURRICULUM_BLOCK,
			array(
				'api_version'     => 2,
				'attributes'      => array(
					'eyebrow'     => array(
						'type'    => 'string',
						'default' => __( 'Course curriculum', 'online-courses' ),
					),
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'Program structure', 'online-courses' ),
					),
					'description' => array(
						'type'    => 'string',
						'default' => __( 'Organize the modules and lessons students will follow.', 'online-courses' ),
					),
					'sections'    => array(
						'type'    => 'array',
						'default' => array(
							array(
								'title'   => __( 'Module 1', 'online-courses' ),
								'meta'    => __( '3 lessons', 'online-courses' ),
								'lessons' => array(
									array(
										'title'    => __( 'Introduce the context and the main problem', 'online-courses' ),
										'duration' => __( '5 min', 'online-courses' ),
										'preview'  => true,
									),
								),
							),
						),
					),
				),
				'editor_script'   => self::EDITOR_SCRIPT_HANDLE,
				'render_callback' => array( $this, 'render_course_curriculum' ),
			)
		);

		register_block_type(
			self::REQUIREMENTS_BLOCK,
			array(
				'api_version'     => 2,
				'attributes'      => array(
					'eyebrow'     => array(
						'type'    => 'string',
						'default' => __( 'Requirements', 'online-courses' ),
					),
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'Before you begin', 'online-courses' ),
					),
					'description' => array(
						'type'    => 'string',
						'default' => __( 'Clarify the minimum context students should have before starting.', 'online-courses' ),
					),
					'items'       => array(
						'type'    => 'array',
						'default' => array(
							__( 'List the minimum knowledge, tools or context needed before starting.', 'online-courses' ),
						),
					),
				),
				'editor_script'   => self::EDITOR_SCRIPT_HANDLE,
				'render_callback' => array( $this, 'render_requirements' ),
			)
		);

		register_block_type(
			self::AUDIENCE_FIT_BLOCK,
			array(
				'api_version'     => 2,
				'attributes'      => array(
					'eyebrow'     => array(
						'type'    => 'string',
						'default' => __( 'Audience fit', 'online-courses' ),
					),
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'Who this course is for.', 'online-courses' ),
					),
					'description' => array(
						'type'    => 'string',
						'default' => __( 'Describe the profiles that will benefit most from this course.', 'online-courses' ),
					),
					'groups'      => array(
						'type'    => 'array',
						'default' => array(
							array(
								'label'       => __( 'Primary audience', 'online-courses' ),
								'title'       => __( 'Professionals who need this operating skill', 'online-courses' ),
								'description' => __( 'Explain why this profile should take the course.', 'online-courses' ),
							),
						),
					),
				),
				'editor_script'   => self::EDITOR_SCRIPT_HANDLE,
				'render_callback' => array( $this, 'render_audience_fit' ),
			)
		);

		register_block_type(
			self::INSTRUCTOR_BIO_BLOCK,
			array(
				'api_version'     => 2,
				'attributes'      => array(
					'eyebrow'    => array(
						'type'    => 'string',
						'default' => __( 'Instructor', 'online-courses' ),
					),
					'name'       => array(
						'type'    => 'string',
						'default' => '',
					),
					'role'       => array(
						'type'    => 'string',
						'default' => __( 'Course instructor', 'online-courses' ),
					),
					'bio'        => array(
						'type'    => 'string',
						'default' => __( 'Add a short instructor bio connected to the course topic.', 'online-courses' ),
					),
					'imageUrl'   => array(
						'type'    => 'string',
						'default' => '',
					),
					'highlights' => array(
						'type'    => 'array',
						'default' => array(
							__( 'Connects course concepts to practical routines.', 'online-courses' ),
						),
					),
				),
				'editor_script'   => self::EDITOR_SCRIPT_HANDLE,
				'render_callback' => array( $this, 'render_instructor_bio' ),
			)
		);
	}

	private function register_editor_script(): void {
		$asset_path = ONLINE_COURSES_DIR . 'assets/editor-blocks.js';

		wp_register_script(
			self::EDITOR_SCRIPT_HANDLE,
			plugins_url( 'assets/editor-blocks.js', ONLINE_COURSES_FILE ),
			array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
			file_exists( $asset_path ) ? (string) filemtime( $asset_path ) : ONLINE_COURSES_VERSION,
			true
		);
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public function render_learning_outcomes( array $attributes ): string {
		$items = $this->strings_attribute( $attributes, 'items' );

		if ( empty( $items ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="es-value-stack online-courses-block online-courses-block--learning-outcomes">
			<header class="es-value-stack__header">
				<p class="es-value-stack__eyebrow"><?php echo esc_html( $this->string_attribute( $attributes, 'eyebrow' ) ); ?></p>
				<h2 class="es-value-stack__title"><?php echo esc_html( $this->string_attribute( $attributes, 'title' ) ); ?></h2>
				<?php if ( $this->string_attribute( $attributes, 'description' ) ) : ?>
					<p class="es-value-stack__description"><?php echo esc_html( $this->string_attribute( $attributes, 'description' ) ); ?></p>
				<?php endif; ?>
			</header>
			<div class="es-value-stack__items">
				<?php foreach ( $items as $index => $item ) : ?>
					<div class="es-value-stack__item">
						<div class="es-value-stack__item-copy">
							<p class="es-value-stack__item-title">
								<?php
								printf(
									/* translators: %d: Learning outcome number. */
									esc_html__( 'Outcome %d', 'online-courses' ),
									(int) $index + 1
								);
								?>
							</p>
							<p class="es-value-stack__item-description"><?php echo esc_html( $item ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public function render_course_curriculum( array $attributes ): string {
		$sections = isset( $attributes['sections'] ) && is_array( $attributes['sections'] ) ? $attributes['sections'] : array();

		if ( empty( $sections ) ) {
			return '';
		}

		$lesson_count = 0;

		foreach ( $sections as $section ) {
			$lessons = isset( $section['lessons'] ) && is_array( $section['lessons'] ) ? $section['lessons'] : array();
			$lesson_count += count( $lessons );
		}

		ob_start();
		?>
		<section class="es-course-curriculum online-courses-block online-courses-block--course-curriculum" data-es-course-curriculum>
			<header class="es-course-curriculum__header">
				<p class="es-course-curriculum__eyebrow"><?php echo esc_html( $this->string_attribute( $attributes, 'eyebrow' ) ); ?></p>
				<h2 class="es-course-curriculum__title"><?php echo esc_html( $this->string_attribute( $attributes, 'title' ) ); ?></h2>
				<?php if ( $this->string_attribute( $attributes, 'description' ) ) : ?>
					<p class="es-course-curriculum__description"><?php echo esc_html( $this->string_attribute( $attributes, 'description' ) ); ?></p>
				<?php endif; ?>
				<dl class="es-course-curriculum__summary">
					<div class="es-course-curriculum__summary-item">
						<dt><?php esc_html_e( 'Sections', 'online-courses' ); ?></dt>
						<dd>
							<?php
							printf(
								/* translators: %d: Number of curriculum sections. */
								esc_html( _n( '%d section', '%d sections', count( $sections ), 'online-courses' ) ),
								count( $sections )
							);
							?>
						</dd>
					</div>
					<div class="es-course-curriculum__summary-item">
						<dt><?php esc_html_e( 'Lessons', 'online-courses' ); ?></dt>
						<dd>
							<?php
							printf(
								/* translators: %d: Number of curriculum lessons. */
								esc_html( _n( '%d lesson', '%d lessons', $lesson_count, 'online-courses' ) ),
								$lesson_count
							);
							?>
						</dd>
					</div>
				</dl>
			</header>
			<div class="es-course-curriculum__sections">
				<?php foreach ( $sections as $index => $section ) : ?>
					<?php
					$lessons = isset( $section['lessons'] ) && is_array( $section['lessons'] ) ? $section['lessons'] : array();
					?>
					<details class="es-course-curriculum__section" <?php echo 0 === $index ? 'open' : ''; ?>>
						<summary class="es-course-curriculum__section-trigger">
							<span class="es-course-curriculum__section-title"><?php echo esc_html( $this->array_string_value( $section, 'title' ) ); ?></span>
							<?php if ( $this->array_string_value( $section, 'meta' ) ) : ?>
								<span class="es-course-curriculum__section-meta"><?php echo esc_html( $this->array_string_value( $section, 'meta' ) ); ?></span>
							<?php endif; ?>
						</summary>
						<ol class="es-course-curriculum__lectures">
							<?php foreach ( $lessons as $lesson ) : ?>
								<li class="es-course-curriculum__lecture">
									<span class="es-course-curriculum__lecture-title"><?php echo esc_html( $this->array_string_value( $lesson, 'title' ) ); ?></span>
									<?php if ( ! empty( $lesson['preview'] ) ) : ?>
										<span class="es-course-curriculum__preview-badge"><?php esc_html_e( 'Preview lesson', 'online-courses' ); ?></span>
									<?php endif; ?>
									<?php if ( $this->array_string_value( $lesson, 'duration' ) ) : ?>
										<span class="es-course-curriculum__lecture-duration"><?php echo esc_html( $this->array_string_value( $lesson, 'duration' ) ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					</details>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public function render_requirements( array $attributes ): string {
		return $this->render_section_list( $attributes, $this->strings_attribute( $attributes, 'items' ), 'online-courses-block--requirements' );
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public function render_audience_fit( array $attributes ): string {
		$groups = isset( $attributes['groups'] ) && is_array( $attributes['groups'] ) ? $attributes['groups'] : array();

		if ( empty( $groups ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="es-audience-fit online-courses-block online-courses-block--audience-fit">
			<header class="es-audience-fit__header">
				<p class="es-audience-fit__eyebrow"><?php echo esc_html( $this->string_attribute( $attributes, 'eyebrow' ) ); ?></p>
				<h2 class="es-audience-fit__title"><?php echo esc_html( $this->string_attribute( $attributes, 'title' ) ); ?></h2>
				<?php if ( $this->string_attribute( $attributes, 'description' ) ) : ?>
					<p class="es-audience-fit__description"><?php echo esc_html( $this->string_attribute( $attributes, 'description' ) ); ?></p>
				<?php endif; ?>
			</header>
			<div class="es-audience-fit__groups">
				<?php foreach ( $groups as $group ) : ?>
					<article class="es-audience-fit-card">
						<?php if ( $this->array_string_value( $group, 'label' ) ) : ?>
							<p class="es-audience-fit-card__label"><?php echo esc_html( $this->array_string_value( $group, 'label' ) ); ?></p>
						<?php endif; ?>
						<h3 class="es-audience-fit-card__title"><?php echo esc_html( $this->array_string_value( $group, 'title' ) ); ?></h3>
						<?php if ( $this->array_string_value( $group, 'description' ) ) : ?>
							<p class="es-audience-fit-card__description"><?php echo esc_html( $this->array_string_value( $group, 'description' ) ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public function render_instructor_bio( array $attributes ): string {
		$name = $this->string_attribute( $attributes, 'name' );

		if ( '' === $name ) {
			$author_id = (int) get_post_field( 'post_author', get_the_ID() );
			$name      = get_the_author_meta( 'display_name', $author_id );
		}

		if ( '' === $name ) {
			return '';
		}

		$image_url = $this->string_attribute( $attributes, 'imageUrl' );

		if ( '' === $image_url ) {
			$author_id = (int) get_post_field( 'post_author', get_the_ID() );
			$image_url = get_avatar_url( $author_id, array( 'size' => 512 ) );
		}

		ob_start();
		?>
		<section class="es-instructor-bio course-single__instructor online-courses-block online-courses-block--instructor-bio">
			<div class="es-instructor-bio__media">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $name ); ?>">
			</div>
			<div class="es-instructor-bio__content">
				<header class="es-instructor-bio__header">
					<p class="es-instructor-bio__eyebrow"><?php echo esc_html( $this->string_attribute( $attributes, 'eyebrow' ) ); ?></p>
					<h2 class="es-instructor-bio__name"><?php echo esc_html( $name ); ?></h2>
					<?php if ( $this->string_attribute( $attributes, 'role' ) ) : ?>
						<p class="es-instructor-bio__role"><?php echo esc_html( $this->string_attribute( $attributes, 'role' ) ); ?></p>
					<?php endif; ?>
				</header>
				<?php if ( $this->string_attribute( $attributes, 'bio' ) ) : ?>
					<div class="es-instructor-bio__bio">
						<p><?php echo esc_html( $this->string_attribute( $attributes, 'bio' ) ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $this->strings_attribute( $attributes, 'highlights' ) ) ) : ?>
					<ul class="es-instructor-bio__highlights">
						<?php foreach ( $this->strings_attribute( $attributes, 'highlights' ) as $highlight ) : ?>
							<li><?php echo esc_html( $highlight ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param array<int, string>  $items List items.
	 */
	private function render_section_list( array $attributes, array $items, string $modifier_class ): string {
		if ( empty( $items ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="es-section-block online-courses-block <?php echo esc_attr( $modifier_class ); ?>">
			<div class="es-section-header">
				<p class="es-section-header__eyebrow"><?php echo esc_html( $this->string_attribute( $attributes, 'eyebrow' ) ); ?></p>
				<h2 class="es-section-header__title"><?php echo esc_html( $this->string_attribute( $attributes, 'title' ) ); ?></h2>
				<?php if ( $this->string_attribute( $attributes, 'description' ) ) : ?>
					<p class="es-section-header__description"><?php echo esc_html( $this->string_attribute( $attributes, 'description' ) ); ?></p>
				<?php endif; ?>
			</div>
			<ul>
				<?php foreach ( $items as $item ) : ?>
					<li><?php echo esc_html( $item ); ?></li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	private function string_attribute( array $attributes, string $key ): string {
		return isset( $attributes[ $key ] ) && is_string( $attributes[ $key ] ) ? trim( $attributes[ $key ] ) : '';
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 *
	 * @return array<int, string>
	 */
	private function strings_attribute( array $attributes, string $key ): array {
		$items = isset( $attributes[ $key ] ) && is_array( $attributes[ $key ] ) ? $attributes[ $key ] : array();

		return array_values(
			array_filter(
				array_map(
					static fn( $item ): string => is_string( $item ) ? trim( $item ) : '',
					$items
				)
			)
		);
	}

	/**
	 * @param array<string, mixed> $item Attribute item.
	 */
	private function array_string_value( array $item, string $key ): string {
		return isset( $item[ $key ] ) && is_string( $item[ $key ] ) ? trim( $item[ $key ] ) : '';
	}
}
