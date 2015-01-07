<?php

class LFEmailTest extends WP_UnitTestCase {
	function test_action_init() {
		$this->assertEquals(12, has_action('init', 'lf_email_register_post_type'));
	}
	
	function test_lf_email_register_post_type() {
		$this->assertTrue(post_type_exists('lf_email'));
	}
	
	function test_action_save_post_lf_email() {
		$this->assertNotFalse(has_action('save_post_lf_email', 'lf_email_save_post'));
	}
	
	/**
	 * @dataProvider data_lf_email_save_post
	 */
	function test_lf_email_save_post($post_content, $text_only, $post_excerpt) {
		$_POST['lf_email_select_template'] = 'NOTNULL';
		$_POST['lf_email_text_only'] = $text_only;
		$_POST['post_content'] = $post_content;
		$post = $this->factory->post->create_and_get( array(
				'post_type' => 'lf_email',
				'$post_content' => $post_content
		));
		$this->assertEquals($post_excerpt, $post->post_excerpt);
	}
	
	function data_lf_email_save_post() {
		return array(
			array('No HTML untouched Text-only', '', 'No HTML untouched Text-only'),
			array('<p>With <h1>HTML</h1></p> untouched Text-only', '', 'With HTML untouched Text-only'),
			array('No HTML edited Text-only', 'Edited text-only', 'Edited text-only'),
			array('<p>With <h1>HTML</h1></p> edited Text-only', 'Edited text-only', 'Edited text-only'),
		);
	}
}