jQuery(document).ready(function($) {
    $('.studio-registration').on('submit', function(e){
		e.preventDefault();
		var validated = true;
		if($(this).hasClass('validate')){
			if($(this).hasClass('competition-page')) {
				$('.routine-container .row').each(function() {
					var empty = true;
					var row = $(this);
					var name = row.find('.routine-name');
					var dancers = row.find('.routine-dancers');
					if(name.val()!='') empty = false;
					if(dancers.val()!='') empty = false;
					if(empty===true) row.remove();
				});
			}
			if($(this).hasClass('roster-page')) {
				$('.roster-container .row').each(function() {
					var row = $(this);
					var date = row.find('.formatted-date');
					if(date.length!==0) {
						var empty = true;
						var first_name = row.find('.first-name');
						var last_name = row.find('.last-name');
						if(date.val()!='') empty = false;
						if(first_name.val()!='') empty = false;
						if(last_name.val()!='') empty = false;
						if(empty===true) row.remove();
					}
				});
			}	
			var validated = $(this).validationEngine('validate', { scroll: false, showArrowOnRadioAndCheckbox: true });
		}
		if(validated==true){
			$('#popup-refresh').modal('show');
			var formdata =  new FormData(this);
			formdata.append('token', ajax_post_object.tokens.save_item);
			var form = new TSForm(formdata);
			form.submitForm(callback);
		}
    });
    $('.individual-registration').on('submit', function(e){
		e.preventDefault();
		var validated = true;
		if($(this).hasClass('validate')){
			if($(this).hasClass('competition-page')) {
				$('.routine-container .row').each(function() {
					var empty = true;
					var row = $(this);
					var name = row.find('.routine-name');
					var dancers = row.find('.routine-dancers');
					if(name.val()!='') empty = false;
					if(dancers.val()!='') empty = false;
					if(empty===true) row.remove();
				});
			}
			var validated = $(this).validationEngine('validate', { scroll: false, showArrowOnRadioAndCheckbox: true });
		}
		if(validated==true){
			$('#popup-refresh').modal('show');
			var formdata =  new FormData(this);
			formdata.append('token', ajax_post_object.tokens.save_item);
			var form = new TSForm(formdata);
			form.submitForm(callback);
		}
    });
    $('.ts-registrationform-wrapper').on('click', '.btn-saveforlater', function(e){
		e.preventDefault();
		var validated = true;
		var formElement = $('.ts-registrationform-wrapper form')[0];
		if($(formElement).hasClass('validate')){
			if($(formElement).hasClass('competition-page')) {
				$('.routine-container .row').each(function() {
					var empty = true;
					var row = $(this);
					var name = row.find('.routine-name');
					var dancers = row.find('.routine-dancers');
					if(name.val()!='') empty = false;
					if(dancers.val()!='') empty = false;
					if(empty===true) row.remove();
				});
			}
			if($(formElement).hasClass('roster-page')) {
				$('.roster-container .row').each(function() {
					var row = $(this);
					var date = row.find('.formatted-date');
					if(date.length!==0) {
						var empty = true;
						var first_name = row.find('.first-name');
						var last_name = row.find('.last-name');
						if(date.val()!='') empty = false;
						if(first_name.val()!='') empty = false;
						if(last_name.val()!='') empty = false;
						if(empty===true) row.remove();
					}
				});
			}	
			var validated = $(formElement).validationEngine('validate', { scroll: false, showArrowOnRadioAndCheckbox: true });
		}
		if(validated==true){
			$('#popup-refresh').modal('show');
			var next_step = $(this).attr('data-nextstep');
			var formdata =  new FormData(formElement);
			formdata.append('token', ajax_post_object.tokens.save_item);
			formdata.append('save_for_later', 1);
			formdata.append('next_step', next_step);
			var form = new TSForm(formdata);
			form.submitForm(callbackForLater);
    	}
    });
    $('.ts-registrationform-wrapper').on('click', '.btn-submitworkshop', function(e){
		e.preventDefault();
		var validated = true;
		if($('#entry-workshop').hasClass('validate')){
			var validated = $('#entry-workshop').validationEngine('validate', { scroll: false, showArrowOnRadioAndCheckbox: true });
		}
		if(validated==true){
			$('#popup-refresh').modal('show');
			var formElement = document.forms.namedItem('entry-workshop');
			var formdata =  new FormData(formElement);
			formdata.append('token', ajax_post_object.tokens.save_item);
			formdata.append('next_step', $(this).val());
			var form = new TSForm(formdata);
			form.submitForm(callback);
    	}
    });
    $('#popup-waiver-form').on('submit', function(e){
		e.preventDefault();
		if($(this).validationEngine('validate', { scroll: false, showArrowOnRadioAndCheckbox: true })) {
			$('#popup-waiver').modal('hide');
			$('.btn-submitconfirmation').click();
		}
    });	

    $('#add-workshop-participants').on('submit', function(e){
		e.preventDefault();
		$(this).find('input[type="submit"]').val('Adding...');
		var formdata =  new FormData(this);
		formdata.append('token', ajax_post_object.tokens.save_item);
		formdata.append('action', 'add_participants');
		var form = new TSForm(formdata);
		form.submitForm(callbackAddWorkshopParticipants);
    });

    $('#form-save-voucher').on('submit', function(e){
		e.preventDefault();
		if($(this).validationEngine('validate', { scroll: false, showArrowOnRadioAndCheckbox: true })) {
			$(this).find('input[type="submit"]').val('Saving...');
			var formdata =  new FormData(this);
			formdata.append('token', ajax_post_object.tokens.save_item);
			formdata.append('action', 'save_voucher');
			var form = new TSForm(formdata);
			form.submitForm(callbackSaveVoucher);
		}	
    });

    $('#form-save-tour').on('submit', function(e){
		e.preventDefault();
		if($(this).validationEngine('validate', { scroll: false, showArrowOnRadioAndCheckbox: true })) {
			$(this).find('input[type="submit"]').val('Saving...');
			var formdata =  new FormData(this);
			formdata.append('token', ajax_post_object.tokens.save_item);
			formdata.append('action', 'save_tour');
			var form = new TSForm(formdata);
			form.submitForm(callbackSaveTour);
		}	
    });

	$('.ts-admin-wrapper').on('click', '.btn-closetour', function(e) {
		e.preventDefault();
		var btn = $(this);
		btn.parent().siblings('.workshop-status').html('<small><i class="fa fa-spinner fa-pulse fa-fw"></i></small>');
		btn.parent().siblings('.tour-status').html('<small><i class="fa fa-spinner fa-pulse fa-fw"></i></small>');
		var id = btn.attr('data-id');
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'close_tour');
		formdata.append('id', id);
		var form = new TSForm(formdata);
		form.submitForm(callbackCloseTour);
	});	

	$('.ts-admin-wrapper').on('click', '.btn-publish', function(e) {
		e.preventDefault();
		var btn = $(this);
		btn.parent().siblings('.schedule-status').html('<small><i class="fa fa-spinner fa-pulse fa-fw"></i></small>');
		var id = btn.attr('data-id');
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'sched_status');
		formdata.append('id', id);
		var form = new TSForm(formdata);
		form.submitForm(callbackSchedStatus);
	});	

	$('#form-create-invoice').on('submit', function(e){
		e.preventDefault();
		$(this).find('input[type="submit"]').val('Creating...');
		var formdata =  new FormData(this);
		formdata.append('token', ajax_post_object.tokens.save_item);
		formdata.append('action', 'create_invoice');
		var form = new TSForm(formdata);
		form.submitForm(callbackCreateInvoice);
	});

    $('.ts-registrationform-wrapper').on('click', '.btn-saveroster', function(e){
		e.preventDefault();
		$('#popup-refresh').modal('show');
		var id = $(this).attr('data-id');
		var eid = $(this).attr('data-eid');
		var first_name 	= $('#item-'+id+' .rostercurr-first_name').find('input').val();
		var last_name 	= $('#item-'+id+' .rostercurr-last_name').find('input').val();
		var birth_date 	= $('#item-'+id+' .rostercurr-birth_date').find('input').val();
		var roster_type = $('#item-'+id+' .rostercurr-roster_type').find('select').val();
		var type_name 	= $('#item-'+id+' .rostercurr-roster_type').find('select option:selected').text();
		var select 		= $('#item-'+id+' .rostercurr-selected').find('input');
		var selected  	= select.is(':checked') ? 1 : '';
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.save_item);
		formdata.append('action', 'save_roster');
		formdata.append('id', id);
		formdata.append('eid', eid);
		formdata.append('first_name', first_name);
		formdata.append('last_name', last_name);
		formdata.append('birth_date', birth_date);
		formdata.append('roster_type', roster_type);
		formdata.append('type_name', type_name);
		formdata.append('selected', selected);
		var form = new TSForm(formdata);
		form.submitForm(callbackSaveRoster);
    });	
    $('.ts-registrationform-wrapper').on('click', '.btn-addobserver', function(e){
		e.preventDefault();
		$('#popup-refresh').modal('show');
		var list = $('.observers-list .observer').length;
		var last_id = list > 0 ? $('.observers-list').find('.observer:last-child').attr('data-id') : 0;
		var tempid = parseInt(last_id)+1;
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.save_item);
		formdata.append('action', 'add_observer');
		formdata.append('id', tempid);
		formdata.append('eid', eid);
		formdata.append('name', 'Observer');
		var form = new TSForm(formdata);
		form.submitForm(callbackAddObserver);
    });
    $('.ts-registrationform-wrapper').on('click', '.btn-addmunchkinobserver', function(e){
		e.preventDefault();
		$('#popup-refresh').modal('show');
		var list = $('.munchkin-observers-list .munchkin-observer').length;
		var last_id = list > 0 ? $('.munchkin-observers-list').find('.munchkin-observer:last-child').attr('data-id') : 0;
		var tempid = parseInt(last_id)+1;
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.save_item);
		formdata.append('action', 'add_munchkin_observer');
		formdata.append('id', tempid);
		formdata.append('eid', eid);
		formdata.append('name', 'Additional Munchkin Observer');
		var form = new TSForm(formdata);
		form.submitForm(callbackAddMunchkinObserver);
    });
    $('#add-routine-dancers').on('submit', function(e){
		e.preventDefault();
		$(this).find('input[type="submit"]').val('Saving...');
		var formdata =  new FormData(this);
		formdata.append('token', ajax_post_object.tokens.save_item);
		formdata.append('action', 'add_routine_dancers');
		var form = new TSForm(formdata);
		form.submitForm(callbackAddRoutineDancers);
    });
    $('.ts-registrationform-wrapper').on('click', '.btn-applycoupon', function(e){
		e.preventDefault();
		if($('#discount-coupon').val()!='') {
			$('#popup-refresh').modal('show');
			var eid = $(this).attr('data-eid');
			var formdata =  new FormData();
			formdata.append('token', ajax_post_object.tokens.default);
			formdata.append('action', 'apply_coupon');
			formdata.append('eid', eid);
			formdata.append('coupon', $('#discount-coupon').val());
			var form = new TSForm(formdata);
			form.submitForm(callbackApplyCoupon);
		}	
    });
    $('.ts-registrationform-wrapper').on('click', '.btn-removecoupon', function(e){
		e.preventDefault();
		$('#popup-refresh').modal('show');
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.default);
		formdata.append('action', 'remove_coupon');
		formdata.append('eid', eid);
		var form = new TSForm(formdata);
		form.submitForm(callbackRemoveCoupon);
    });
    $('.ts-registrationform-wrapper').on('change', '.adjust-workshop-fee', function(e){
		e.preventDefault();
		if($(this).hasClass('disabled')==false) {
			$('#popup-refresh').modal('show');
			var id = $(this).attr('data-id');
			var eid = $(this).attr('data-eid');
			var formdata =  new FormData();
			formdata.append('token', ajax_post_object.tokens.default);
			formdata.append('action', 'adjust_fee');
			formdata.append('id', id);
			formdata.append('eid', eid);
			formdata.append('age_division', $('#age-division-'+id).val());
			formdata.append('discount', $('#discount-'+id).val());
			formdata.append('duration', $('#duration-'+id).val());
			var form = new TSForm(formdata);
			form.submitForm(callbackAdjustFee);
		}
    });
    $('.ts-registrationform-wrapper').on('change', '.select-tour-city', function(e){
		e.preventDefault();
		var tour_city = $(this).val();
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.default);
		formdata.append('action', 'set_tour_city');
		formdata.append('tour_city', tour_city);
		formdata.append('eid', eid);
		var form = new TSForm(formdata);
		form.submitForm(callbackSelectTourCity);
    });
	$('.ts-registrationform-wrapper').on('click', '.btn-removeparticipant', function(e) {
		e.preventDefault();
		$('#popup-refresh').modal('show');
		var id = $(this).attr('data-id');
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.delete_item);
		formdata.append('action', 'remove_participant');
		formdata.append('id', id);
		formdata.append('eid', eid);
		var form = new TSForm(formdata);
		form.submitForm(callbackRemoveParticipant);
	});	
	$('.ts-registrationform-wrapper').on('click', '.btn-removeobserver', function(e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.delete_item);
		formdata.append('action', 'remove_observer');
		formdata.append('id', id);
		formdata.append('eid', eid);
		var form = new TSForm(formdata);
		form.submitForm(callbackRemoveObserver);
	});	
	$('.ts-registrationform-wrapper').on('click', '.btn-removemunchkinobserver', function(e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.delete_item);
		formdata.append('action', 'remove_munchkin_observer');
		formdata.append('id', id);
		formdata.append('eid', eid);
		var form = new TSForm(formdata);
		form.submitForm(callbackRemoveMunchkinObserver);
	});	
    $('#entries-list').on('click', '.btn-edit-entry', function(e){
		e.preventDefault();
		$(this).html('<small><i class="fa fa-spinner fa-pulse fa-fw"></i></small>');
		var url = $(this).attr('data-url');
		var eid = $(this).attr('data-eid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.default);
		formdata.append('action', 'edit_entry');
		formdata.append('url', url);
		formdata.append('eid', eid);
		var form = new TSForm(formdata);
		form.submitForm(callback);
    });
    $('#tourstops-list').on('click', '.btn-new-registration', function(e){
		e.preventDefault();
		$(this).html('<small><i class="fa fa-spinner fa-pulse fa-fw"></i></small>');
		var id = $(this).attr('data-id');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.default);
		formdata.append('action', 'new_registration');
		formdata.append('id', id);
		var form = new TSForm(formdata);
		form.submitForm(callback);
    });
    $('#new-registration').on('submit', function(e){
		e.preventDefault();
		$(this).find('button').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Processing');
		var formdata =  new FormData(this);
		formdata.append('token', ajax_post_object.tokens.default);
		formdata.append('action', 'new_registration');
		var form = new TSForm(formdata);
		form.submitForm(callback);
    });
	$('.ts-admin-wrapper').on('click', '.btn-delete-routine', function(e) {
		e.preventDefault();
		$('#popup-refresh').modal('show');
		var id = $(this).attr('data-id');
		var eid = $(this).attr('data-eid');
		var token = ajax_post_object.tokens.delete_item;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'delete_routine');
		formdata.append('id', id);
		formdata.append('eid', eid);
		var form = new TSForm(formdata);
		form.submitForm(callbackDeleteRoutine);
	});
	$('.ts-admin-wrapper').on('click', '.btn-delete', function(e) {
		e.preventDefault();
		$('#popup-refresh').modal('show');
		var id = $(this).attr('data-id');
		var type = $(this).attr('data-type');
		var token = ajax_post_object.tokens.delete_item;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'delete_item');
		formdata.append('id', id);
		formdata.append('type', type);
		var form = new TSForm(formdata);
		form.submitForm(callbackDelete);
	});

	$('.ts-admin-wrapper').on('click', '.btn-delete-all', function(e) {
		e.preventDefault();
		var ids = getSelectedIds();
		var type = $(this).attr('data-type');
		var token = ajax_post_object.tokens.delete_item;
		var formdata =  new FormData();
		formdata.append('ids', ids);
		formdata.append('type', type);
		formdata.append('action', 'delete_all');
		formdata.append('token', token);
		var form = new TSForm(formdata);
		form.submitForm(callbackReloadPage);
	});

	$('#invoices-list').on('click', '.btn-pay-invoice', function(e){
		e.preventDefault();
		$(this).html('<small><i class="fa fa-spinner fa-pulse fa-fw"></i></small>');
		var url = $(this).attr('data-url');
		var eid = $(this).attr('data-eid');
		var ivid = $(this).attr('data-ivid');
		var formdata =  new FormData();
		formdata.append('token', ajax_post_object.tokens.default);
		formdata.append('action', 'pay_invoice');
		formdata.append('url', url);
		formdata.append('eid', eid);
		formdata.append('ivid', ivid);
		var form = new TSForm(formdata);
		form.submitForm(callback);
	});

	$('.ts-admin-wrapper').on('click', '.btn-downloadallmusic', function(e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'download_all_music');
		formdata.append('id', id);
		var form = new TSForm(formdata);
		form.submitForm(callbackDownloadAllMusic);
	});

	$('#form-save-music-info').on('submit', function(e){
		e.preventDefault();
		$(this).find('input[type="submit"]').val('Saving...');
		var formdata =  new FormData(this);
		formdata.append('token', ajax_post_object.tokens.save_item);
		formdata.append('action', 'save_music_info');
		var form = new TSForm(formdata);
		form.submitForm(callbackSaveMusicInfo);
	});

	$('.ts-admin-wrapper').on('click', '.btn-markpaid', function(e) {
		e.preventDefault();
		$(this).text('Processing...');
		var id = $(this).attr('data-id');
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'save_mark_as_paid');
		formdata.append('id', id);
		var form = new TSForm(formdata);
		form.submitForm(callbackMarkAsPaid);
  	});  

	$('#results-page').on('click', '.btn-publishresults', function(e) {
		e.preventDefault();
		$(this).prepend('<small><i class="fa fa-spinner fa-pulse fa-fw"></i></small>');
		var id = $(this).attr('data-id');
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'publish_results');
		formdata.append('id', id);
		var form = new TSForm(formdata);
		form.submitForm(callbackResultStatus);
	});

	$('#critiques-page').on('click', '.btn-removeroutinecritique', function(e){
		var button = $(this);
		var id = button.attr('data-id');
		var currroutine = $('#routine-'+id);
		currroutine.find('.routine-critique-container').html('<a href="javascript:void(0);" class="btn-addroutinecritique btn btn-green" data-id="'+id+'"><small>Upload</small></a>');
		removeVideoCritique(id);
	});	

	$('.scholarship-wrapper').on('change', '.scholarship', function(e){
		e.preventDefault();
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'load_participant_info');
		formdata.append('id', $(this).val());
		formdata.append('tempid', $(this).attr('data-id'));
		var form = new TSForm(formdata);
		form.submitForm(callbackChangeScholar);	
	});

	$('#form-special-awards').on('change', '.change-routine-number', function(e){
		e.preventDefault();
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'load_routine_info');
		formdata.append('routine_number', $(this).val());
		formdata.append('tour_id', $(this).attr('data-tourid'));
		formdata.append('row', $(this).closest('.row').attr('id'));
		var form = new TSForm(formdata);
		form.submitForm(callbackChangeRoutine);	
	});

  	$('#form-special-awards').on('submit', function(e){
		e.preventDefault();
		var validated = $(this).validationEngine('validate', { 
			scroll: false, 
			showArrowOnRadioAndCheckbox: true 
		});
		if(validated==true){
			$(this).find('input[type="submit"]').val('Saving...').prop('disabled', true);
			var formdata =  new FormData(this);
			formdata.append('token', ajax_post_object.tokens.save_item);
			formdata.append('action', 'save_special_awards');
			var form = new TSForm(formdata);
			form.submitForm(callbackSaveSpecidalAwards);
		}
	});

  	$('#form-scholarships').on('submit', function(e){
		e.preventDefault();
		var validated = $(this).validationEngine('validate', { 
			scroll: false, 
			showArrowOnRadioAndCheckbox: true 
		});
		if(validated==true){
			$(this).find('input[type="submit"]').val('Saving...').prop('disabled', true);
			var formdata =  new FormData(this);
			formdata.append('token', ajax_post_object.tokens.save_item);
			formdata.append('action', 'save_scholarships');
			var form = new TSForm(formdata);
			form.submitForm(callbackSaveScholarships);
		}
	});

	$('#view-schedule-page').on('click', '.btn-resetschedule', function(e){
		e.preventDefault();
		$(this).prepend('<i class="fa fa-spinner fa-pulse fa-fw"></i>').prop('disabled', true);
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'reset_competition_schedule');
		formdata.append('id', $(this).attr('data-id'));
		formdata.append('return', $(this).attr('data-return'));
		var form = new TSForm(formdata);
		form.submitForm(callback);	
	});

	$('#form-submit-scores').on('click', '.btn-submitscore', function(e){
		e.preventDefault();
		$(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Submitting..').prop('disabled', true);
		var id = $(this).attr('data-id');
		var judge1 = $('#routine-'+id+' .score-judge1').val();
		var judge2 = $('#routine-'+id+' .score-judge2').val();
		var judge3 = $('#routine-'+id+' .score-judge3').val();
		var token = ajax_post_object.tokens.default;
		var formdata =  new FormData();
		formdata.append('token', token);
		formdata.append('action', 'save_routine_scores');
		formdata.append('id', id);
		formdata.append('judge1', judge1);
		formdata.append('judge2', judge2);
		formdata.append('judge3', judge3);
		var form = new TSForm(formdata);
		form.submitForm(callbackSaveRoutineScores);
	});

});

function addVideoCritique(attachment_id, routine_id) {
	var token = ajax_post_object.tokens.default;
	var formdata =  new FormData();
	formdata.append('token', token);
	formdata.append('action', 'add_critique');
	formdata.append('attachment_id', attachment_id);
	formdata.append('routine_id', routine_id);
	var form = new TSForm(formdata);
	form.submitForm(callback);	
}

function addVideoCritiques(attachments, tour_id) {
	var token = ajax_post_object.tokens.default;
	var formdata =  new FormData();
	formdata.append('token', token);
	formdata.append('action', 'add_critiques');
	formdata.append('attachments', attachments);
	formdata.append('tour_id', tour_id);
	var form = new TSForm(formdata);
	form.submitForm(callbackSaveAndReload);	
}

function removeVideoCritique(post_id) {
	var token = ajax_post_object.tokens.default;
	var formdata =  new FormData();
	formdata.append('token', token);
	formdata.append('action', 'remove_critique');
	formdata.append('post_id', post_id);
	var form = new TSForm(formdata);
	form.submitForm(callback);	
}

function TSForm(formdata) {
	this.formdata = formdata;
}

TSForm.prototype.submitForm = function(callback, callback2) {
	jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajax_post_object.ajaxurl,
        data: this.formdata,
		contentType: false,
		processData: false,			
        success: function(data){
        	if(callback)
				callback(data);
        	if(callback2)
				callback2(data);
        }
    });
}

TSForm.prototype.getSelectValues = function(select) {
	var result = [];
	var options = select && select.options;
	var opt;
	for (var i=0, iLen=options.length; i<iLen; i++) {
		opt = options[i];
		if (opt.selected) {
			result.push(opt.value || opt.text);
		}
	}
	return result;
}