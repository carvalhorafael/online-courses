<?php
/**
 * Content domain integration tests.
 *
 * @package Online_Courses
 */

final class ContentDomainTest extends WP_UnitTestCase {
	public function test_post_type_is_registered_with_portable_contract(): void {
		$post_type = get_post_type_object( online_courses_post_type() );

		$this->assertNotNull( $post_type );
		$this->assertSame( 'course', online_courses_post_type() );
		$this->assertTrue( $post_type->public );
		$this->assertSame( 'cursos', $post_type->has_archive );
		$this->assertTrue( $post_type->show_in_rest );
		$this->assertSame( 'cursos', $post_type->rewrite['slug'] );
		$this->assertTrue( post_type_supports( online_courses_post_type(), 'title' ) );
		$this->assertTrue( post_type_supports( online_courses_post_type(), 'editor' ) );
		$this->assertTrue( post_type_supports( online_courses_post_type(), 'thumbnail' ) );
		$this->assertTrue( post_type_supports( online_courses_post_type(), 'excerpt' ) );
		$this->assertFalse( $post_type->template_lock );
		$this->assertIsArray( $post_type->template );
		$this->assertNotEmpty( $post_type->template );
		$this->assertSame( 'core/heading', $post_type->template[0][0] );
		$this->assertSame( 'What you will learn', $post_type->template[0][1]['content'] );
		$this->assertSame( 2, $post_type->template[0][1]['level'] );
	}

	public function test_course_editor_template_includes_expected_editable_sections(): void {
		$post_type = get_post_type_object( online_courses_post_type() );
		$template  = $post_type->template;
		$headings  = array();

		foreach ( $template as $block ) {
			if ( 'core/heading' === $block[0] ) {
				$headings[] = $block[1]['content'];
			}
		}

		$this->assertSame(
			array(
				'What you will learn',
				'Course curriculum',
				'Requirements',
				'About this course',
				'Who this course is for',
				'Instructor',
			),
			$headings
		);

		$this->assertContains( 'core/list', wp_list_pluck( $template, 0 ) );
		$this->assertContains( 'core/paragraph', wp_list_pluck( $template, 0 ) );
	}

	public function test_taxonomy_is_registered_with_portable_contract(): void {
		$taxonomy = get_taxonomy( online_courses_taxonomy() );

		$this->assertNotFalse( $taxonomy );
		$this->assertSame( 'course_category', online_courses_taxonomy() );
		$this->assertTrue( $taxonomy->hierarchical );
		$this->assertTrue( $taxonomy->show_in_rest );
		$this->assertContains( online_courses_post_type(), $taxonomy->object_type );
		$this->assertSame( 'cursos/categoria', $taxonomy->rewrite['slug'] );
	}

	public function test_checkout_url_metadata_is_registered(): void {
		online_courses()->content_domain()->register_meta();

		$registered_meta = get_registered_meta_keys( 'post', online_courses_post_type() );

		$this->assertArrayHasKey( '_online_courses_checkout_url', $registered_meta );
		$this->assertSame( online_courses_checkout_url_meta_key(), '_online_courses_checkout_url' );
		$this->assertTrue( $registered_meta['_online_courses_checkout_url']['show_in_rest'] );
		$this->assertSame( 'string', $registered_meta['_online_courses_checkout_url']['type'] );
		$this->assertTrue( $registered_meta['_online_courses_checkout_url']['single'] );
	}

	public function test_meta_box_renders_checkout_url_field(): void {
		$post_id = self::factory()->post->create(
			array(
				'post_title'  => 'Strategy course',
				'post_status' => 'publish',
				'post_type'   => online_courses_post_type(),
			)
		);

		update_post_meta( $post_id, online_courses_checkout_url_meta_key(), 'https://checkout.example.com/course' );

		ob_start();
		online_courses()->content_domain()->render_meta_box( get_post( $post_id ) );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'name="online_courses_checkout_url"', $output );
		$this->assertStringContainsString( 'type="url"', $output );
		$this->assertStringContainsString( 'value="https://checkout.example.com/course"', $output );
	}

	public function test_save_meta_box_updates_sanitized_value_and_deletes_invalid_value(): void {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$post_id = self::factory()->post->create(
			array(
				'post_title'  => 'Checkout course',
				'post_status' => 'publish',
				'post_type'   => online_courses_post_type(),
			)
		);

		$_POST[ Online_Courses_Content_Domain::META_BOX_NONCE_NAME ] = wp_create_nonce( Online_Courses_Content_Domain::META_BOX_NONCE_ACTION );
		$_POST['online_courses_checkout_url']                       = ' https://checkout.example.com/course ';

		online_courses()->content_domain()->save_meta_box( $post_id );

		$this->assertSame( 'https://checkout.example.com/course', get_post_meta( $post_id, online_courses_checkout_url_meta_key(), true ) );

		$_POST['online_courses_checkout_url'] = 'not-a-valid-url';

		online_courses()->content_domain()->save_meta_box( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, online_courses_checkout_url_meta_key(), true ) );
	}

	public function test_save_meta_box_requires_nonce(): void {
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$post_id = self::factory()->post->create(
			array(
				'post_title'  => 'Nonce course',
				'post_status' => 'publish',
				'post_type'   => online_courses_post_type(),
			)
		);

		$_POST = array(
			'online_courses_checkout_url' => 'https://checkout.example.com/course',
		);

		online_courses()->content_domain()->save_meta_box( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, online_courses_checkout_url_meta_key(), true ) );
	}

	public function test_public_helper_returns_checkout_url_only_for_courses(): void {
		$course_id = self::factory()->post->create(
			array(
				'post_title'  => 'Helper course',
				'post_status' => 'publish',
				'post_type'   => online_courses_post_type(),
			)
		);

		$post_id = self::factory()->post->create(
			array(
				'post_title'  => 'Regular post',
				'post_status' => 'publish',
				'post_type'   => 'post',
			)
		);

		update_post_meta( $course_id, online_courses_checkout_url_meta_key(), 'https://checkout.example.com/course' );
		update_post_meta( $post_id, online_courses_checkout_url_meta_key(), 'https://checkout.example.com/post' );

		$this->assertSame( 'https://checkout.example.com/course', online_courses_get_checkout_url( $course_id ) );
		$this->assertSame( '', online_courses_get_checkout_url( $post_id ) );
	}
}
