jQuery( function ( $ ) {

	// Handle switching between the page builder and other tabs
	$( '.wp-editor-tabs' )
		.find( '.wp-switch-editor' )
		.click(function (e) {
			$( '#wp-content-editor-container, #post-status-info' ).show();
			$( '#lf-email-text-only-tab' ).hide();
			$( '#wp-content-wrap' ).removeClass('panels-active');
			$('#content-resize-handle' ).show();
		} ).end()
		.prepend(
		$( '<a id="content-panels" class="hide-if-no-js wp-switch-editor switch-panels">' + $( '#lf-email-text-only-tab h3.hndle span' ).html() + '</a>' )
			.click( function (e) {
				// Switch to the Text-only interface
				e.preventDefault();

				// Hide the standard content editor
				$( '#wp-content-wrap' ).hide();
				setTimeout( function() {
					$( '#post-status-info' ).hide();
				}, 500 );

				// Show page builder and the inside div
				$( '#lf-email-text-only-tab' ).show().find('> .inside').show();

				// Check if Text-only textarea is on .inside div
				if ( ! $( '#lf-email-text-only' ).parent().hasClass( 'inside' ) ) {
					$( '#lf-email-text-only' ).detach().appendTo( $( '#lf-email-text-only-tab > .inside' ) );
				} // end if

				// Triggers full refresh
				$( window ).resize();
			} )
		);

	// Click Switch to Standard
	$( '#lf-email-text-only-tab #lf-email-tab-content .switch-to-standard' ).click( function() {
		// Switch back to the standard editor
		$( '#wp-content-wrap, #post-status-info' ).show();
		$( '#lf-email-text-only-tab' ).hide();
		$( window ).resize();
	} );

	// Move the panels box into a tab of the content editor
	$( '#lf-email-text-only-tab' )
		.insertAfter( '#wp-content-wrap' )
		.hide()
		.find( '.handlediv' ).remove()
		.end()
		.find( '.hndle' ).html( '' ).append(
			$( '#lf-email-tab-content' )
		)
		.end();

	// Clip string on blur if size is at limit
	$( '#lf-email-text-only' ).blur( function() {
		var text = $( this ).val();
		var string_length = 65536; // 64Kb

		if ( text.length > string_length ) {
			$( '#lf-email-text-only' ).val( text.substr( 0, string_length ) );
			alert( "'Text-only' is clipped due to size limit." );
		}
	} );
} );