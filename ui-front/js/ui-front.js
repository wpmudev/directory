jQuery(document).ready(function($) {
	$('form.confirm-form').hide();
	$('form.dr-contact-form').hide();
});

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

	},
	toggle_contact_form: function() {
		jQuery('.dr-ad-info').hide();
		jQuery('#action-form').hide();
		jQuery('#confirm-form').show();
	},
	cancel_contact_form: function() {
		jQuery('#confirm-form').hide();
		jQuery('.dr-ad-info').show();
		jQuery('#action-form').show();
	},
	cancel: function(key) {
		jQuery('#confirm-form-'+key).hide();
		jQuery('#action-form-'+key).show();
	}
	
};

var js_translate = js_translate || {};
js_translate.image_chosen = 'Image Chosen';

(function($){

	jQuery(document).ready(function($) {
		$('.upload-button input:file').on('change focus click', fileInputs );
	});

	fileInputs = function() {
		var $this = $(this),
		$val = $this.val(),
		valArray = $val.split('\\'),
		newVal = valArray[valArray.length-1],
		$button = $this.siblings('.button'),
		$fakeFile = $this.siblings('.file-holder');
		if(newVal !== '') {
			$button.text(js_translate.image_chosen);
			if($fakeFile.length === 0) {
				$button.after('<span class="file-holder">' + newVal + '</span>');
			} else {
				$fakeFile.text(newVal);
			}
		}
	};

})(jQuery);

