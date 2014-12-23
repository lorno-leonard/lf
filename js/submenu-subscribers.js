jQuery( function ( $ ) {

	// On click Send Emails
	$( 'div.button.send-emails' ).click( function() {
		var checkboxes = $( 'input[name="leadferrysubscriber[]"]:checked' );

		// Check if there Checked Campaigns
		if ( checkboxes.length > 0 ) {
			var campaigns_id_arr = [];
			$.each( checkboxes, function() {
				campaigns_id_arr.push( $( this ).val() );
			} );

			var params = {
				action: 'lf_email_get_for_rest_post_campaigns_id',
				campaign_ids: campaigns_id_arr
			};
			$.post( ajaxurl, params, function( response ) {
				var decode = JSON.parse( response );
				
				// RESTful POST /campaigns/:id
				if ( decode.length > 0 ) {
					for ( var i = 0; i < decode.length; i++ ) {
						var data = decode[i];

						// Set URL
						var url = '/leadferry/wp-content/plugins/lf/api.php/campaigns/' + data.id;

						// POST
						var data_params = {
							posts: data.posts
						}
						$.post( url, data_params );
					} // end for
				} // end if
			} );
		}
		else {
			alert( 'Please check at least one Subscriber.' );
		}
	} );
} );