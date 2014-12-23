<?php

/* -------------------------------------------------- *
 *
 * AJAX FUNCTIONS
 *
 * 1. Get for REST POST /campaigns/<id>
 *
 * -------------------------------------------------- */

// 1. Get for REST POST /campaigns/<id>
function ajax_lf_email_get_for_rest_post_campaigns_id() {
	
	// Set Campaign Ids
	$campaign_ids = $_REQUEST['campaign_ids'];

	// Set Posts By Campaign array
	$posts_by_campaign = array();

	if ( count( $campaign_ids ) > 0 ) {
		foreach ( $campaign_ids as $id ) {

			// Get Campaign Details
			$campaign_details = lf_email_util_get_campaign_details( $id );

			// Get Posts by Campaign Id
			$posts = lf_email_util_get_posts_by_campaign( $id );
			
			// Get Subscribers
			$subscribers = lf_email_util_get_subscribers_by_campaign( $id );

			// Format Results
			$posts_results = array();
			foreach ( $posts as $v ) {
				// $details = array(
				// 	'campaign_id'   => $campaign_details['id'],
				// 	'campaign_name' => $campaign_details['name'],
				// 	'title'         => esc_sql( $v->post_title ),
				// 	'html'          => esc_sql( $v->post_content ),
				// 	'text_only'     => esc_sql( $v->post_excerpt ),
				// 	'subscribers'   => $subscribers
				// );
				$details = array(
					'subject'     => esc_sql( $v->post_title ),
					'subscribers' => $subscribers,
					'body'        => array(
						'html' => esc_sql( $v->post_content ),
						'text' => esc_sql( $v->post_excerpt )
					)
				);
				$posts_results[] = $details;
			}

			// Set Posts By Campaign
			$posts_by_campaign[] = array(
				'id'    => $id,
				'posts' => $posts_results
			);
		}
	} // end if
	
	// Return Data
	echo json_encode( $posts_by_campaign );
	die();
}
add_action( 'wp_ajax_lf_email_get_for_rest_post_campaigns_id', 'ajax_lf_email_get_for_rest_post_campaigns_id' );




/* -------------------------------------------------- *
 *
 * UTILITY FUNCTIONS
 *
 * 1. Get Campaign Details 
 * 2. Get Posts By Campaign Id
 * 3. Get Subscribers By Campaign Id
 *
 * -------------------------------------------------- */

// 1. Get Campaign Details
function lf_email_util_get_campaign_details( $id ) {

	global $wpdb;

	$table_campaigns = $wpdb->prefix . 'lf_campaigns';

	// Set Query
	$sql = "SELECT * FROM $table_campaigns WHERE id = $id";

	// Get Results
	$results = $wpdb->get_results( $sql, ARRAY_A );

	// Format Results
	$details;
	if ( count( $results ) > 0 ) {
		$details = $results[0];
	} // end if

	return $details;
}

// 2. Get Posts By Campaign Id
function lf_email_util_get_posts_by_campaign( $id ) {

	// Get Term Id
	$campaign_details = lf_email_util_get_campaign_details( $id );
	$term_id = $campaign_details['term_id'];

	// Get Posts
	$args = array(
		'post_type' => 'lf_email',
		'orderby'   => 'post_date',
		'order'     => 'DESC',
		'tax_query' => array(
			array(
				'taxonomy' => 'lf_email_campaigns_tags',
				'field'    => 'term_id',
				'terms'    => $term_id
			)
		)
	);
	$posts = get_posts( $args );

	return $posts;
}

// 3. Get Subscribers By Campaign Id
function lf_email_util_get_subscribers_by_campaign( $id ) {

	global $wpdb;

	$table_subscribers = $wpdb->prefix . 'lf_subscribers';
	$table_users = $wpdb->prefix . 'users';

	// Set Query
	$sql = "
		SELECT 
			$table_users.user_email
		FROM
			$table_subscribers
		LEFT JOIN
			$table_users ON $table_subscribers.user_id = $table_users.ID
		WHERE
			$table_subscribers.campaign_id = $id
	";

	// Get Results
	$results = $wpdb->get_results( $sql, ARRAY_A );

	// Format Results
	$subscribers = array();
	if ( count( $results ) > 0 ) {
		foreach ( $results as $v ) {
			$subscribers[] = $v['user_email'];
		} // end foreach
	} // end if

	return $subscribers;
}