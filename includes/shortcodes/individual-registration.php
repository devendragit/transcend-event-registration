<?php
function ts_individual_registration_html($entry_data, $entry_id, $eid, $user_id, $base_url) {

	$steps = ts_get_steps_individual();
	
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
				ts_get_individual_profile_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==2) { 
				ts_get_workshop_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==3) { 
				ts_get_competition_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==4) { 
				ts_get_confirmation_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==5) { 
				ts_get_payment_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			else if($curr_step==6) { 
				ts_get_results_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps);
			} 
			?>
		</div>
	</div>
	<?php
}

function ts_get_individual_profile_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$siblings = ts_check_value($entry_data, 'profile', 'siblings');

	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$email = $current_user->user_email;

	$profile = array(
		'name' => get_field('name', 'user_'. $user_id), 
		'birth_date' => get_field('birth_date', 'user_'. $user_id), 
		'parent' => get_field('parent', 'user_'. $user_id), 
		'studio' => get_field('studio', 'user_'. $user_id), 
		'address' => get_field('address', 'user_'. $user_id), 
		'city' => get_field('city', 'user_'. $user_id), 
		'state' => get_field('state', 'user_'. $user_id), 
		'zipcode' => get_field('zipcode', 'user_'. $user_id), 
		'country' => get_field('country', 'user_'. $user_id), 
		'cell' => get_field('cell', 'user_'. $user_id), 
		'email' => $email, 
	);
	?>
	<div class="individual-profile-container">
		<form name="individual-profile" id="individual-profile" class="individual-registration registration-form validate" method="post" action="">
			<?php if( $entry_id ) { ?>
				<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
			<?php } ?>
			<input type="hidden" name="eid" value="<?php echo $eid; ?>">
			<input type="hidden" name="tab" value="profile">
			<input type="hidden" name="action" value="individual_registration">
			<input type="hidden" name="next_step" value="<?php echo $next_step; ?>">

			<div class="profile-info">
				<div class="row">
					<div class="col-md-4"> 
						<strong>Dancer's Name:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[name]" value="<?php echo $profile['name']; ?>" class="dancer-name validate[required]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Dancer's Date of Birth:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[birth_date]" value="<?php echo $profile['birth_date']; ?>" class="dancer-birthdate formatted-date validate[required,custom[date_format]]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Parent's Name </strong><small>(if under 18)</small><strong> :</strong>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[parent]" value="<?php echo $profile['parent']; ?>" class="dancer-parent" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Studio Name </strong><small>(if applicable)</small><strong> :</strong>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[studio]" value="<?php echo $profile['studio']; ?>" class="dancer-studio" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Address:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[address]" value="<?php echo $profile['address']; ?>" class="dancer-address validate[required]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>City:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[city]" value="<?php echo $profile['city']; ?>" class="validate[required]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>State:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[state]" value="<?php echo $profile['state']; ?>" class="validate[required]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Zip Code:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[zipcode]" value="<?php echo $profile['zipcode']; ?>" class="validate[required,custom[zip]]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Country:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[country]" value="<?php echo $profile['country']; ?>" class="validate[required]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Cell:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[cell]" value="<?php echo $profile['cell']; ?>" class="dancer-cell formatted-phone validate[required,custom[phone]]" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4"> 
						<strong>Email:</strong><sup class="required">*</sup>
					</div>
					<div class="col-md-8"> 
						<input type="text" name="profile[email]" value="<?php echo $profile['email']; ?>" class="dancer-email validate[required,custom[email]]" />
					</div>
				</div>
			</div>	
			<div class="siblings-container">
				<?php
				if(! empty($siblings)) {
					$args = array(
						'orderby'	=> 'meta_value_num',
						'order'		=> 'ASC',
						'include'	=> array_keys($siblings),
						'meta_key'	=> 'age_cat_order',
					);
					$siblings_posts = ts_get_user_posts('ts_sibling', -1, false, $args);

					foreach ($siblings_posts as $sibling) {
						$id 		= $sibling->ID;
						$name 		= get_post_meta($id, 'name', true);
						$birth_date = get_post_meta($id, 'birth_date', true);
						$parent 	= get_post_meta($id, 'parent', true);
						$studio 	= get_post_meta($id, 'studio', true);
						$address 	= get_post_meta($id, 'address', true);
						$city 		= get_post_meta($id, 'city', true);
						$state 		= get_post_meta($id, 'state', true); 
						$zipcode 	= get_post_meta($id, 'zipcode', true); 
						$country 	= get_post_meta($id, 'country', true); 
						$cell 		= get_post_meta($id, 'cell', true);
						$email 		= get_post_meta($id, 'email', true);
						?>
						<div class="sibling-info" data-id="<?php echo $id; ?>">
							<div class="row">
								<div class="col-md-12 t-right"> 
									<a href="javascript:void(0);" class="btn btn-red btn-removesibling">remove</a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Dancer's Name:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][name]" value="<?php echo $name; ?>" class="dancer-name validate[required]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Dancer's Date of Birth:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][birth_date]" value="<?php echo $birth_date; ?>" class="dancer-birthdate formatted-date ts-date-picker validate[required,custom[date_format]]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Parent's Name </strong><small>(if under 18)</small><strong> :</strong>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][parent]" value="<?php echo $parent; ?>" class="dancer-parent" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Studio Name </strong><small>(if applicable)</small><strong> :</strong>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][studio]" value="<?php echo $studio; ?>" class="dancer-studio" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Address:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][address]" value="<?php echo $address; ?>" class="dancer-address validate[required]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>City:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][city]" value="<?php echo $city; ?>" class="validate[required]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>State:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][state]" value="<?php echo $state; ?>" class="validate[required]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Zip Code:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][zipcode]" value="<?php echo $zipcode; ?>" class="validate[required,custom[zip]]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Country:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][country]" value="<?php echo $country; ?>" class="validate[required]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Cell:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][cell]" value="<?php echo $cell; ?>" class="dancer-cell formatted-phone validate[required]" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"> 
									<strong>Email:</strong><sup class="required">*</sup>
								</div>
								<div class="col-md-8"> 
									<input type="text" name="currsiblings[<?php echo $id; ?>][email]" value="<?php echo $email; ?>" class="dancer-email validate[required]" />
								</div>
							</div>
						</div>
						<?php
					}
				} 
				?>
			</div>	
			<div class="row">
				<div class="col-md-12"> 
					<a href="javascript:void(0);" class="btn-addsibling btn btn-green">Add Sibling</a>
				</div>
			</div>	

			<div class="row form-footer-btns">
				<div class="col-md-4 t-left"> 
					<a class="btn btn-blue" href="<?php echo TS_INDIVIDUAL_DASHBOARD; ?>">Dashboard</a>
					<a class="btn btn-gray" href="<?php echo $base_url .'&step='. $prev_step; ?>">Back</a>
				</div>
				<div class="col-md-8 t-right"> 
					<input class="btn btn-green" type="submit" value="Continue to Workshop">
				</div>
			</div>
		</form>

		<div class="sibling-base hidden">
			<div class="row">
				<div class="col-md-12 t-right"> 
					<a href="javascript:void(0);" class="btn btn-red btn-removesibling">remove</a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Dancer's Name:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-name validate[required]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Dancer's Date of Birth:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-birthdate formatted-date validate[required]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Parent's Name </strong><small>(if under 18)</small><strong> :</strong>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-parent" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Studio Name </strong><small>(if applicable)</small><strong> :</strong>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-studio" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Address:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-address validate[required]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>City:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-city validate[required]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>State:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-state validate[required]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Zip Code:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-zipcode validate[required,custom[zip]]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Country:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-country validate[required]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Cell:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-cell formatted-phone validate[required]" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-4"> 
					<strong>Email:</strong><sup class="required">*</sup>
				</div>
				<div class="col-md-8"> 
					<input type="text" class="dancer-email validate[required]" />
				</div>
			</div>
		</div>
	</div>	
	<?php			
}
