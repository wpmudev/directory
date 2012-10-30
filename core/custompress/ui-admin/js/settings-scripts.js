(function($) {

	$(document).ready(function($) {
		// bind functions

		//Make the combo box for date formats
		$('#date_format').combobox([
		'mm/dd/yy',
		'mm-dd-yy',
		'mm.dd.yy',
		'dd/mm/yy',
		'dd-mm-yy',
		'dd.mm.yy',
		'yy/mm/dd',
		'yy-mm-dd',
		'yy.mm.dd',
		'M d, y',
		'MM d, yy',
		'd M, yy',
		'd MM, yy',
		'DD, d MM, yy',
		"'day' d 'of' MM 'in the year' yy"
		]);

	});

})(jQuery);

