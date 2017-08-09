jQuery(document).ready(function($) {

	init_dataTable();
	//init_datePicker();
	//init_dateTimePicker();

	$('.formatted-date').mask('99/99/9999',{placeholder:'MM/DD/YYYY'});
	$('.formatted-phone').mask('(999) 999-9999');

	$('.btn-select-all').on('click', function(e){
		e.preventDefault();
		$('.select-item').prop('checked', true);
	});

	$('.btn-unselect-all').on('click', function(e){
		e.preventDefault();
		$('.select-item').prop('checked', false);
	});

	$('.btn-remove').live('click', function(e){
		e.preventDefault();
		var row = $(this).closest('.row');
		/*var siblings = row.siblings();
		if(siblings.length==0){
			location.reload();
		}
		else{
			row.remove();
		}*/
		row.remove();
	});

	$('.btn-reset').on('click', function(e){
		var form = $(this).closest('form');
		form[0].reset();
	});

 	$('.check-all').on('change',function(e){
 		if($(this).is(':checked') == true){
 			$('.select-item').prop('checked', true);
 		}else{
 			$('.select-item').prop('checked', false);
 		}
 	});

	$('.ts-registrationform-wrapper').on('focusout', 'input.formatted-date', function(e) {
		var field = $(this);
        var date = field.val();
        var validate = validateDate(date);
        var parent = field.parent();
        if(validate && (date!='' || date!='MM/DD/YYYY')) {
        	parent.find('.formError').hide();
        }
        else{
        	if(parent.find('.formError').length==0)
				parent.prepend('<div class="formError" style="opacity: 0.87; position: absolute; top: 0px; left: -13.875px; margin-top: -68px;"><div class="formErrorContent">* Invalid date or format, must be in MM/DD/YYYY format</div><div class="formErrorArrow"><div class="line10"><!-- --></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div><div class="line2"><!-- --></div><div class="line1"><!-- --></div></div></div>');
        }
    });    

	$('#individual-profile').on('click', '.btn-addsibling', function(e) {
		e.preventDefault();
		
		var list = $('.siblings-container .sibling-info').length;
		var last_id = list > 0 ? $('.siblings-container .sibling-info:last-child').attr('data-id') : 0;
		var tempid = parseInt(last_id)+1;
		var clone = $('.sibling-base').clone();
		clone.attr('data-id', tempid);
		clone.addClass('sibling-info');
		clone.removeClass('sibling-base hidden');
		clone.find('.dancer-name').attr('name', 'newsiblings['+tempid+'][name]').val('');
		clone.find('.dancer-birthdate').attr('name', 'newsiblings['+tempid+'][birth_date]').val('')
			 .mask('99/99/9999',{placeholder:'MM/DD/YYYY'});
			 //.datepicker({dateFormat:'mm/dd/yy', changeMonth:true, changeYear:true, yearRange:'-100:+0', maxDate: '-5Y'});
		clone.find('.dancer-parent').attr('name', 'newsiblings['+tempid+'][parent]').val('');
		clone.find('.dancer-studio').attr('name', 'newsiblings['+tempid+'][studio]').val('');
		clone.find('.dancer-address').attr('name', 'newsiblings['+tempid+'][address]').val('');
		clone.find('.dancer-city').attr('name', 'newsiblings['+tempid+'][city]').val('');
		clone.find('.dancer-state').attr('name', 'newsiblings['+tempid+'][state]').val('');
		clone.find('.dancer-zipcode').attr('name', 'newsiblings['+tempid+'][zipcode]').val('');
		clone.find('.dancer-country').attr('name', 'newsiblings['+tempid+'][country]').val('');
		clone.find('.dancer-cell').attr('name', 'newsiblings['+tempid+'][cell]').val('')
			.mask('(999) 999-9999');
		clone.find('.dancer-email').attr('name', 'newsiblings['+tempid+'][email]').val('');
		clone.appendTo('.siblings-container');
	});		

	$('#studio-roster').on('click', 'a.btn-adddancer', function(e) {
		e.preventDefault();
		
		var last = $('.roster-container .row:last-child');
		var last_id = last.attr('data-id');
		var tempid = parseInt(last_id)+1;
		var clone = $('.rosternew-base').clone();
		clone.attr('data-id', tempid);
		clone.attr('id', 'item-'+tempid);
		clone.removeClass('rosternew-base');
		clone.find('.rosternew-first_name').attr('name', 'rosternew['+tempid+'][first_name]').removeClass('rosternew-first_name');
		clone.find('.rosternew-last_name').attr('name', 'rosternew['+tempid+'][last_name]').removeClass('rosternew-last_name');
		clone.find('.rosternew-birth_date').attr('name', 'rosternew['+tempid+'][birth_date]').removeClass('rosternew-birth_date')
			 .mask('99/99/9999',{placeholder:'MM/DD/YYYY'});
			 //.datepicker({dateFormat:'mm/dd/yy', changeMonth:true, changeYear:true, yearRange:'-100:+0', maxDate: '-5Y'});
		clone.find('.rosternew-roster_type').attr('name', 'rosternew['+tempid+'][roster_type]').removeClass('rosternew-roster_type');
		clone.find('.rosternew-selected').attr('name', 'rosternew['+tempid+'][selected]').removeClass('rosternew-selected');
		clone.appendTo('.roster-container');
	});		

	$('#studio-roster').on('click', 'a.btn-adddancerrow', function(e) {
		e.preventDefault();
		
		var last = $('.roster-container .row:last-child');
		var last_id = last.attr('data-id');
		var tempid = parseInt(last_id)+1;
		var clone = $('.rosternew-base').clone();
		clone.attr('data-id', tempid);
		clone.attr('id', 'item-'+tempid);
		clone.removeClass('rosternew-base');
		clone.find('.rosternew-first_name').attr('name', 'rosternew['+tempid+'][first_name]').removeClass('rosternew-first_name');
		clone.find('.rosternew-last_name').attr('name', 'rosternew['+tempid+'][last_name]').removeClass('rosternew-last_name');
		clone.find('.rosternew-birth_date').attr('name', 'rosternew['+tempid+'][birth_date]').removeClass('rosternew-birth_date')
			 .mask('99/99/9999',{placeholder:'MM/DD/YYYY'});
			 //.datepicker({dateFormat:'mm/dd/yy', changeMonth:true, changeYear:true, yearRange:'-100:+0', maxDate: '-5Y'});
		clone.find('.rosternew-roster_type').attr('name', 'rosternew['+tempid+'][roster_type]').removeClass('rosternew-roster_type');
		clone.find('.rosternew-selected').attr('name', 'rosternew['+tempid+'][selected]').removeClass('rosternew-selected');
		clone.appendTo('.roster-container');
	});		

	$('#studio-roster').on('click', 'a.btn-editroster', function(e) {
		e.preventDefault();
		e.stopPropagation();
		var btn = $(this);
		var id = btn.attr('data-id');
		var row = $('#item-'+id);

		btn.removeClass('btn-blue btn-editroster').addClass('btn-green btn-saveroster').text('Save');

		var firstname = row.find('.rostercurr-first_name');
		var lastname = row.find('.rostercurr-last_name');
		var firstname_val = firstname.text();
		var lastname_val = lastname.text();
		firstname.html('<input type="text" name="rosteredit['+id+'][first_name]" value="'+firstname_val+'" class="validate[required]" />');
		lastname.html('<input type="text" name="rosteredit['+id+'][last_name]" value="'+lastname_val+'" class="validate[required]" />');

		var birthdate = row.find('.rostercurr-birth_date');
		var birthdate_val = birthdate.text();
		var input_birthdate = document.createElement('input');
		input_birthdate.type = 'text';
		input_birthdate.value = birthdate_val;
		input_birthdate.name = 'rosteredit['+id+'][birth_date]';
		input_birthdate.className  = 'validate[required] formatted-date';
		$(input_birthdate).mask('99/99/9999',{placeholder:'MM/DD/YYYY'});
		//$(input_birthdate).datepicker({dateFormat:'mm/dd/yy', changeMonth:true, changeYear:true, yearRange:'-100:+0', maxDate: '-5Y'})
		birthdate.html(input_birthdate);

		var rostertype = row.find('.rostercurr-roster_type');
		var rostertype_id = rostertype.attr('data-id');
		var rostertype_select = $('.rosternew-roster_type').clone();
		rostertype_select.removeClass('rosternew-roster_type')
						.attr('name', 'rosteredit['+id+'][roster_type]')
						.find('option[value="'+rostertype_id+'"]').attr('selected',true);
		rostertype.html(rostertype_select);

		var select = row.find('.select-item');
		select.attr({'name':'rosteredit['+id+'][selected]', 'value':1});
	});		

	$('.ts-registrationform-wrapper').on('click', 'a.btn-addroutine', function(e) {
		e.preventDefault();
		
		$('#add-routine-dancers')[0].reset();

		var last = $('.routine-container .row:last-child');
		var last_id = last.attr('data-id');
		var tempid = parseInt(last_id)+1;
		var clone = last.clone();

		clone.attr('id', 'item-'+tempid);
		clone.attr('data-id', tempid); 
		clone.find('.btn-addroutinedancers').attr('data-id', tempid);
		clone.find('.btn-addroutinemusics').attr('data-id', tempid);
		clone.find('.btn-delete').addClass('remove-routine');
		clone.find('.routine-name').attr({'name':'routinenew['+tempid+'][name]', 'value': '', 'id': 'routine-name-'+tempid});
		clone.find('.routine-dancers-preview').text('').attr('id','routine-dancers-preview-'+tempid);
		clone.find('.routine-dancers').val('').attr('id','routine-dancers-'+tempid).attr('name', 'routinenew['+tempid+'][dancers]');
		clone.find('.routine-agediv-preview').text('').attr('id','routine-agediv-preview-'+tempid);
		clone.find('.routine-agediv').val('').attr('id','routine-agediv-'+tempid).attr('name', 'routinenew['+tempid+'][agediv]');
		clone.find('.routine-cat-preview').text('').attr('id','routine-cat-preview-'+tempid);
		clone.find('.routine-cat').val('').attr('id','routine-cat-'+tempid).attr('name', 'routinenew['+tempid+'][cat]');
		clone.find('.routine-genre').val('').attr('name', 'routinenew['+tempid+'][genre]');
		clone.find('.routine-flows').val('').attr('name', 'routinenew['+tempid+'][flows]');
		clone.find('.routine-props').val(2).attr('name', 'routinenew['+tempid+'][props]');
		clone.find('.routine-fee-preview').text('0.00').attr('id','routine-fee-preview-'+tempid);
		clone.find('.routine-fee').val('').attr('id','routine-fee-'+tempid).attr('name', 'routinenew['+tempid+'][fee]');
		clone.find('.routine-music-container').html('<input class="routine-music" id="routine-music-'+tempid+'" name="routinenew['+tempid+'][music]" value="" type="hidden"><a href="javascript:void(0);" class="btn-addroutinemusic btn btn-green" data-id="'+tempid+'"><small>Add</small></a>');
		clone.find('.btn-delete-routine').removeClass('btn-delete-routine').addClass('btn-remove').attr('data-id', tempid);
		clone.find('.formError').remove();
		clone.appendTo('.routine-container');
	});		

	$('.ts-registrationform-wrapper').on('click', '.btn-remove', function(e) {
		e.preventDefault();
		var button = $(this);
		button.closest('.row').remove();
	});	

	$('.ts-registrationform-wrapper').on('click', '.btn-removesibling', function(e) {
		e.preventDefault();
		var button = $(this);
		button.closest('.sibling-info').remove();
	});	

	$('.ts-registrationform-wrapper').on('click', '.btn-addroutinedancers', function(e) {
		e.preventDefault();
		$('#add-routine-dancers input[type="checkbox"]').prop('checked', false);
		var id = $(this).attr('data-id');
		var dancers = $('#routine-dancers-'+id).val();

		if(dancers!='') {
			var dancer_ids = dancers.split(',');
			$.each(dancer_ids, function(index, value){
				$('#add-routine-dancers').find('input[value="'+value+'"]').attr('checked', true);
			});
		}
		var name = $('#routine-name-'+id).val();
		$('#add-dancers #routine-id').val(id);
		$('#add-dancers #routine-name').val(name);
		$('#add-dancers').modal('show');
	});

	$('.btn-addfromroster').on('click', function(e) {
		e.preventDefault();
		$('#add-fromroster').modal('show');
	});
	
	$('.ts-registrationform-wrapper').on('click', '.btn-addroutinemusic', function(e){
		e.preventDefault();

		var button = $(this);
		var id = button.attr('data-id');
		var custom_uploader;

		if(custom_uploader) {
            custom_uploader.open();
            return;
        }		

		custom_uploader = wp.media.frames.file_frame = wp.media({
			title    : 'Add Routine Music',
			button   : {
				text     : 'Use as Routine Music'
			},
			multiple : false
		});

		custom_uploader.on('select', function(){
			attachment = custom_uploader.state().get('selection').first().toJSON();
			if ( attachment.url != '' ) {
				var currroutine = $('#routine-music-'+id);
				currroutine.val(attachment.id);
				$('<div><small>'+attachment.filename+'</small></div><a href="javascript:void(0);" class="btn-removeroutinemusic btn btn-red" data-id="'+id+'"><small>Remove</small></a>').insertAfter(currroutine);
				button.remove();
			}
		});

		custom_uploader.open();

		$('.media-modal-content .media-menu-item:first-child').click();
	});	

	$('.ts-registrationform-wrapper').on('click', '.btn-removeroutinemusic', function(e){
		var button = $(this);
		var id = button.attr('data-id');
		var currroutine = $('#routine-music-'+id);
		currroutine.val('');
		button.remove();
		currroutine.next('div').remove();
		$('<a href="javascript:void(0);" class="btn-addroutinemusic btn btn-green" data-id="'+id+'"><small>Add</small></a>').insertAfter(currroutine);
	});	

	$('.ts-registrationform-wrapper').on('click', '.btn-popupwaiver', function(e){
		e.preventDefault();
		$('#popup-waiver').modal('show');
	});	

	$('.ts-registrationform-wrapper').on('click', '.btn-pagenumber', function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		var title = $(this).attr('data-title');
		var next_step = id;
		var curr_title = $('.steps-btn-container .current-step').find('.btn-pagenumber').attr('data-title');
		if($('.btn-submitworkshop').length > 0) {
			$('.registration-form .btn-blue.btn-submitworkshop').val(next_step).click();
		}
		else{
			if(title!='Completed' && title!='Payment') {
				$('.registration-form input[name="next_step"]').val(next_step);
				$('.registration-form .btn[type="submit"]').click();
			}
			if(title=='Payment' && curr_title=='Confirmation') {
				$('#popup-waiver').modal('show');
			}
			else if(title=='Payment' && curr_title!='Confirmation') {
				$('.registration-form input[name="next_step"]').val(next_step);
				$('.registration-form .btn[type="submit"]').click();
			}
		}
	});	

});

function callback(data) {
	if(data.success==true) {
		jQuery('#popup-refresh').modal('hide');
		if(data.redirect)
			window.location.href = data.redirect;
	}
}

function callbackForLater(data) {
	if(data.success==true) {
		jQuery('#popup-refresh .modal-dialog').css({'width' :'auto'});
		jQuery('#popup-refresh .modal-content').html('<span class="message success">Your registration has been saved!</span>');
		setTimeout(function(){
			jQuery('#popup-refresh').modal('hide');
			if(data.redirect)
				window.location.href = data.redirect;
	    }, 2000);
	}
}

function callbackSaveRoster(data) {
	if(data.success==true) {
		var id 				= data.id;
		var first_name 		= data.first_name;
		var last_name 		= data.last_name;
		var birth_date 		= data.birth_date;
		var roster_type 	= data.roster_type;
		var type_name 		= data.type_name;

		jQuery('#item-'+id+' .rostercurr-first_name').text(first_name);
		jQuery('#item-'+id+' .rostercurr-last_name').text(last_name);
		jQuery('#item-'+id+' .rostercurr-birth_date').text(birth_date);
		jQuery('#item-'+id+' .rostercurr-roster_type').text(type_name).attr('data-id', roster_type);
		jQuery('#item-'+id+' .rostercurr-selected').find('input').attr({'name':'rostercurr[]', 'value':id});

		var btn = jQuery('#item-'+id).find('.btn-saveroster');
		btn.removeClass('btn-green btn-saveroster').addClass('btn-blue btn-editroster').text('Edit');

		jQuery('#popup-refresh').modal('hide');
	}
}

function callbackAdjustFee(data) {
	if(data.success==true) {
		var id 					= data.id;
		var onedaydisabled 		= data.onedaydisabled; 
		var fee_new 			= parseFloat(data.new_value);
		var fee_new_preview 	= data.new_value_preview;
		var total_new 			= parseFloat(data.new_total);
		var total_new_preview 	= data.new_total_preview;

		jQuery('#fee-'+id).val(fee_new);
		jQuery('#fee-preview-'+id).text(fee_new_preview);
		jQuery('#total-fee').val(total_new);
		jQuery('#total-fee-preview').text(total_new_preview);

		if(onedaydisabled==true) {
			jQuery('#duration-'+id).find('option:nth-child(1)').attr('selected',true);
			jQuery('#duration-'+id).find('option:nth-child(2)').attr('disabled',true);
		}
		jQuery('#popup-refresh').modal('hide');
	}
}

function callbackAddWorkshopParticipants(data) {
	if(data.success==true) {

		var newparticipants = data.newparticipants;

		jQuery.each(newparticipants, function(index, value){
			var id = index;
			var name = value.name;
			var age_division = value.age_division;
			var discount = value.discount;
			var duration = value.duration;
			var fee = parseFloat(value.fee);
			var last = jQuery('.participants-list .row:last-child');
			var clone = last.clone();
			
			clone.attr('data-id', id);
			clone.attr('id', 'item-'+id);
			clone.find('.participant-name').val(name);
			clone.find('.participant-name-preview').text(name);
			clone.find('.age-div-'+age_division).attr('selected', true);
			clone.find('#fee-'+id).val(fee);
			clone.find('#fee-preview-'+id).text(fee.formatMoney(2));
			clone.insertAfter(last);

			jQuery('#add-workshop-participants').find('#participant-'+id).attr({'disabled':true, 'checked':true});
		});

		jQuery('#popup-refresh').modal('hide');
		location.reload();
	}	
}

function callbackAddObserver(data) {
	if(data.success==true) {
		var eid 		 = data.eid;
		var observer 	 = data.newpobserver;
		var id 			 = observer.id;
		var name 		 = observer.name;
		var age_division = observer.age_division;
		var discount 	 = observer.discount;
		var duration 	 = observer.duration;
		var fee 		 = parseFloat(observer.fee);

		var total = parseFloat(jQuery('#total-fee').val());
		var clone = jQuery('.workshop-observer-base').clone();

		clone.removeClass('workshop-observer-base hidden');
		clone.addClass('observer');
		clone.attr('id', 'observer-'+id);
		clone.attr('data-id', id);
		clone.find('.observer-name').attr('name', 'workshop[observers]['+id+'][name]').val(name);
		clone.find('.observer-name-preview').text(name);
		clone.find('.observer-fee').attr('name', 'workshop[observers]['+id+'][fee]').val(fee);
		clone.find('.observer-fee-preview').text(fee.formatMoney(2));
		clone.find('.btn-removeobserver').attr({'data-id':id, 'data-eid':eid});
		clone.appendTo('.observers-list');

		total = total+fee;

		jQuery('#total-fee').val(total);
		jQuery('#total-fee-preview').text(total.formatMoney(2));

		jQuery('#popup-refresh').modal('hide');
	}	
}

function callbackAddMunchkinObserver(data) {
	if(data.success==true) {
		var eid 		 = data.eid;
		var observer 	 = data.newpobserver;
		var id 			 = observer.id;
		var name 		 = observer.name;
		var age_division = observer.age_division;
		var discount 	 = observer.discount;
		var duration 	 = observer.duration;
		var fee 		 = parseFloat(observer.fee);

		var total = parseFloat(jQuery('#total-fee').val());
		var clone = jQuery('.workshop-observer-base').clone();

		clone.removeClass('workshop-observer-base hidden');
		clone.addClass('munchkin-observer');
		clone.attr('id', 'munchkin-observer-'+id);
		clone.attr('data-id', id);
		clone.find('.observer-name').attr('name', 'workshop[munchkin_observers]['+id+'][name]').val(name);
		clone.find('.observer-name-preview').text(name);
		clone.find('.observer-fee').attr('name', 'workshop[munchkin_observers]['+id+'][fee]').val(fee);
		clone.find('.observer-fee-preview').text(fee.formatMoney(2));
		clone.find('.btn-removeobserver').removeClass('btn-removeobserver').addClass('btn-removemunchkinobserver').attr({'data-id':id, 'data-eid':eid});
		clone.appendTo('.munchkin-observers-list');

		total = total+fee;

		jQuery('#total-fee').val(total);
		jQuery('#total-fee-preview').text(total.formatMoney(2));

		jQuery('#popup-refresh').modal('hide');
	}	
}

function callbackAddRoutineDancers(data) {
	if(data.success==true) {
		var id = data.id;
		var routine_id = data.routine_id;
		var dancer_names = data.dancer_names;
		var dancer_ids = data.dancer_ids;
		var age_div_name = data.age_div_name;
		var routine_cat_id = data.routine_cat_id;
		var routine_cat_name = data.routine_cat_name;
		var count = data.count;
		var fee = data.fee;
		var fee_preview = data.fee_preview;
		var total = data.total_fee;
		var total_preview = data.total_fee_preview;

		jQuery('#routine-dancers-preview-'+id).text(dancer_names);
		jQuery('#routine-agediv-preview-'+id).text(age_div_name);
		jQuery('#routine-cat-preview-'+id).text(routine_cat_name);
		jQuery('#routine-fee-preview-'+id).text(fee_preview);

		jQuery('#routine-dancers-'+id).val(dancer_ids);
		jQuery('#routine-agediv-'+id).val(age_div_name);
		jQuery('#routine-cat-'+id).val(routine_cat_id);
		jQuery('#routine-fee-'+id).val(fee);	

		jQuery('#total-fee').val(total);
		jQuery('#total-fee-preview').text(total_preview);

		jQuery('#add-routine-dancers')[0].reset();
		jQuery('#add-dancers').modal('hide');

		var row = jQuery('.routine-container #item-'+id);

		row.attr('id', 'item-'+routine_id);
		row.attr('data-id', routine_id); 
		row.find('.btn-addroutinedancers').attr('data-id', routine_id);
		row.find('.btn-addroutinemusics').attr('data-id', routine_id);
		row.find('.routine-name').attr({'name':'routinecurr['+routine_id+'][name]', 'id': 'routine-name-'+routine_id});
		row.find('.routine-dancers-preview').attr('id','routine-dancers-preview-'+routine_id);
		row.find('.routine-dancers').attr('id','routine-dancers-'+routine_id).attr('name', 'routinecurr['+routine_id+'][dancers]');
		row.find('.routine-agediv-preview').attr('id','routine-agediv-preview-'+routine_id);
		row.find('.routine-agediv').attr('id','routine-agediv-'+routine_id).attr('name', 'routinecurr['+routine_id+'][agediv]');
		row.find('.routine-cat-preview').attr('id','routine-cat-preview-'+routine_id);
		row.find('.routine-cat').attr('id','routine-cat-'+routine_id).attr('name', 'routinecurr['+routine_id+'][cat]');
		row.find('.routine-genre').attr('name', 'routinecurr['+routine_id+'][genre]');
		row.find('.routine-flows').attr('name', 'routinecurr['+routine_id+'][flows]');
		row.find('.routine-props').attr('name', 'routinecurr['+routine_id+'][props]');
		row.find('.routine-fee-preview').attr('id','routine-fee-preview-'+routine_id);
		row.find('.routine-fee').attr('id','routine-fee-'+routine_id).attr('name', 'routinecurr['+routine_id+'][fee]');
		row.find('.routine-music').attr('id','routine-music-'+routine_id).attr('name', 'routinecurr['+routine_id+'][music]');
		row.find('.btn-addroutinemusic').attr('data-id', routine_id);
		row.find('.btn-remove').removeClass('btn-remove').addClass('btn-delete-routine').attr('data-id', routine_id);
		row.find('.parentFormstudio-competition.formError').remove();
	}	
	jQuery('#add-routine-dancers').find('input[type="submit"]').val('Add');
}

function callbackApplyCoupon(data) {
	if(data.success==true) {
		var new_grand_total = data.new_grand_total;
		var button_html = data.button_html;
		jQuery('.coupon-container').html(button_html);
		jQuery('#grand-total').text(new_grand_total.formatMoney(2));
	}
	else{
		jQuery('.coupon-container').prepend('<span style="color:red;">Invalid Discount Code </span>');
	}
	jQuery('#popup-refresh').modal('hide');
}

function callbackRemoveCoupon(data) {
	if(data.success==true) {
		var new_grand_total = data.new_grand_total;
		var button_html = data.button_html;
		jQuery('.coupon-container').html(button_html);
		jQuery('#grand-total').text(new_grand_total.formatMoney(2));
		jQuery('#popup-refresh').modal('hide');
	}	
}

function callbackRemoveParticipant(data) {
	if(data.success==true) {
		var id = data.id;
		var fee = parseFloat(jQuery('#fee-'+id).val());
		var total = parseFloat(jQuery('#total-fee').val());
		var newtotal = total-fee;
		jQuery('#total-fee').val(newtotal);
		jQuery('#total-fee-preview').text(newtotal.formatMoney(2));
		jQuery('#item-'+id).remove();
		jQuery('#add-workshop-participants').find('#participant-'+id).attr({'disabled':false, 'checked':false});
	}
	jQuery('#popup-refresh').modal('hide');	
	location.reload();
}

function callbackRemoveObserver(data) {
	if(data.success==true) {
		var id = data.id;
		var newtotal = data.new_total;
		location.reload();
	}	
}

function callbackRemoveMunchkinObserver(data) {
	if(data.success==true) {
		var id = data.id;
		var newtotal = data.new_total;
		location.reload();
	}	
}

function callbackDeleteRoutine(data) {
	if(data.success==true){
		var new_total = data.new_total_fee;
		var new_total_preview = data.new_total_fee_preview;
		var this_item = jQuery('#item-'+data.id);
		this_item.fadeOut('fast', 
			function(){ 
				this_item.remove();                    
		});
		jQuery('#total-fee').val(new_total);
		jQuery('#total-fee-preview').text(new_total_preview);
		if(this_item.siblings().length==0)
			location.reload();
	}
	jQuery('#popup-refresh').modal('hide');				
}

function callbackDelete(data) {
	if(data.success==true){
		var this_item = jQuery('#item-'+data.id);
		this_item.fadeOut('fast', 
			function(){ 
				this_item.remove();                    
		});
		if(this_item.siblings().length==0)
			location.reload();
	}
	jQuery('#popup-refresh').modal('hide');		
}

function callbackSelectTourCity(data) {
	if(data.success==true) {
		location.reload();
	}	
}

var init_dataTable = function() {
 	if(jQuery('.ts-data-table').length > 0) {
		jQuery('.ts-data-table').each(function() {
	 		var orderby = jQuery(this).attr('data-orderby') !=null ? jQuery(this).attr('data-orderby') : 0;
	 		var sort 	= jQuery(this).attr('data-sort') !=null ? jQuery(this).attr('data-sort').toString() : 'desc';
	 		var length 	= jQuery(this).attr('data-length') !=null ? jQuery(this).attr('data-length') : 5;

			jQuery(this).DataTable({
				'bLengthChange' : true,
				'bFilter' : false, 
				'bInfo' : true,
				'iDisplayLength' : parseInt(length),
				'aLengthMenu' : [[10, 25, 50, -1], [10, 25, 50, 'All']],
				'order' : [[orderby, sort]],
				'dom' : 'rt<"table-footer clearfix"pl>',
				'language': {
		            'lengthMenu': '_MENU_ Records per page',
		        	}				
			});
		});
	}
}

/*var init_datePicker = function() {
	if(jQuery('.ts-date-picker').length > 0) {
		jQuery('.ts-date-picker').each(function() {
			var maxdate = jQuery(this).attr('data-maxdate') !=null ? jQuery(this).attr('data-maxdate') : new Date();
	 		var dateformat = jQuery(this).attr('data-dateformat') !=null ? jQuery(this).attr('data-dateformat') : 'mm/dd/yy';
			jQuery(this).datepicker({
		        dateFormat : dateformat,
		        maxDate: maxdate,
		        changeMonth: true,
    			changeYear: true,
    			showButtonPanel: true,
    			yearRange: "-100:+0",
		    });
		});
	}
}

var init_dateTimePicker = function() {
	if(jQuery('.ts-datetime-picker').length > 0) {
		jQuery('.ts-datetime-picker').each(function() {
	 		var timeformat = jQuery(this).attr('data-timeformat') !=null ? jQuery(this).attr('data-timeformat') : 'hh:mm tt z';
			jQuery(this).datetimepicker({
		        timeFormat : timeformat
		    });
		});
	}
}*/

var getParameterByName = function(name) {
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.location.href );
	if( results == null )
		return "";
	else
		return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getSelectedIds(container) {
	var ids = new Array(); 
	var cont = container!=null ? '#'+container : 'body';
	var selected = jQuery(cont+' .select-item:checked');
	if(selected.length > 0){
		selected.each(function(){
			ids.push(jQuery(this).val());
		});	
	}	
	return ids;	
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
		j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function dateFormat(el){
    value = el.value;       
    el.value = value.replace(/^([\d]{4})([\d]{2})([\d]{2})$/,"$1/$2/$3");        
}

function validateDate(dateValue) {

    var selectedDate = dateValue;
    if(selectedDate == '')
        return false;
    
    var regExp = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
    var dateArray = selectedDate.match(regExp);
    
    if (dateArray == null){
        return false;
	}
 
    month = dateArray[1];
    day= dateArray[3];
    year = dateArray[5];        
    
    if (month < 1 || month > 12){
       return false;
    }
    else if (day < 1 || day> 31){ 
       return false;
    }
    else if ((month==4 || month==6 || month==9 || month==11) && day ==31){
       return false;
    }
    else if (month == 2){
        var isLeapYear = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
    	if (day> 29 || (day ==29 && !isLeapYear)){
           return false;
	 	}
    }
    return true;
}