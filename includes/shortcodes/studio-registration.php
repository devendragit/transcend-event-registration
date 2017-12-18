<?php
function ts_studio_registration_html($entry_data, $entry_id, $eid, $user_id, $base_url) {

	$steps = ts_get_steps_studio();
	
	$curr_step = isset($_GET['step']) ? $_GET['step'] : 1;
	$curr_index = $curr_step-1;
	$prev_step = $curr_index>0 ? $curr_index : 1;
	$next_step = $curr_step+1;
	?>
	<div class="ts-registrationform-wrapper ts-admin-wrapper">
		<div class="header clearfix">
			<?php ts_display_tabs_html($steps, $curr_step, $base_url); ?>
		</div>
		<div class="content clearfix">
			<?php 
			if($curr_step==1) {
				ts_get_studio_profile_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==2) {
				ts_get_studio_roster_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==3) { 
				ts_get_workshop_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==4) { 
				ts_get_competition_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==5) { 
				ts_get_confirmation_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==6) { 
				ts_get_payment_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==7) { 
				ts_get_results_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			?>
		</div>
	</div>
	<?php
}

function ts_get_studio_profile_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$session_profile = ts_check_value($entry_data, 'profile');

	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$studio_email = $current_user->user_email;

	$profile = array(
		'studio_name' => get_field('studio', 'user_'. $user_id), 
		'studio_director' => get_field('director', 'user_'. $user_id), 
		'studio_address' => get_field('address', 'user_'. $user_id), 
		'studio_city' => get_field('city', 'user_'. $user_id), 
		'studio_state' => get_field('state', 'user_'. $user_id), 
		'studio_zipcode' => get_field('zipcode', 'user_'. $user_id), 
		'studio_country' => get_field('country', 'user_'. $user_id), 
		'studio_phone' => get_field('phone', 'user_'. $user_id), 
		'studio_email' => $studio_email, 
		'studio_cell' => get_field('cell', 'user_'. $user_id), 
		'studio_contact' => get_field('contact', 'user_'. $user_id), 
	);
	?>
	<div class="studio-profile-container">
		<form name="studio-profile" id="studio-profile" class="studio-registration registration-form validate" method="post" action="">
			<?php if( $entry_id ) { ?>
				<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
			<?php } ?>
			<input type="hidden" name="eid" value="<?php echo $eid; ?>">
			<input type="hidden" name="tab" value="profile">
			<input type="hidden" name="action" value="studio_registration">
			<input type="hidden" name="next_step" value="<?php echo $next_step; ?>">
			<div class="table-container">
				<div class="row table-head">
					<div class="col-md-12"><strong>Profile</strong></div>
				</div>
				<div class="table-body">
					<div class="row">
						<div class="col-md-4"> 
							<strong>Studio Name:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_name]" value="<?php echo $profile['studio_name']; ?>" class="validate[required]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Director's Name:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_director]" value="<?php echo $profile['studio_director']; ?>" class="validate[required]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Address:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_address]" value="<?php echo $profile['studio_address']; ?>" class="validate[required]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>City:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_city]" value="<?php echo $profile['studio_city']; ?>" class="validate[required]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>State:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_state]" value="<?php echo $profile['studio_state']; ?>" class="validate[required]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Zip Code:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_zipcode]" value="<?php echo $profile['studio_zipcode']; ?>" class="validate[required,custom[zip]]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Country:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_country]" value="<?php echo $profile['studio_country']; ?>" class="validate[required]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Studio Phone Number:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_phone]" value="<?php echo $profile['studio_phone']; ?>" class="formatted-phone validate[custom[phone]]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Email:</strong><sup class="required">*</sup>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_email]" value="<?php echo $profile['studio_email']; ?>" class="validate[required, custom[email]]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Cell:</strong>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_cell]" value="<?php echo $profile['studio_cell']; ?>" class="formatted-phone validate[custom[phone]]" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<strong>Studio Contact Name:</strong>
						</div>
						<div class="col-md-8"> 
							<input type="text" name="profile[studio_contact]" value="<?php echo $profile['studio_contact']; ?>" />
						</div>
					</div>
				</div>	
			</div>	
			<div class="row form-footer-btns">
				<div class="col-md-4 t-left"> 
					<a class="btn btn-blue" href="<?php echo TS_STUDIO_DASHBOARD; ?>">Dashboard</a>
				</div>
				<div class="col-md-8 t-right"> 
					<a class="btn btn-gray btn-saveforlater" data-nextstep="<?php echo $next_step; ?>" href="javascript:void(0);">Save</a>
					<input class="btn btn-green" type="submit" value="Continue to Roster">
				</div>
			</div>
		</form>
	</div>	
	<?php			
}

function ts_get_studio_roster_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$participants = ts_check_value($entry_data, 'workshop', 'participants');
	$participants_ids = array_keys($participants);
	?>
	<div class="studio-roster-container">
		<form name="studio-roster" id="studio-roster" class="studio-registration registration-form roster-page validate" method="post" action="">
			<?php if( $entry_id ) { ?>
				<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
			<?php } ?>
			<input type="hidden" name="eid" value="<?php echo $eid; ?>">
			<input type="hidden" name="tab" value="roster">
			<input type="hidden" name="action" value="studio_registration">
			<input type="hidden" name="next_step" value="<?php echo $next_step; ?>">
			<?php
			$args = array(
				'orderby'          => 'meta_value',
				'order'            => 'ASC',
				'meta_key'         => 'last_name',
			);

			$studio_roster = ts_get_user_posts('ts_studio_roster', -1, false, $args);

			$argsRosterType = array(
			    'taxonomy' => 'ts_rostertype',
			    'hide_empty' => false,
			    'hierarchical' => false,
			    //'orderby' => 'meta_value_num',
			    //'meta_key' => 'type_order',
			);									
			$roster_types = get_terms($argsRosterType);
			?>
			<div class="table-container">	
				<?php
				if($studio_roster) {
					$addbutton_class = 'adddancer';
					?>
					<div class="row table-head">
						<div class="col-md-3"><strong>First Name</strong></div> 
						<div class="col-md-2"><strong>Last Name</strong></div> 
						<div class="col-md-2 t-center"><strong>Birthdate</strong></div> 
						<div class="col-md-2 t-center"><strong>Dancer/Teacher</strong></div> 
						<div class="col-md-1 t-center"><strong>Select to Register</strong><br /><input type="checkbox" class="check-all" value="" /></div> 
						<div class="col-md-2 t-center"><strong>Actions</strong></div> 
					</div>
					<div class="roster-container table-body">
						<?php
						foreach ($studio_roster as $r) {
							$rid = $r->ID;
							$roster_type = wp_get_object_terms($rid, 'ts_rostertype');
							$first_name = get_post_meta($rid, 'first_name', true);
							$last_name = get_post_meta($rid, 'last_name', true);
							$birth_date = get_post_meta($rid, 'birth_date', true);
							$order = get_post_meta($rid, 'age_cat_order', true);
							?>
							<div class="row" id="item-<?php echo $rid; ?>" data-id="<?php echo $rid; ?>" data-order="<?php echo $order; ?>">
								<div class="col-md-3 rostercurr-first_name"><?php echo $first_name; ?></div> 
								<div class="col-md-2 rostercurr-last_name"><?php echo $last_name; ?></div> 
								<div class="col-md-2 t-center rostercurr-birth_date"><?php echo $birth_date; ?></div> 
								<div class="col-md-2 t-center rostercurr-roster_type" data-id="<?php echo $roster_type[0]->term_id; ?>"><?php echo $roster_type[0]->name; ?></div> 
								<div class="col-md-1 t-center rostercurr-selected">
									<input type="checkbox" name="rostercurr[]" value="<?php echo $rid; ?>" class="select-item" <?php echo is_array($participants_ids) && in_array($rid, $participants_ids) ? 'checked' : ''; ?> />
								</div> 
								<div class="col-md-2 t-center">
									<a title="edit" href="javascript:void(0);" class="btn btn-blue btn-editroster" data-id="<?php echo $rid; ?>" data-eid="<?php echo $eid; ?>" >Edit</a>
									<a title="delete" href="javascript:void(0);" class="btn btn-red btn-delete" data-id="<?php echo $rid; ?>" data-type="roster">Delete</a>
								</div> 
							</div>
							<?php
						}
						?>
					</div>	
					<?php
				}
				else{
					$addbutton_class = 'adddancerrow';
					?>
					<div class="row table-head">
						<div class="col-md-3"><strong>First Name</strong></div> 
						<div class="col-md-3"><strong>Last Name</strong></div> 
						<div class="col-md-3 t-center"><strong>Birthdate</strong></div> 
						<div class="col-md-2 t-center"><strong>Dancer/Teacher</strong></div> 
						<div class="col-md-1 t-center"><strong>Select to Register</strong><br /><input type="checkbox" class="check-all" value="" /></div> 
					</div>	
					<div class="roster-container table-body">
						<?php
						for ($i=1; $i <= 20; $i++) { 
						?>
						<div class="row" id="item-<?php echo $i; ?>" data-id="<?php echo $i; ?>">
							<div class="col-md-3"> 
								<input type="text" name="rosternew[<?php echo $i; ?>][first_name]" value="" class="validate[required] first-name" />
							</div>
							<div class="col-md-3"> 
								<input type="text" name="rosternew[<?php echo $i; ?>][last_name]" value="" class="validate[required] last-name" />
							</div>
							<div class="col-md-3 t-center"> 
								<input type="text" name="rosternew[<?php echo $i; ?>][birth_date]" value="" maxlength="10" class="validate[required,custom[date_format]] formatted-date ts-date-picker" data-maxdate="-5Y" placeholder="MM/DD/YYYY" />
							</div>
							<div class="col-md-2 t-center"> 
								<?php
								if($roster_types) {
								?>
								<select name="rosternew[<?php echo $i; ?>][roster_type]">
									<?php
									foreach ($roster_types as $rt) {
										echo '<option value="'. $rt->term_id .'">'. $rt->name .'</option>';
									}
									?>
								</select>
								<?php
								} ?>
							</div>
							<div class="col-md-1 t-center"> 
								<input type="checkbox" name="rosternew[<?php echo $i; ?>][selected]" value="1" class="select-item" />
							</div>
						</div>
						<?php
						}
						?>
					</div>
					<?php	
				}
				?>
			</div>
			<div class="row">
				<div class="col-md-8">
					<a href="javascript:void(0);" class="btn-<?php echo $addbutton_class; ?> btn btn-green">Add Dancer</a>
				</div>
				<div class="col-md-3 t-center">
					<a class="btn-select-all" href="javascript:void(0);">Select All</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="btn-unselect-all" href="javascript:void(0);">Unselect All</a>
				</div>
				<div class="col-md-1"></div>
			</div>

			<div class="row form-footer-btns">
				<div class="col-md-4 t-left"> 
					<a class="btn btn-blue" href="<?php echo TS_STUDIO_DASHBOARD; ?>">Dashboard</a>
					<a class="btn btn-gray" href="<?php echo $base_url .'&step='. $prev_step; ?>">Back</a>
				</div>
				<div class="col-md-8 t-right"> 
					<a class="btn btn-gray btn-saveforlater" data-nextstep="<?php echo $next_step; ?>" href="javascript:void(0);">Save</a>
					<input class="btn btn-green" type="submit" value="Continue to Workshop">
				</div>
			</div>
		</form>
		<?php 
		if($studio_roster) {?>
			<div class="row rosternew-base" data-id="0">
				<div class="col-md-3"> 
					<input type="text" name="" value="" class="validate[required] rosternew-first_name first-name" />
				</div>
				<div class="col-md-2"> 
					<input type="text" name="" value="" class="validate[required] rosternew-last_name last-name" />
				</div>
				<div class="col-md-2 t-center"> 
					<input type="text" name="" value="" maxlength="10" class="validate[required,custom[date_format]] formatted-date ts-date-picker rosternew-birth_date" placeholder="MM/DD/YYYY" />
				</div>
				<div class="col-md-2 t-center"> 
					<?php
					if($roster_types) {
						?>
						<select name="" class="rosternew-roster_type">
							<?php
							foreach ($roster_types as $rt) {
								echo '<option value="'. $rt->term_id .'">'. $rt->name .'</option>';
							}
							?>
						</select>
						<?php
					} ?>
				</div>
				<div class="col-md-1 t-center"> 
					<input type="checkbox" name="" value="1" class="rosternew-selected select-item" />
				</div>
				<div class="col-md-2 t-center"> 
					<a title="delete" href="javascript:void(0);" class="btn btn-red btn-remove">Delete</a>
				</div>
			</div>
		<?php 
		}
		else { ?>
			<div class="row rosternew-base" data-id="0">
				<div class="col-md-3"> 
					<input type="text" name="" value="" class="validate[required] rosternew-first_name first-name" />
				</div>
				<div class="col-md-3"> 
					<input type="text" name="" value="" class="validate[required] rosternew-last_name last-name" />
				</div>
				<div class="col-md-3 t-center"> 
					<input type="text" name="" value="" maxlength="10" class="validate[required,custom[date_format]] formatted-date ts-date-picker rosternew-birth_date" placeholder="MM/DD/YYYY" />
				</div>
				<div class="col-md-2 t-center"> 
					<?php
					if($roster_types) {
						?>
						<select name="" class="rosternew-roster_type">
							<?php
							foreach ($roster_types as $rt) {
								echo '<option value="'. $rt->term_id .'">'. $rt->name .'</option>';
							}
							?>
						</select>
						<?php
					} ?>
				</div>
				<div class="col-md-1 t-center"> 
					<input type="checkbox" name="" value="1" class="rosternew-selected select-item" />
				</div>
			</div>
		<?php } ?>
	</div>						
	<?php 
}


