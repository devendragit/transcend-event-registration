jQuery(document).ready(function($) {

	$('.select-redirect').on('change', function(e) {
		e.preventDefault();
		var url = $('option:selected', this).attr('data-url');
		if(url){
			window.location.href = url;
		}
	});	

	$('.ts-tabs').tabs();
});