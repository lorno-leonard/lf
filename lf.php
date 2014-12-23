<?php
/**
 * @package Leadferry Email
 */
/*
Plugin Name: Leadferry Email
Plugin URI: 
Description: Leadferry Email
Version: 1.0
Author: Leadferry
Author URI: http://ec2-54-169-93-43.ap-southeast-1.compute.amazonaws.com/redmine
*/

/* -------------------------------------------------- *
 *
 * INCLUDE FILES
 *
 * -------------------------------------------------- */

include_once( 'functions/ajax-functions.php' );
include_once( 'functions/render-submenu-functions.php' );




/* -------------------------------------------------- *
 *
 * WORDPRESS ACTIONS/FILTERS/HOOKS FUNCTIONS
 *
 *  1. Plugin Activation
 *  2. Plugin Deactivation
 *  3. Register Taxonomy
 *  4. Register Post Type
 *  5. Add Submenus
 *  6. Add Meta Box
 *  7. Remove Unwanted Meta Boxes
 *  8. Save Leadferry Email Custom Post Type
 *  9. Add Custom Button to Editor
 * 10. Enqueue Styles
 * 11. Enqueue Scripts
 *
 * -------------------------------------------------- */

// 1. Plugin Activation
function lf_email_plugin_activation() {

	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_campaigns = $wpdb->prefix . 'lf_campaigns';
	$table_subscribers = $wpdb->prefix . 'lf_subscribers';
	$table_users = $wpdb->prefix . 'users';

	$charset_collate = $wpdb->get_charset_collate();

	// Create Campaigns Table
	$sql = "
	CREATE TABLE IF NOT EXISTS $table_campaigns (
		id int NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		term_id int NOT NULL,
		PRIMARY KEY (id)
	);
	";
	dbDelta( $sql );

	// Create Subscribers Table
	$sql = "
	CREATE TABLE IF NOT EXISTS $table_subscribers (
		id int NOT NULL AUTO_INCREMENT,
		campaign_id int NOT NULL,
		user_id bigint UNSIGNED NOT NULL,
		PRIMARY KEY (id)
	);
	";
	dbDelta( $sql );
	
	// Insert Test Data
	$wpdb->query( "INSERT INTO $table_campaigns (id, name, term_id) VALUES (1, 'Campaign 1', 1)" );
	$wpdb->query( "INSERT INTO $table_campaigns (id, name, term_id) VALUES (2, 'Campaign 2', 2)" );
	$wpdb->query( "INSERT INTO $table_campaigns (id, name, term_id) VALUES (3, 'Campaign 3', 3)" );

	$wpdb->query( "INSERT INTO $table_subscribers (campaign_id, user_id) VALUES (1, 1);" );
	$wpdb->query( "INSERT INTO $table_subscribers (campaign_id, user_id) VALUES (2, 1);" );
	$wpdb->query( "INSERT INTO $table_subscribers (campaign_id, user_id) VALUES (3, 1);" );
}
register_activation_hook( __FILE__, 'lf_email_plugin_activation' );

// 2. Plugin Deactivation
function lf_email_plugin_deactivation() {

	global $wpdb;

	$table_campaigns = $wpdb->prefix . 'lf_campaigns';
	$table_subscribers = $wpdb->prefix . 'lf_subscribers';

	// Truncate Tables
	$wpdb->query( "TRUNCATE TABLE $table_campaigns;" );
	$wpdb->query( "TRUNCATE TABLE $table_subscribers;" );
}
register_deactivation_hook( __FILE__, 'lf_email_plugin_deactivation' );

// 3. Register Taxonomy
function lf_email_register_taxonomy() {

	$labels = array(
		'name'                       => 'Campaign Tags',
		'singular_name'              => 'Campaign Tag',
		'menu_name'                  => 'Campaign Tag',
		'all_items'                  => 'All Campaign Tags',
		'parent_item'                => 'Parent Campaign Tag',
		'parent_item_colon'          => 'Parent Campaign Tag:',
		'new_item_name'              => 'New Campaign Tag',
		'add_new_item'               => 'Add New Campaign Tag',
		'edit_item'                  => 'Edit Campaign Tag',
		'update_item'                => 'Update Campaign Tag',
		'separate_items_with_commas' => 'Separate Campaign Tag with commas',
		'search_items'               => 'Search Campaign Tag',
		'add_or_remove_items'        => 'Add or remove Campaign Tag',
		'choose_from_most_used'      => 'Choose from the most used Campaign Tag',
		'not_found'                  => 'Not Found'
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true
	);

	register_taxonomy( 'lf_email_campaigns_tags', array( 'lf_email' ), $args );
}
add_action( 'init', 'lf_email_register_taxonomy', 11 );

// 4. Register Post Type
function lf_email_register_post_type() {

	$labels = array(
		'name'                => 'Leadferry Emails',
		'singular_name'       => 'Leadferry Email',
		'menu_name'           => 'Leadferry Emails',
		'parent_item_colon'   => 'Parent Leadferry Email:',
		'all_items'           => 'All Emails',
		'view_item'           => 'View Leadferry Email',
		'add_new_item'        => 'Add New Leadferry Email',
		'add_new'             => 'New Email',
		'edit_item'           => 'Edit Leadferry Email',
		'update_item'         => 'Update Leadferry Email',
		'search_items'        => 'Search Leadferry Emails',
		'not_found'           => 'No Leadferry Emails found',
		'not_found_in_trash'  => 'No Leadferry Emails found in Trash'
	);

	$rewrite = array(
		'slug'                => 'lf_email',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true
	);

	$args = array(
		'label'               => 'lf_email',
		'description'         => 'Leadferry Email Description',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 25,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
		'taxonomies'          => array( 'lf_email_campaigns_tags' )
	);

	register_post_type( 'lf_email', $args );
}
add_action( 'init', 'lf_email_register_post_type', 12 );

// 5. Add Submenus
function lf_email_add_submenus() {

	add_submenu_page( 'edit.php?post_type=lf_email', 'Campaigns', 'Campaigns', 'manage_options', 'lf_email_submenu_campaigns', 'lf_email_submenu_campaigns' );
	add_submenu_page( 'edit.php?post_type=lf_email', 'Subscribers', 'Subscribers', 'manage_options', 'lf_email_subscribers', 'lf_email_submenu_subscribers' );
}
add_action( 'admin_menu', 'lf_email_add_submenus' );

// 6. Add Meta Box
function lf_email_add_meta_box() {

	// Add Options Meta Box
	add_meta_box(
		'lf-email-options',
		'Leadferry Email Options',
		'lf_email_util_render_options_meta_box',
		'lf_email',
		'normal'
	);

	// Add Text-only Tab Meta Box
	add_meta_box(
		'lf-email-text-only-tab',
		'Text-only',
		'lf_email_util_render_text_only_tab_meta_box',
		'lf_email',
		'advanced',
		'high'
	);
}
add_action( 'add_meta_boxes', 'lf_email_add_meta_box', 11 );

// 7. Remove Unwanted Meta Boxes
function lf_email_remove_unwanted_meta_boxes() {

	lf_email_util_remove_unwanted_meta_boxes();
}
add_action( 'add_meta_boxes', 'lf_email_remove_unwanted_meta_boxes', 12 );

// 8. Save Leadferry Email Custom Post Type
function lf_email_save_post( $post_id ) {

	// Check if Post is new or not
	$is_new_post = get_post_meta( $post_id, 'lf_email_select_template', true ) == '' ? true : false;
	
	// Update Custom Post Type Meta
	update_post_meta( $post_id, 'lf_email_select_template', $_POST['lf_email_select_template'] );

	// Update Post
	if ( ! wp_is_post_revision( $post_id ) ) {

		// Unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_lf_email', 'lf_email_save_post' );

		// Update the post
		$string_length = 65536; // 64Kb
		$text_only = $is_new_post && $_POST['lf_email_text_only'] == '' ? esc_sql( substr( wp_strip_all_tags( $_POST['post_content'] ), 0, $string_length ) ) : esc_sql( $_POST['lf_email_text_only'] );
		
		$post_args = array(
			'ID'           => $post_id,
			'post_excerpt' => $text_only
		);
		wp_update_post( $post_args );

		// Re-hook this function
		add_action( 'save_post_lf_email', 'lf_email_save_post' );
	} // end if
}
add_action( 'save_post_lf_email', 'lf_email_save_post' ); // As of WP 3.7, an alternative action has been introduced, which is called for specific post types: save_post_{post_type}

// 9. Add Custom Button to Editor
function lf_email_add_custom_button_editor() {

	$screen = get_current_screen();
	
	
	// Check if current screen id is the Custom Post Type and option for Rich Editing is 'true'
	if ( $screen->id  == 'lf_email' && get_user_option( 'rich_editing' ) == 'true' ) {
		add_filter( 'mce_external_plugins', 'lf_email_util_button_tinymce_plugin' );
		add_filter( 'mce_buttons', 'lf_email_util_register_mce_button' );
	} // end if
}
add_action( 'admin_head', 'lf_email_add_custom_button_editor' );

// 10. Enqueue Styles
function lf_email_enqueue_styles() {

	$screen = get_current_screen();

	// Check if current screen id is the Custom Post Type
	if ( $screen->id  == 'lf_email' ) {
		wp_enqueue_style( 'lf-email-editor-custom-tab', plugin_dir_url(__FILE__) . 'css/editor-custom-tab.css', array(), '1.0', 'screen' );
	} // end if
}
add_action( 'admin_print_styles-post-new.php', 'lf_email_enqueue_styles' );
add_action( 'admin_print_styles-post.php', 'lf_email_enqueue_styles' );

// 11. Enqueue Scripts

// Add Scripts to post.php and post-new.php
function lf_email_enqueue_scripts() {

	$screen = get_current_screen();
	
	// Check if current screen id is the Custom Post Type
	if ( $screen->id  == 'lf_email' ) {
		wp_enqueue_script( 'lf-email-editor-custom-tab', plugin_dir_url(__FILE__) . 'js/editor-custom-tab.js', array( 'jquery' ), '1.0', true );
	} // end if
}
add_action( 'admin_print_scripts-post-new.php', 'lf_email_enqueue_scripts' );
add_action( 'admin_print_scripts-post.php', 'lf_email_enqueue_scripts' );

// Add Scripts to Leadferry Subscribers
function lf_email_enqueue_scripts_subscribers() {

	$screen = get_current_screen();

	if ( $screen->id == 'lf_email_page_lf_email_subscribers' ) {
		wp_enqueue_script( 'lf-email-submenu-subscribers', plugin_dir_url(__FILE__) . 'js/submenu-subscribers.js', array( 'jquery' ), '1.0', true );
	} // end if
}
add_action( 'admin_print_scripts', 'lf_email_enqueue_scripts_subscribers' );




/* -------------------------------------------------- *
 *
 * SUBMENU FUNCTIONS
 *
 * 1. Subscribers
 * 2. Campaigns
 *
 * -------------------------------------------------- */

// 1. Subscribers
function lf_email_submenu_subscribers() {

	lf_email_render_submenu_subscribers();
}

// 2. Campaigns
function lf_email_submenu_campaigns() {

	lf_email_render_submenu_campaigns();
}




/* -------------------------------------------------- *
 *
 * UTILITY FUNCTIONS
 *
 * 1. Render Options Meta Box
 * 2. Render Text-only Tab Meta Box
 * 2. Get Templates
 * 3. Remove Unwanted Meta Boxes
 * 4. Get Meta Boxes
 * 5. Format Meta Boxes
 * 6. Add Button as TinyMCE plugin
 * 7. Register TinyMCE Button
 *
 * -------------------------------------------------- */

// 1. Callback function that renders the Option the Meta Box
function lf_email_util_render_options_meta_box() {

	global $post;
	$templates = lf_email_util_get_templates();
	$meta = get_post_meta( $post->ID, 'lf_email_select_template', true );
	
	?>
	<table class="form-table">
		<tr>
			<th>Select Layout</th>
			<td>
				<select name="lf_email_select_template">
				<?php foreach ( $templates as $template ): ?>
					<option <?php echo $meta == $template['value'] ? 'selected="selected"' : '' ?> value="<?php echo $template['value'] ?>"><?php echo $template['name'] ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
	<?php
}

// 2. Render Text-only Tab Meta Box
function lf_email_util_render_text_only_tab_meta_box() {

	global $post;
	
	?>
	<div id="lf-email-tab-content">
		<span>Text-only</span>
		<a href="#" class="switch-to-standard">Switch to Editor</a>
		<textarea id="lf-email-text-only" name="lf_email_text_only"><?php echo $post->post_excerpt ?></textarea>
	</div>
	<?php
}

// 2. Get Templates
function lf_email_util_get_templates() {

	$templates = array(
		array(
			'name' => 'Default',
			'value' => 'default'
		),
		array(
			'name' => 'No SideBar',
			'value' => 'no_sidebar'
		)
	);

	return $templates;
}

// 3. Remove Unwanted Meta Boxes
function lf_email_util_remove_unwanted_meta_boxes() {

	// Default Meta Boxes not to be removed
	$default_meta_boxes = array(
		'authordiv',
		'categorydiv',
		'commentstatusdiv',
		'commentsdiv',
		'formatdiv',
		'pageparentdiv',
		'postcustom',
		'postexcerpt',
		'postimagediv',
		'revisionsdiv',
		'slugdiv',
		'submitdiv',
		'tagsdiv-post_tag',
		'trackbacksdiv',

		// of course your own meta box(es)
		'lf-email-options',
		'lf-email-text-only-tab',
		'tagsdiv-lf_email_campaigns_tags'
	);

	$meta_boxes = lf_email_util_get_meta_boxes();

	foreach ( $meta_boxes as $meta_box ) {

		// Remove Meta Box not included in the Default Meta Boxes
		if ( ! in_array( $meta_box['id'], $default_meta_boxes ) ) {

			remove_meta_box( $meta_box['id'], 'lf_email', $meta_box['context'] );
		} // end if
	} // end foreach
}

// 4. Get Meta Boxes
function lf_email_util_get_meta_boxes() {

	global $wp_meta_boxes;
	
	// Let's format first the Meta Boxes existing on our Custom Type
	$meta_boxes = lf_email_util_format_meta_boxes( $wp_meta_boxes['lf_email'] );

	return $meta_boxes;
}

// 5. Format Meta Boxes
function lf_email_util_format_meta_boxes( $meta_boxes = null ) {

	if ( $meta_boxes == null || empty( $meta_boxes ) ) {
		return array();
	} // end if

	$formatted_meta_boxes = array();

	foreach ( $meta_boxes as $context => $context_value ) {
		foreach ( $context_value as $priority => $priority_value ) {
			foreach ( $priority_value as $meta_box ) {

				// Meta Box details
				$details =  array(
					'id' => $meta_box['id'],
					'context' => $context
				);

				$formatted_meta_boxes[] = $details;

			} // end foreach
		} // end foreach
	} // end foreach

	return $formatted_meta_boxes;
}

// 6. Add Button as TinyMCE plugin
function lf_email_util_button_tinymce_plugin( $plugin_array ) {

	$plugin_array['lf_email_mce_button'] = plugin_dir_url( __FILE__ ) . 'js/editor-custom-button.js';
	return $plugin_array;
}

// 7. Register TinyMCE Button
function lf_email_util_register_mce_button( $buttons ) {

	array_push( $buttons, 'lf_email_mce_button' );
	return $buttons;
}