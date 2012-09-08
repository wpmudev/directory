(function ($) {
$(function () {

/**
 * Handler tutorial resets.
 */
$(".dr-restart_tutorial").click(function () {
	var $me = $(this);
	// Change UI
	$me.after(
		'<img src="' + _dr_data.root_url + 'ui-admin/images/ajax-loader.gif" />'
	).remove();
	// Do call
	$.post(ajaxurl, {
		"action": "dr_restart_tutorial",
		"tutorial": $me.attr("data-dr_tutorial"),
	}, function () {
		window.location.reload();
	});
	return false;
});

});
})(jQuery);
