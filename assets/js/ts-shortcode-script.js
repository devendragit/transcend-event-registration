jQuery(document).ready(function($) {

	$('.select-redirect').on('change', function(e) {
		e.preventDefault();
		var url = $('option:selected', this).attr('data-url');
		window.location.href = url;
	});	

});