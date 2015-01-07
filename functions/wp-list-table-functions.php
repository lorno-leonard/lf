<?php

/* -------------------------------------------------- *
 *
 * INCLUDE FILES
 *
 * -------------------------------------------------- */

include_once( 'lf-class-wp-list-table.php' );




/* -------------------------------------------------- *
 *
 * CLASSES
 *
 * 1. Subscribers Class
 * 2. Campaigns Class
 *
 * -------------------------------------------------- */

// 1. Subscribers Class
Class LF_Subscribers_List_Table extends LF_WP_List_Table {

	var $data;
	var $status;
	var $countAll;

	function __construct() {

		global $status, $page, $wpdb;
		$filter_arr = array();
		$filter_string = '';
		$table_subscribers = $wpdb->prefix . 'lf_subscribers';
		$table_campaigns = $wpdb->prefix . 'lf_campaigns';
		$table_users = $wpdb->prefix . 'users';

		//Set parent defaults
		parent::__construct(
			array(
				'singular'  => 'Leadferry Subscriber',     //singular name of the listed records
				'plural'    => 'Leadferry Subscribers',    //plural name of the listed records
				'ajax'      => false,                    //does this table support ajax?
				'screen'    => 'interval-list'           //hook suffix
			)
		);

		// Default Filter, so that the WHERE clause in the SQL query will work
		$filter_arr[] = '1 = 1';

		// Status Filter
		$this->status = 'all';
		// if ( !isset($_REQUEST['status']) || $_REQUEST['status'] == 'all' || $_REQUEST['status'] == '' ) {
		// 	$status = 'all';
		// }
		// else if ( $_REQUEST['status'] == 'trash' ) {
		// 	$status = 'trash';
		// }
		// $this->status = $status;
		// $filter_arr[] = 'trash = ' . ( $status == 'all' ? 'FALSE' : 'TRUE' );

		// Subscriber Id Filter
		if ( isset( $_REQUEST['campaign_id'] ) && $_REQUEST['campaign_id'] != '' ) {
			$filter_arr[] = "$table_subscribers.campaign_id = " . esc_sql( $_REQUEST['campaign_id'] );
		}

		// User Filter
		if ( isset( $_REQUEST['user_id'] ) && $_REQUEST['user_id'] != '' ) {
			$filter_arr[] = "$table_subscribers.user_id = " . esc_sql( $_REQUEST['user_id'] );
		}

		// Set Filter String
		$filter_string = implode( ' AND ', $filter_arr );

		// Get Data
		$results = $wpdb->get_results( "
			SELECT
				$table_subscribers.*,
				$table_campaigns.name AS title,
				$table_users.display_name AS user
			FROM 
				$table_subscribers
			LEFT JOIN
				$table_campaigns ON $table_subscribers.campaign_id = $table_campaigns.id
			LEFT JOIN
				$table_users ON $table_subscribers.user_id = $table_users.ID
			WHERE $filter_string
			ORDER BY
				$table_campaigns.name
		", ARRAY_A );
		$this->data = $results;

		// Count Status
		$this->countAll = count( $wpdb->get_results( "SELECT * FROM $table_subscribers", ARRAY_A ) );
	}

	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'title':
			case 'user':
				return $item[$column_name];
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb( $item ) {

		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
		);
	}

	function get_columns() {

		$columns = array(
			'cb'    => '<input type="checkbox" />', //Render a checkbox instead of text
			'title' => 'Campaign',
			'user'  => 'User'
		);
		return $columns;
	}

	function get_sortable_columns() {

		$sortable_columns = array(
			'title' => array( 'title', false ),
			'user'  => array( 'user', false )
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {

		if ( $this->status == 'all' ) {
			$actions = array();
		}
		else {
			$actions = array();
		}

		return $actions;
	}

	function process_bulk_action() {
		global $wpdb;

		if ( $this->current_action() == 'trash' ) {

		}
		else if ( $this->current_action() == 'restore' ) {

		}
		else if ( $this->current_action() == 'delete' ) {

		}
	}

	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column 
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example 
		 * package slightly different than one you might build on your own. In 
		 * this example, we'll be using array manipulation to sort and paginate 
		 * our data. In a real-world implementation, you will probably want to 
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = $this->data;


		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 * 
		 * In a real-world situation involving a database, you would probably want 
		 * to handle sorting by passing the 'orderby' and 'order' values directly 
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder($a,$b) {
			$orderby = ( !empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
			$order = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result = strcmp( $a[$orderby], $b[$orderby] ); //Determine sort order
			return ( $order === 'asc' ) ? $result : -$result; //Send final sort direction to usort
		}
		usort( $data, 'usort_reorder' );

		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 * 
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 * 
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/


		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently 
		 * looking at. We'll need this later, so you should always include it in 
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array. 
		 * In real-world use, this would be the total number of items in your database, 
		 * without filtering. We'll need this later, so you should always include it 
		 * in your own package classes.
		 */
		$total_items = count( $data );


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to 
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );



		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,                      //WE have to calculate the total number of items
				'per_page'    => $per_page,                         //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
			)
		);
	}

	function extra_tablenav( $which ) {

		global $wpdb;

		// Table Names
		$table_subscribers = $wpdb->prefix . 'lf_subscribers';
		$table_campaigns = $wpdb->prefix . 'lf_campaigns';
		$table_users = $wpdb->prefix . 'users';

		// Get Campaigns
		$campaigns_arr = $wpdb->get_results( "SELECT id, name FROM $table_campaigns ORDER BY name", ARRAY_A );

		// Get Users
		$users_arr = $wpdb->get_results( "SELECT ID AS id, display_name FROM $table_users ORDER BY display_name", ARRAY_A );

		// Top
		if ( $which == "top" ){
		?>
			<div class="alignleft actions top">
				<select name="campaign_id">
					<option value="" <?php echo !isset( $_REQUEST['campaign_id'] ) ? 'selected' : '' ?>>View All Campaigns</option>
					<?php foreach( $campaigns_arr as $v ): ?>
					<option value="<?php echo $v['id'] ?>" <?php echo isset( $_REQUEST['campaign_id'] ) && $_REQUEST['campaign_id'] == $v['id'] ? 'selected' : '' ?>><?php echo $v['name'] ?></option>
					<?php endforeach; // end foreach ?>
				</select>
				<select name="user_id">
					<option value="" <?php echo !isset( $_REQUEST['user_id'] ) ? 'selected' : '' ?>>View All Users</option>
					<?php foreach( $users_arr as $v ): ?>
					<option value="<?php echo $v['id'] ?>" <?php echo isset( $_REQUEST['user_id'] ) && $_REQUEST['user_id'] == $v['id'] ? 'selected' : '' ?>><?php echo $v['display_name'] ?></option>
					<?php endforeach; // end foreach ?>
				</select>
				<button class="button action">Filter</button>
				<div class="button send-emails">Send Emails</div>
			</div>
		<?php
		}

		// Bottom
		if ( $which == "bottom" ){
		?>
			<div class="alignleft actions bottom">
				<select name="campaign_id">
					<option value="" <?php echo !isset( $_REQUEST['campaign_id'] ) ? 'selected' : '' ?>>View All Campaigns</option>
					<?php foreach( $campaigns_arr as $v ): ?>
					<option value="<?php echo $v['id'] ?>" <?php echo isset( $_REQUEST['campaign_id'] ) && $_REQUEST['campaign_id'] == $v['id'] ? 'selected' : '' ?>><?php echo $v['name'] ?></option>
					<?php endforeach; // end foreach ?>
				</select>
				<select name="user_id">
					<option value="" <?php echo !isset( $_REQUEST['user_id'] ) ? 'selected' : '' ?>>View All Users</option>
					<?php foreach( $users_arr as $v ): ?>
					<option value="<?php echo $v['id'] ?>" <?php echo isset( $_REQUEST['user_id'] ) && $_REQUEST['user_id'] == $v['id'] ? 'selected' : '' ?>><?php echo $v['display_name'] ?></option>
					<?php endforeach; // end foreach ?>
				</select>
				<button class="button action">Filter</button>
				<div class="button send-emails">Send Emails</div>
			</div>
		<?php
		}
	}
}

// 2. Campaigns Class
Class LF_Campaigns_List_Table extends LF_WP_List_Table {

	var $data;
	var $status;
	var $countAll;

	function __construct() {

		global $status, $page, $wpdb;
		$filter_arr = array();	
		$filter_string = '';
		$table_subscribers = $wpdb->prefix . 'lf_subscribers';
		$table_campaigns = $wpdb->prefix . 'lf_campaigns';
		$table_users = $wpdb->prefix . 'users';

		//Set parent defaults
		parent::__construct(
			array(
				'singular'  => 'Leadferry Campaign',     //singular name of the listed records
				'plural'    => 'Leadferry Campaigns',    //plural name of the listed records
				'ajax'      => false,                    //does this table support ajax?
				'screen'    => 'interval-list'           //hook suffix
			)
		);

		// Default Filter, so that the WHERE clause in the SQL query will work
		$filter_arr[] = '1 = 1';

		// Status Filter
		$this->status = 'all';
		// if ( !isset($_REQUEST['status']) || $_REQUEST['status'] == 'all' || $_REQUEST['status'] == '' ) {
		// 	$status = 'all';
		// }
		// else if ( $_REQUEST['status'] == 'trash' ) {
		// 	$status = 'trash';
		// }
		// $this->status = $status;
		// $filter_arr[] = 'trash = ' . ( $status == 'all' ? 'FALSE' : 'TRUE' );

		// Set Filter String
		$filter_string = implode( ' AND ', $filter_arr );

		// Get Data
		$results = $wpdb->get_results( "
			SELECT
				id AS title,
				name
			FROM 
				$table_campaigns
			WHERE $filter_string
			ORDER BY
				name
		", ARRAY_A );
		$this->data = $results;

		// Count Status
		$this->countAll = count( $wpdb->get_results( "SELECT * FROM $table_campaigns", ARRAY_A ) );
	}

	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'title':
			case 'name':
				return $item[$column_name];
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_cb( $item ) {

		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
		);
	}

	function get_columns() {

		$columns = array(
			'cb'    => '<input type="checkbox" />', //Render a checkbox instead of text
			'title' => 'Id',
			'name'  => 'Name'
		);
		return $columns;
	}

	function get_sortable_columns() {

		$sortable_columns = array(
			'title' => array( 'title', false ),
			'name'  => array( 'name', false )
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {

		if ( $this->status == 'all' ) {
			$actions = array();
		}
		else {
			$actions = array();
		}

		return $actions;
	}

	function process_bulk_action() {
		global $wpdb;

		if ( $this->current_action() == 'trash' ) {

		}
		else if ( $this->current_action() == 'restore' ) {

		}
		else if ( $this->current_action() == 'delete' ) {

		}
	}

	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column 
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example 
		 * package slightly different than one you might build on your own. In 
		 * this example, we'll be using array manipulation to sort and paginate 
		 * our data. In a real-world implementation, you will probably want to 
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = $this->data;


		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 * 
		 * In a real-world situation involving a database, you would probably want 
		 * to handle sorting by passing the 'orderby' and 'order' values directly 
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder($a,$b) {
			$orderby = ( !empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
			$order = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result = strcmp( $a[$orderby], $b[$orderby] ); //Determine sort order
			return ( $order === 'asc' ) ? $result : -$result; //Send final sort direction to usort
		}
		usort( $data, 'usort_reorder' );

		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 * 
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 * 
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/


		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently 
		 * looking at. We'll need this later, so you should always include it in 
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array. 
		 * In real-world use, this would be the total number of items in your database, 
		 * without filtering. We'll need this later, so you should always include it 
		 * in your own package classes.
		 */
		$total_items = count( $data );


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to 
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );



		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,                      //WE have to calculate the total number of items
				'per_page'    => $per_page,                         //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
			)
		);
	}

	function extra_tablenav( $which ) {

		global $wpdb;

		// Top
		if ( $which == "top" ){
		
		}

		// Bottom
		if ( $which == "bottom" ){
		
		}
	}
}