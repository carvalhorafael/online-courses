<?php
/**
 * Blocks integration tests.
 *
 * @package Online_Courses
 */

final class BlocksTest extends WP_UnitTestCase {
	public function test_course_blocks_are_registered(): void {
		$registry = WP_Block_Type_Registry::get_instance();

		$this->assertTrue( $registry->is_registered( 'online-courses/learning-outcomes' ) );
		$this->assertTrue( $registry->is_registered( 'online-courses/course-curriculum' ) );
		$this->assertTrue( $registry->is_registered( 'online-courses/requirements' ) );
		$this->assertTrue( $registry->is_registered( 'online-courses/audience-fit' ) );
		$this->assertTrue( $registry->is_registered( 'online-courses/instructor-bio' ) );
	}

	public function test_learning_outcomes_block_renders_design_system_markup(): void {
		$html = do_blocks(
			'<!-- wp:online-courses/learning-outcomes {"items":["Map the problem","Apply the method"]} /-->'
		);

		$this->assertStringContainsString( 'es-value-stack', $html );
		$this->assertStringContainsString( 'Map the problem', $html );
		$this->assertStringContainsString( 'Apply the method', $html );
	}

	public function test_curriculum_block_renders_design_system_markup(): void {
		$html = do_blocks(
			'<!-- wp:online-courses/course-curriculum {"sections":[{"title":"Module 1","meta":"2 lessons","lessons":[{"title":"Lesson 1","duration":"4 min","preview":true}]}]} /-->'
		);

		$this->assertStringContainsString( 'es-course-curriculum', $html );
		$this->assertStringContainsString( 'Module 1', $html );
		$this->assertStringContainsString( 'Preview lesson', $html );
	}
}
