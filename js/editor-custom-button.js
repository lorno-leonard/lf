( function() {
	tinymce.PluginManager.add( 'lf_email_mce_button', function( editor, url ) {
		editor.addButton( 'lf_email_mce_button', {
			text: 'Generate/Update Text-only',
			icon: false,
			onclick: function() {
				jQuery( function ( $ ) {
					var text = $( editor.getContent() ).text();
					var string_length = 65536; // 64Kb

					if ( text.length > string_length ) {
						$( '#lf-email-text-only' ).val( text.substr( 0, string_length ) );
						alert( "'Text-only' is clipped due to size limit." );
					}
					else {
						$( '#lf-email-text-only' ).val( $( editor.getContent() ).text() );
					}
				} );
			}
		} );
	} );
} )();