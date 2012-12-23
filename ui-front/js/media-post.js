
var WPSetThumbnailHTML, WPSetThumbnailID, WPRemoveThumbnail;

(function($){

WPSetThumbnailHTML = function(html){
	$('.inside', '#postimagediv').html(html);
};

WPSetThumbnailID = function(id){
	var field = $('input[value="_thumbnail_id"]', '#list-table');
	if ( field.size() > 0 ) {
		$('#meta\\[' + field.attr('id').match(/[0-9]+/) + '\\]\\[value\\]').text(id);
	}
};

WPRemoveThumbnail = function(nonce){
	$.post(ajaxurl, {
		action:"set-post-thumbnail", post_id: $('#post_ID').val(), thumbnail_id: -1, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
	}, function(str){
		if ( str == '0' ) {
			alert( setPostThumbnailL10n.error );
		} else {
			WPSetThumbnailHTML(str);
		}
	}
	);
};

})(jQuery);