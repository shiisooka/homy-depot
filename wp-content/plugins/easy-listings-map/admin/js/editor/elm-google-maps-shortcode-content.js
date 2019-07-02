(function( $ ) {
	// Tooltip for labels.
	jQuery( '.controls' ).tipTip( {
		'attribute' : 'data-tip',
		'defaultPosition' : 'top',
		'fadeIn' : 50,
		'fadeOut' : 50,
		'delay' : 200
	} );

	// Inside of this function, $() will work as an alias for jQuery()
	// and other libraries also using $ will not be accessible under this shortcut
	jQuery( '#insert_shortcode' ).on( 'click', function() {
		button_dialog.insert(button_dialog.local_ed);
	} );

	jQuery( '#auto_zoom' ).change( function() {
		var value = jQuery( '#auto_zoom' ).val();
		if ( '1' === value ) {
			// Hiding zoom level control
			jQuery( '#zoom_level_control' ).hide();
		} else {
			// Showing zoom level control
			jQuery( '#zoom_level_control' ).show();
		}
	});

	var button_dialog = {
		local_ed : 'ed',
		init : function( ed ) {
			button_dialog.local_ed = ed;
			tinyMCEPopup.resizeToInnerSize();
		},
		insert: function insert_button( ed ) {
			// Try and remove existing style / blockquote
			tinyMCEPopup.execCommand( 'mceRemoveNode', false, null );

			var property_types = '"';
			jQuery( '#property_types :selected' ).each( function ( i, selected ) {
				property_types += jQuery( selected ).val() + ( i < jQuery( '#property_types :selected' ).length - 1 ? ',' : '' ) +
				( i == jQuery( '#property_types :selected' ).length - 1 ? '"' : '' );
			} );
			if ( property_types == '"' ) {
				// Show error message for property types.
				return alert( 'Please select at least one property type.' );
			}

			var property_status = '';
			jQuery( '#property_status :selected' ).each( function( i, selected ) {
				property_status += jQuery( selected ).val() + ( i < jQuery( '#property_status :selected' ).length - 1 ? ',' : '' );
			});
			property_status = '"' + property_status + '"';

			var location = '';
			jQuery( '#location input:checkbox:checked' ).each( function( i, selected ) {
				location += jQuery( selected ).val() + ( i < jQuery( '#location input:checkbox:checked' ).length - 1 ? ',' : '' );
			});

			var map_types = '';
			jQuery( 'input:checkbox.map_types:checked' ).each( function( i, selected ) {
				map_types += jQuery( selected ).val() + ( i < jQuery( 'input:checkbox.map_types:checked' ).length - 1 ? ',' : '' );
			});
			map_types = '"' + map_types + '"';

			var output = '[elm_google_maps';
			output += ' post_type=' + property_types;
			output += ' status=' + property_status;
			output += location.length ? ' location="' + location + '"' : '';
			output += ' map_types=' + map_types;
			if ( jQuery( '#default_map_type' ).val().length ) {
				output += ' default_map_type="' + jQuery( '#default_map_type' ).val() + '"';
			}
			output += ' limit="' + ( jQuery( '#limit' ).val() > -1 ? jQuery( '#limit' ).val() : '-1' ) + '"';
			output += jQuery( '#map_height' ).val() > 0 ? ' map_style_height="' + jQuery( '#map_height' ).val() + '"' : '';
			output += ' auto_zoom="' + jQuery( '#auto_zoom' ).val() + '"';
			output += ( jQuery( '#map_zoom' ).val() >= 0 && jQuery( '#auto_zoom' ).val() === '0' ) ? ' zoom="' + jQuery( '#map_zoom' ).val() + '"' : '';
			output += ' zoom_events="' + jQuery( '#zoom_events' ).val() + '"';
			output += jQuery( '#cluster_size' ).val() ? ' cluster_size="' + jQuery( '#cluster_size' ).val() + '"' : '';
			output += ' order="'+ jQuery( '#order' ).val() + '"';
			output += ' default_latitude="' + jQuery( '#default_latitude' ).val() + '"';
			output += ' default_longitude="' + jQuery( '#default_longitude' ).val() + '"';
			output += jQuery.trim( jQuery( '#map_id' ).val() ) ? ' map_id="' + jQuery( '#map_id' ).val() + '"' : '';
			// check to see if the TEXT field is blank
			if ( jQuery( '#title' ).val() ) {
				output += ']'+ jQuery( '#title' ).val() + '[/elm_google_maps]';
			}
			// if it is blank, use the selected text, if present
			else {
				output += ']' + button_dialog.local_ed.selection.getContent() + '[/elm_google_maps]';
			}

			tinyMCEPopup.execCommand( 'mceReplaceContent', false, output );

			// Return
			tinyMCEPopup.close();
		}
	};
	tinyMCEPopup.onInit.add( button_dialog.init, button_dialog );
})( jQuery );
