<?php

class LFEmailTestActions extends WP_UnitTestCase {

	// Test Action - init
	function test_action_init() {
		$this->assertNotFalse( has_action( 'init', 'lf_email_register_taxonomy') );
		$this->assertEquals( 11, has_action( 'init', 'lf_email_register_taxonomy' ) );

		$this->assertNotFalse( has_action( 'init', 'lf_email_register_post_type') );
		$this->assertEquals( 12, has_action( 'init', 'lf_email_register_post_type' ) );
	}

	// Test Action - admin_menu
	function test_action_admin_menu() {
		$this->assertNotFalse( has_action( 'admin_menu', 'lf_email_add_submenus') );
	}

	// Test Action - add_meta_boxes
	function test_action_add_meta_boxes() {
		$this->assertNotFalse( has_action( 'add_meta_boxes', 'lf_email_add_meta_box') );
		$this->assertEquals( 11, has_action( 'add_meta_boxes', 'lf_email_add_meta_box' ) );

		$this->assertNotFalse( has_action( 'add_meta_boxes', 'lf_email_remove_unwanted_meta_boxes') );
		$this->assertEquals( 12, has_action( 'add_meta_boxes', 'lf_email_remove_unwanted_meta_boxes' ) );
	}

	// Test Action - save_post_lf_email
	function test_action_save_post_lf_email() {
		$this->assertNotFalse( has_action( 'save_post_lf_email', 'lf_email_save_post') );
	}

	// Test Action - admin_head
	function test_action_admin_head() {
		$this->assertNotFalse( has_action( 'admin_head', 'lf_email_add_custom_button_editor') );
	}

	// Test Action - admin_print_styles
	function test_action_admin_print_styles() {
		$this->assertNotFalse( has_action( 'admin_print_styles-post-new.php', 'lf_email_enqueue_styles') );
		$this->assertNotFalse( has_action( 'admin_print_styles-post.php', 'lf_email_enqueue_styles') );
	}

	// Test Action - admin_print_scripts
	function test_action_admin_print_scripts() {
		$this->assertNotFalse( has_action( 'admin_print_scripts-post-new.php', 'lf_email_enqueue_scripts') );
		$this->assertNotFalse( has_action( 'admin_print_scripts-post.php', 'lf_email_enqueue_scripts') );
		$this->assertNotFalse( has_action( 'admin_print_scripts', 'lf_email_enqueue_scripts_subscribers') );
	}
}