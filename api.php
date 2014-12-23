<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'library/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that apache_response_headers(oid)
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

// POST route
$app->post(
	'/campaigns/:id',
	function ( $id ) use ( $app ) {

		// global $wpdb;

		// $table_campaigns = $wpdb->prefix . 'lf_campaigns';

		// // Set Query
		// $sql = "SELECT id FROM $table_campaigns WHERE id = $id";

		// // Get Results
		// $results = $wpdb->get_results( $sql, ARRAY_A );

		// // Check if the Campaign Id exists
		// if ( count( $results ) > 0 ) {
		// 	$app->status( 200 );
		// } // end if
		// else {
		// 	$app->status( 500 );
		// } // end else

		// Check if posts is set
		if ( isset( $_REQUEST['posts'] ) ) {
			$app->status( 200 );
		} // end if
		else {
			$app->status( 500 );
		} // end else
	}
);

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
