<?php

/* -------------------------------------------------- *
 *
 * INCLUDE FILES
 *
 * -------------------------------------------------- */

include_once( 'wp-list-table-functions.php' );




/* -------------------------------------------------- *
 *
 * RENDER SUBMENU FUNCTIONS
 *
 * 1. Subscribers Submenu
 * 2. Campaigns Submenu
 *
 * -------------------------------------------------- */

// 1. Subscribers Submenu
function lf_email_render_submenu_subscribers() {
	
	// Create an instance of Subscribers List Table class
	$lf_subscribers_list_table = new LF_Subscribers_List_Table();

	// Prepare List Table
	$lf_subscribers_list_table->prepare_items();
	
	?>
	<div class="wrap">
		<div id="icon-users" class="icon32"><br/></div>
		<h2>Leadferry Subscribers</h2>

		<ul class="subsubsub">
			<li class="all"><a href="#" class="current">All <span class="count">(<?php echo $lf_subscribers_list_table->countAll ?>)</span></a></li>
		</ul>

		<form id="campaigns-filter" method="get">
			<input type="hidden" name="post_type" value="lf_email" />
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $lf_subscribers_list_table->display() ?>
		</form>
	</div>
	<script>
		jQuery(document).ready(function($) {
			// On Change Campaign, Update the other
			$( '.alignleft.actions.top select[name="campaign_id"], .alignleft.actions.bottom select[name="campaign_id"]' ).change(function() {
				var val = $(this).val();

				// Set position to change, if top then bottom and vice versa
				var pos = $(this).parent( '.alignleft.actions' ).hasClass( 'top' ) == true ? 'bottom' : 'top';

				// Change value of the other select element
				$( '.alignleft.actions.' + pos + ' select[name="campaign_id"] option' ).each(function() {
					this.selected = this.value == val;
				});
			});

			// On Change User, Update the other
			$( '.alignleft.actions.top select[name="user_id"], .alignleft.actions.bottom select[name="user_id"]' ).change(function() {
				var val = $(this).val();

				// Set position to change, if top then bottom and vice versa
				var pos = $(this).parent( '.alignleft.actions' ).hasClass( 'top' ) == true ? 'bottom' : 'top';

				// Change value of the other select element
				$( '.alignleft.actions.' + pos + ' select[name="user_id"] option' ).each(function() {
					this.selected = this.value == val;
				});
			});
		});
	</script>
	<?php
}

// 2. Campaigns Submenu
function lf_email_render_submenu_campaigns() {
	
	// Create an instance of Subscribers List Table class
	$lf_campaigns_list_table = new LF_Campaigns_List_Table();

	// Prepare List Table
	$lf_campaigns_list_table->prepare_items();
	
	?>
	<div class="wrap">
		<div id="icon-users" class="icon32"><br/></div>
		<h2>Leadferry Campaigns</h2>

		<ul class="subsubsub">
			<li class="all"><a href="#" class="current">All <span class="count">(<?php echo $lf_campaigns_list_table->countAll ?>)</span></a></li>
		</ul>

		<form id="campaigns-filter" method="get">
			<input type="hidden" name="post_type" value="lf_email" />
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<?php $lf_campaigns_list_table->display() ?>
		</form>
	</div>
	<?php
}