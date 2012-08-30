var dr_listings = {
	edit: function( key ) {
		jQuery( '#action-form' ).attr( 'action', dr_edit );
		jQuery( '#action-form input[name="action"]' ).val( 'edit_listing' );
		jQuery( '#action-form input[name="post_id"]' ).val( key );
		jQuery( '#action-form' ).submit();
	},
	toggle_delete: function( key ) {
		jQuery( '#delete-confirm-' + key ).parent().find( 'span' ).hide();
		jQuery( '#delete-confirm-' + key ).show();
	},
	toggle_delete_yes: function( key ) {
		jQuery( '#action-form input[name="action"]' ).val( 'delete_listing' );
		jQuery( '#action-form input[name="post_id"]' ).val( key );
		jQuery( '#action-form' ).submit();

	},
	toggle_delete_no: function( key ) {
		jQuery( '#delete-confirm-' + key ).parent().find( 'span' ).show();
		jQuery( '#delete-confirm-' + key ).hide();

	}
};
