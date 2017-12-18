<?php
function ts_event_registration_shortcode() {

	ob_start();
	?>
	<style type="text/css">
		body.ts-event-registration .fl-content-full.container {
			width: 100% !important;
			max-width: none !important;
		}
		body.ts-event-registration .fl-post-header {
			max-width: 1020px;
			margin-left: auto;
			margin-right: auto;
		}
		.ts-loginform-wrapper {
			max-width: 320px;
			margin: 0 auto;
		}
		.ts-loginform-wrapper .login-submit input {
			width: auto;
		}
	</style>
	<?php
	if ( ! is_user_logged_in() ) {
		$args = array(
			'echo' => true,
			'redirect' => get_permalink(ts_get_register_page_id()),
			'form_id' => 'loginform',
			'label_username' => __( '' ),
			'label_password' => __( '' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in' => __( 'Log In' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => false,
			'value_username' => NULL,
			'value_remember' => false
		);
		?>
		<div class="ts-loginform-wrapper tml tml-login text-center">
			<?php wp_login_form( $args ); ?>
			<div class="LoginLinks">
				<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="Forgot Password">Forgot Password?</a>&nbsp;&nbsp;
				<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>&forgotusername=1" title="Forgot Username">Forgot Username?</a>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('#user_login').attr('placeholder', 'Username');
			jQuery('#user_pass').attr('placeholder', 'Password');
		</script>
		<?php
	}
	else {
		if(! is_admin()){
			?>
			<a class="logout-url" href="<?php echo wp_logout_url( get_permalink(ts_get_register_page_id()) ); ?>">Logout</a>
			<?php
		}
		wp_enqueue_media();

		$user_id 	= get_current_user_id();
		$entry_id 	= ts_get_entry_id();
		$eid 		= ts_get_current_eid();
		$base_url 	= ts_get_base_url($entry_id, $eid);
		$entry_data = ts_get_session_entry_data($eid);

		$login_count = absint(get_user_meta($user_id, 'ts_login_count', true));

		if($login_count > 0 && ! is_admin()) {
			?>
			<script type="text/javascript">
				window.location.replace('<?php echo TS_ADMIN_DASHBOARD; ?>');
			</script>
			<?php
		}
		else {
			if(current_user_can('is_studio')) {
				require_once( TS_INCLUDES . 'shortcodes/studio-registration.php' );
				ts_studio_registration_html($entry_data, $entry_id, $eid, $user_id, $base_url );
			}
			else if(current_user_can('is_individual')) {
				require_once( TS_INCLUDES . 'shortcodes/individual-registration.php' );
				ts_individual_registration_html($entry_data, $entry_id, $eid, $user_id, $base_url);
			}
			else {
				?>
				<script type="text/javascript">
					window.location.replace('<?php echo TS_ADMIN_DASHBOARD; ?>');
				</script>
				<?php
			}
		}
		?>
		<div id="popup-refresh" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
					<span class="sr-only">Loading...</span>
				</div>
			</div>
		</div>
		<?php
	}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function ts_display_tabs_html($steps, $curr_step, $base_url) {
	?>
	<div class="steps-btn-container clearfix">
		<ul class="clearfix">
			<?php
			$curr_page = ts_get_array_index($steps, $curr_step);
			foreach ($steps as $step) {
				$step_class = $step['id'] == $curr_step ? 'current-step' : '';
				$step_class.= $step['id'] < $curr_step ? ' previous-step' : '';
				$btn_link = $curr_page['title_short'] == 'Payment' ? $base_url .'&step='. $step['id'] : 'javascript:void(0);';
				$btn_class = $curr_page['title_short'] == 'Payment' ? '' : 'btn-pagenumber';
				?>
				<li class="li-step <?php echo $step_class; ?>">
					<a href="<?php echo $btn_link; ?>" data-id="<?php echo $step['id']; ?>" data-title="<?php echo $step['title_short']; ?>" class="<?php echo $btn_class; ?>">
						<span class="btn-step-outer grad-white">
							<span class="btn-step-inner grad-ligher-white">
								<span class="btn-step <?php echo $step['id'] == $curr_step ? 'grad-blue' : 'grad-light-gray'; ?>"><?php echo $step['id']; ?></span>
							</span>
						</span>
						<span class="step-title-short"><?php echo $step['title_short']; ?></span>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
	<?php
}

function ts_get_workshop_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$workshop 			= ts_check_value($entry_data, 'workshop');
	$participants 		= ts_check_value($workshop, 'participants');
	$observer 			= ts_check_value($workshop, 'observers');
	$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');

	$argsAgeDivision = array(
		'taxonomy' => 'ts_agediv',
		'hide_empty' => false,
		'hierarchical' => false,
		'orderby' => 'meta_value_num',
		'meta_key' => 'div_order',
	);
	$age_divisions = get_terms($argsAgeDivision);

	$free_teacher_ids = ts_get_free_teacher_ids($eid, $entry_data);
	$discounted_total = ts_get_discounted_total_workshop_fee($eid, $entry_data);

	$argsTourCities = array(
		'orderby' => 'meta_value',
		'order' => 'ASC',
		'meta_key' => 'date_from',
		'meta_type' => 'DATE'
	);
	$tour_cities = ts_get_posts('ts_tour', -1, $argsTourCities);
	$current_city = $workshop['tour_city'];
	$workshop_status = get_post_meta($current_city, 'workshop', true);
	$form_action = ts_get_form_action();

	?>
	<div class="workshop-container">
		<form name="entry-workshop" id="entry-workshop" class="studio-registration registration-form" method="post" action="">
			<?php if( $entry_id ) { ?>
				<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
			<?php } ?>
			<input type="hidden" name="eid" value="<?php echo $eid; ?>">
			<input type="hidden" name="tab" value="workshop">
			<input type="hidden" name="action" value="<?php echo $form_action;?>">
			<div class="row">
				<div class="col-md-12 select-tour-city-container">
					<span>Please select a city</span>
					<select name="workshop[tour_city]" class="validate[required] select-tour-city" data-eid="<?php echo $eid; ?>" <?php echo ts_is_paid($entry_id) ? 'disabled' : ''; ?>>
						<?php
						foreach ($tour_cities as $tc) {
							$tc_id 		= $tc->ID;
							$tc_title 	= $tc->post_title;
							$disabled 	= ts_is_tour_close($tc_id) ? 'disabled' : '';
							echo '<option value="'. $tc_id .'" '. ( $workshop['tour_city']==$tc_id ? 'selected' : '' ) .' '. $disabled .'>'. $tc_title .'</option>';
						}
						?>
					</select>
				</div>
			</div>
			<?php
			if($workshop_status != 2) { ?>
				<div class="table-container">
					<div class="row table-head">
						<div class="col-md-2"><strong>Name</strong></div>
						<div class="col-md-2 t-center"><strong>Age Division</strong></div>
						<div class="col-md-3 t-center"><strong>Discount/Scholarship</strong></div>
						<div class="col-md-2 t-center"><strong>Full Weekend/1-Day</strong></div>
						<div class="col-md-2 t-center"><strong>Fee</strong></div>
						<div class="col-md-1 t-center"><strong>Delete</strong></div>
					</div>
					<?php
					?>
					<div class="roster-container table-body participants-list">
						<?php
						if(! empty($participants) ){
							$args = array(
								'orderby'          => 'meta_value_num',
								'order'            => 'ASC',
								'include'          => array_keys($participants),
								'meta_key'         => 'age_cat_order',
							);
							if(current_user_can('is_studio')) {
								$post_type = 'ts_studio_roster';
							}
							else if(current_user_can('is_individual')){
								$post_type = 'ts_sibling';
							}
							$roster_posts = ts_get_user_posts($post_type, -1, false, $args);

							foreach ($roster_posts as $rp) {
								$rid 			= $rp->ID;
								$roster_type 	= wp_get_object_terms($rid, 'ts_rostertype');
								$age_div 		= wp_get_object_terms($rid, 'ts_agediv');
								$name 			= get_the_title($rid);

								$discount_id 	= $participants[$rid]['discount'];
								$duration_id 	= $participants[$rid]['duration'];
								$base_fee 		= ts_get_workshop_fee($rid, $duration_id, $eid);
								$discounted_fee = ts_get_discounted_workshop_fee($base_fee, $discount_id);
								$participant_fee 		 = in_array($rid, $free_teacher_ids) ? 0 : $discounted_fee;
								$participant_fee_preview = in_array($rid, $free_teacher_ids) ? 'Free' : '$'. number_format($discounted_fee, 2);
								$disabled_class 		 = in_array($rid, $free_teacher_ids) ? 'disabled' : '';
								?>
								<div class="row participant" id="item-<?php echo $rid; ?>" data-id="<?php echo $rid; ?>">
									<div class="col-md-2">
										<input type="hidden" class="participant-name" name="workshop[participants][<?php echo $rid; ?>][name]" value="<?php echo $first_name .' '. $last_name; ?>" />
										<span class="participant-name-preview"><?php echo $name; ?></span>
									</div>
									<div class="col-md-2 t-center <?php echo $disabled_class; ?>-container">
										<?php
										if($age_divisions) {
											?>
											<select name="workshop[participants][<?php echo $rid; ?>][age_division]" id="age-division-<?php echo $rid; ?>" data-id="<?php echo $rid; ?>" data-eid="<?php echo $eid; ?>" class="adjust-workshop-fee select-age-division <?php echo $disabled_class; ?>">
												<option value="">None</option>
												<?php
												foreach ($age_divisions as $ad) {
													$ad_id = $ad->term_id;
													$ad_order = get_term_meta($ad_id, 'div_order', true);
													echo '<option data-order="'. $ad_order .'" class="age-div-'. $ad_id .'" value="'. $ad_id .'" '. ( $ad_id==$age_div[0]->term_id ? 'selected' : '' ) .'>'. $ad->name .'</option>';
												}
												?>
											</select>
											<?php
										} ?>
									</div>
									<div class="col-md-3 t-center <?php echo $disabled_class; ?>-container">
										<select name="workshop[participants][<?php echo $rid; ?>][discount]" id="discount-<?php echo $rid; ?>" data-id="<?php echo $rid; ?>" data-eid="<?php echo $eid; ?>" class="adjust-workshop-fee select-discount <?php echo $disabled_class; ?>">
											<option value="0">None</option>
											<?php
											$discounts = ts_get_discounts();
											foreach ($discounts as $dc) {
												echo '<option value="'. $dc['id'] .'" '. ( $participants[$rid]['discount']==$dc['id'] ? 'selected' : '' ) .'>'. $dc['title'] .'</option>';
											}
											?>
										</select>
									</div>
									<div class="col-md-2 t-center <?php echo $disabled_class; ?>-container">
										<select name="workshop[participants][<?php echo $rid; ?>][duration]" id="duration-<?php echo $rid; ?>" data-id="<?php echo $rid; ?>" data-eid="<?php echo $eid; ?>" class="adjust-workshop-fee select-duration <?php echo $disabled_class; ?>">
											<?php
											$munchkins = get_term_by('name', 'Munchkin', 'ts_agediv');
											$munchkins_id = $munchkins->term_id;

											$duration = ts_get_workshop_durations();
											foreach ($duration as $wd) {
												$disabled = $age_div[0]->term_id == $munchkins_id && $wd['id']==2 ? 'disabled' : '';
												echo '<option '. $disabled .' value="'. $wd['id'] .'" '. ( $participants[$rid]['duration']==$wd['id'] ? 'selected' : '' ) .'>'. $wd['title'] .'</option>';
											}
											?>
										</select>
									</div>
									<div class="col-md-2 t-center">
										<input type="hidden" name="workshop[participants][<?php echo $rid; ?>][fee]" id="fee-<?php echo $rid; ?>" value="<?php echo $participant_fee; ?>" />
										<span id="fee-preview-<?php echo $rid; ?>"><?php echo $participant_fee_preview; ?></span>
									</div>
									<div class="col-md-1 t-center"><a href="javascript:void(0);" class="btn btn-red btn-removeparticipant" data-id="<?php echo $rid; ?>" data-eid="<?php echo $eid; ?>">Delete</a></div>
								</div>
								<?php
							}
						}
						?>
					</div>
					<div class="observers-list table-body">
						<?php
						$countRosterPosts = count($roster_posts);
						if($countRosterPosts % 2 !==0) {
							?>
							<div class="row fillter-row"></div>
							<?php
						}

						if($observer){
							foreach ($observer as $key => $value) {
								$id = $key;
								$fee = ts_get_observer_fee();
								$name = $value['name'];
								?>
								<div class="row observer" id="observer-<?php echo $id; ?>" data-id="<?php echo $id; ?>" class="workshop-observer">
									<div class="col-md-2">
										<input type="hidden" class="observer-name" name="workshop[observers][<?php echo $id; ?>][name]" value="<?php echo $name; ?>">
										<span class="observer-name-preview"><?php echo $value['name']; ?></span>
									</div>
									<div class="col-md-2 t-center">N/A</div>
									<div class="col-md-3 t-center">N/A</div>
									<div class="col-md-2 t-center">N/A</div>
									<div class="col-md-2 t-center">
										<input class="observer-fee" type="hidden" name="workshop[observers][<?php echo $id; ?>][fee]" value="<?php echo $fee; ?>">
										$<span class="observer-fee-preview"><?php echo number_format($fee, 2); ?></span>
									</div>
									<div class="col-md-1 t-center">
										<a href="javascript:void(0);" class="btn btn-red btn-removeobserver" data-id="<?php echo $id; ?>" data-eid="<?php echo $eid; ?>">Delete</a>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
					<div class="munchkin-observers-list table-body">
						<?php
						$countObserver = count($observer);
						if( ($countObserver%2 == 0 && $countRosterPosts%2 !==0) || $countObserver%2 !== 0 && $countRosterPosts%2 ==0 ) {
							?>
							<div class="row fillter-row"></div>
							<?php
						}

						if($munchkin_observer) {
							foreach ($munchkin_observer as $key => $value) {
								$id = $key;
								$fee = ts_get_munchkin_observer_fee();
								$name = $value['name'];
								?>
								<div class="row munchkin-observer" id="munchkin-observer-<?php echo $id; ?>" data-id="<?php echo $id; ?>" class="workshop-observer">
									<div class="col-md-2">
										<input type="hidden" class="observer-name" name="workshop[munchkin_observers][<?php echo $id; ?>][name]" value="<?php echo $name; ?>">
										<span class="observer-name-preview"><?php echo $value['name']; ?></span>
									</div>
									<div class="col-md-2 t-center">N/A</div>
									<div class="col-md-3 t-center">N/A</div>
									<div class="col-md-2 t-center">N/A</div>
									<div class="col-md-2 t-center">
										<input class="observer-fee" type="hidden" name="workshop[munchkin_observers][<?php echo $id; ?>][fee]" value="<?php echo $fee; ?>">
										$<span class="observer-fee-preview"><?php echo number_format($value['fee'], 2); ?></span>
									</div>
									<div class="col-md-1 t-center">
										<a href="javascript:void(0);" class="btn btn-red btn-removemunchkinobserver" data-id="<?php echo $id; ?>" data-eid="<?php echo $eid; ?>">Delete</a>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-9 addbutton-container">
						<?php if(current_user_can('is_studio')) { ?>
							<a href="javascript:void(0);" class="btn-addfromroster btn btn-gray">Add from Roster</a>
						<?php } ?>
						<a href="javascript:void(0);" class="btn-addobserver btn btn-gray" data-eid="<?php echo $eid; ?>">Add Observer</a>
						<a href="javascript:void(0);" class="btn-addmunchkinobserver btn btn-gray" data-eid="<?php echo $eid; ?>">Add Additional Munchkin Observer</a>
					</div>
					<div class="col-md-2 t-center">
						<input type="hidden" name="workshop[discounted_total]" id="total-fee" value="<?php echo $discounted_total; ?>" />
						<strong>Total Fee: $<span id="total-fee-preview"><?php echo number_format( $discounted_total, 2 ); ?></span></strong>
					</div>
					<div class="col-md-1">
					</div>
				</div>
				<?php
			}
			else {
				echo '
				<div class="form-container-2 t-center boxed-container">
					<h1>Workshop registration is already closed for this city.</h1>
				</div>';
			}
			?>
			<div class="row form-footer-btns">
				<div class="col-md-4 t-left">
					<a class="btn btn-blue" href="<?php echo TS_STUDIO_DASHBOARD; ?>">Dashboard</a>
					<a class="btn btn-gray" href="<?php echo $base_url .'&step='. $prev_step; ?>">Back</a>
				</div>
				<div class="col-md-8 t-right">
					<a class="btn btn-gray btn-saveforlater" data-nextstep="<?php echo $next_step; ?>" href="javascript:void(0);">Save</a>
					<button class="btn btn-blue btn-submitworkshop" type="button" value="<?php echo $next_step; ?>">Continue to Competition</button>
					<button class="btn btn-green btn-submitworkshop" type="button" value="<?php echo $next_step+1; ?>">Continue to Confirmation</button>
				</div>
			</div>
		</form>

		<div class="row workshop-observer-base hidden" id="" data-id="">
			<div class="col-md-2">
				<input type="hidden" class="observer-name" name="" value="">
				<span class="observer-name-preview"></span>
			</div>
			<div class="col-md-2 t-center">N/A</div>
			<div class="col-md-3 t-center">N/A</div>
			<div class="col-md-2 t-center">N/A</div>
			<div class="col-md-2 t-center">
				<input class="observer-fee" type="hidden" name="" value="">
				$<span class="observer-fee-preview"></span>
			</div>
			<div class="col-md-1 t-center">
				<a href="javascript:void(0);" class="btn btn-red btn-removeobserver" data-id="" data-eid="">Delete</a>
			</div>
		</div>

		<div id="add-fromroster" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Add Dancer From Roster</h4>
					</div>
					<div class="modal-body">
						<?php
						$args = array(
							'orderby' => 'meta_value_num',
							'order' => 'ASC',
							'meta_key' => 'age_cat_order',
						);
						$roster = ts_get_user_posts('ts_studio_roster', -1, false, $args);

						if($roster) {
							?>
							<form method="post" action="" id="add-workshop-participants" name="add-workshop-participants" >
								<input type="hidden" name="eid" value="<?php echo $eid; ?>">
								<div class="dancer-list table-container">
									<div class="row table-head">
										<div class="col-md-6"><strong>Name</strong></div>
										<div class="col-md-6 t-center"><strong>Age Division</strong></div>
									</div>
									<div class="table-body">
										<?php
										foreach($roster as $r) {
											$rid = $r->ID;
											$name = get_the_title($rid);
											$age_div = wp_get_object_terms($rid, 'ts_agediv');
											$checked = array_key_exists($rid, $participants) ? 'checked disabled' : '';
											?>
											<div class="row">
												<div class="col-md-6">
													<label><input type="checkbox" name="participants[]" value="<?php echo $rid; ?>" id="participant-<?php echo $rid; ?>" <?php echo $checked; ?> />
														<span><?php echo $name; ?></span></label>
												</div>
												<div class="col-md-6 t-center">
													<?php echo $age_div[0]->name; ?>
												</div>
											</div>
											<?php
										}
										?>
									</div>
								</div>
								<div class="clearfix">
									<input type="submit" value="Add" class="btn btn-blue">
								</div>
							</form>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function ts_get_competition_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$workshop = ts_check_value($entry_data, 'workshop');
	$participants = ts_check_value($workshop, 'participants');
	$competition = ts_check_value($entry_data, 'competition');
	$total_competition_fee = ts_get_total_competition_fee($eid, $entry_data);
	$form_action = ts_get_form_action();
	$current_city = $workshop['tour_city'];
	$competition_status = get_post_meta($current_city, 'competition', true);
	?>
	<div class="studio-competition-container">
		<form name="studio-competition" id="studio-competition" class="studio-registration registration-form validate competition-page" method="post" action="">
				<?php if( $entry_id ) { ?>
					<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
				<?php } ?>
				<input type="hidden" name="eid" value="<?php echo $eid; ?>">
				<input type="hidden" name="tab" value="competition">
				<input type="hidden" name="action" value="<?php echo $form_action;?>">
				<input type="hidden" name="next_step" value="<?php echo $next_step; ?>">
				<?php
				if($competition_status != 2) {
					?>
					<div class="table-container">
						<?php
						$routines = $competition['routines'];

						$routine_ids = ! empty($routines) ? array_keys($routines) : 0;

						$args = array(
							'order'            => 'ASC',
							'include'          => $routine_ids,
						);
						$routine_posts = ts_get_user_posts('ts_routine', -1, false, $args);

						if($routine_ids!==0 && $routine_posts) {
							?>
							<div class="row table-head">
								<div class="col-sm-2"><strong>Routine Name</strong></div>
								<div class="col-sm-2 t-center"><strong>Dancers</strong></div>
								<div class="col-sm-1 t-center"><strong>Age Division</strong></div>
								<div class="col-sm-1 t-center"><strong>Category</strong></div>
								<div class="col-sm-1 t-center"><strong>Genre</strong></div>
								<div class="col-sm-1 t-center"><strong>Enter / Exit with / without music</strong></div>
								<div class="col-sm-1 t-center"><strong>Are there Props w/ set up / clean up?</strong></div>
								<div class="col-sm-1 t-center"><strong>Music</strong></div>
								<div class="col-sm-1 t-center"><strong>Fee</strong></div>
								<div class="col-sm-1 t-center"><strong>Delete</strong></div>
							</div>
							<div class="routine-container table-body">
								<?php
								foreach ($routine_posts as $rp) {
									$rpid 	 	= $rp->ID;
									$name 	 	= get_the_title($rpid);
									$dancers 	= get_post_meta($rpid, 'dancers', true);
									$agediv  	= get_post_meta($rpid, 'agediv', true);
									$cat 	 	= get_post_meta($rpid, 'cat', true);
									$genre 	 	= (int)get_post_meta($rpid, 'genre', true);
									$flow 	 	= (int)get_post_meta($rpid, 'flows', true);
									$music 	 	= (int)get_post_meta($rpid, 'music', true);
									$prop 	 	= (int)get_post_meta($rpid, 'props', true);

									$dancersArray  = is_array($dancers) ? $dancers : explode(",", $dancers);
									$dancersString = ! is_array($dancers) ? $dancers : implode(",", $dancers);

									$dancers_count = get_post_meta($rpid, 'dancers_count', true);
									$dancers_count_edited = get_post_meta($rpid, 'dancers_count_edited', true);
									$countDancers = $dancers_count_edited ? $dancers_count_edited : $dancers_count;
									//$countDancers = count($dancersArray);
									$fee 	 	  = ts_get_routine_fee($countDancers);
									?>

									<div class="row" id="item-<?php echo $rpid; ?>" data-id="<?php echo $rpid; ?>">
										<div class="col-sm-2">
											<input name="routinecurr[<?php echo $rpid; ?>][name]" value="<?php echo $name; ?>" id="routine-name-<?php echo $rpid; ?>" class="routine-name validate[required]" type="text">
										</div>
										<div class="col-sm-2 t-center">
									<span class="routine-dancers-preview" id="routine-dancers-preview-<?php echo $rpid; ?>">
										<?php
										$ids = $dancersArray;
										$count_d = count($ids);
										$age_total = 0;
										if(! empty($ids)){
											$count=0;
											foreach ($ids as $d) {
												if(ts_post_exists_by_id($d)){
													if($count>0) echo ', ';
													echo get_the_title($d);
													$birth_date = get_post_meta($d, 'birth_date', true);
													$age = ts_get_the_age($birth_date);
													$age_total = $age_total + $age;
													$count++;
												}
											}
											$age_ave = round($age_total / $count_d);
											$age_div_name = ts_get_routine_agediv_name($age_ave);
										}
										?>
									</span>
											<input class="routine-dancers validate[required]" id="routine-dancers-<?php echo $rpid; ?>" name="routinecurr[<?php echo $rpid; ?>][dancers]" value="<?php echo $age_total !==0 ? $dancersString : ''; ?>" type="text">
											<a href="javascript:void(0);" class="btn-addroutinedancers btn btn-green" data-id="<?php echo $rpid; ?>"><small>Edit</small></a>
										</div>
										<div class="col-sm-1 t-center">
											<span class="routine-agediv-preview" id="routine-agediv-preview-<?php echo $rpid; ?>"><?php echo $age_div_name; ?></span>
											<input class="routine-agediv" id="routine-agediv-<?php echo $rpid; ?>" name="routinecurr[<?php echo $rpid; ?>][agediv]" value="<?php echo $age_div_name; ?>" type="hidden">
										</div>
										<div class="col-sm-1 t-center">
									<span class="routine-cat-preview" id="routine-cat-preview-<?php echo $rpid; ?>">
										<?php
										$categories = ts_get_competition_categories();
										echo $categories[$cat]['title'];
										?>
									</span>
											<input class="routine-cat" id="routine-cat-<?php echo $rpid; ?>" name="routinecurr[<?php echo $rpid; ?>][cat]" value="<?php echo $cat; ?>" class="" type="hidden">
										</div>
										<div class="col-sm-1 t-center">
											<select class="routine-genre" name="routinecurr[<?php echo $rpid; ?>][genre]" >
												<option value="">None</option>
												<?php
												$genres = ts_get_routine_genres();
												foreach ($genres as $g) {
													echo '<option value="'. $g['id'] .'" '. ( $genre==$g['id'] ? 'selected' : '' ) .'>'. $g['title'] .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-1 t-center">
											<select class="routine-flows" name="routinecurr[<?php echo $rpid; ?>][flows]" >
												<option value="">None</option>
												<?php
												$flows = ts_get_routine_flows();
												foreach ($flows as $f) {
													echo '<option value="'. $f['id'] .'" '. ( $flow==$f['id'] ? 'selected' : '' ) .'>'. $f['title'] .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-1 t-center">
											<select class="routine-props" name="routinecurr[<?php echo $rpid; ?>][props]" >
												<?php
												$props = ts_get_routine_props();
												foreach ($props as $p) {
													echo '<option value="'. $p['id'] .'" '. ( $prop==$p['id'] ? 'selected' : '' ) .'>'. $p['title'] .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-1 t-center routine-music-container">
											<input class="routine-music" id="routine-music-<?php echo $rpid; ?>" name="routinecurr[<?php echo $rpid; ?>][music]" value="<?php echo $music; ?>" type="hidden">
											<?php
											if(! $music ) {
												echo '
										<a href="javascript:void(0);" class="btn-addroutinemusic btn btn-green" data-id="'. $rpid .'"><small>Add</small></a>';
											}
											else{
												$music_filename = basename(get_attached_file($music));
												echo '
										<div><small>'. $music_filename .'</small></div>
										<a href="javascript:void(0);" class="btn-removeroutinemusic btn btn-red" data-id="'. $rpid .'"><small>Remove</small></a>';
											}
											?>
										</div>
										<div class="col-sm-1 t-center">
											$<span class="routine-fee-preview" id="routine-fee-preview-<?php echo $rpid; ?>"><?php echo number_format($fee, 2); ?></span>
											<input class="routine-fee" id="routine-fee-<?php echo $rpid; ?>" name="routinecurr[<?php echo $rpid; ?>][fee]" value="<?php echo $fee; ?>" type="hidden">
										</div>
										<div class="col-sm-1 t-center">
											<a href="javascript:void(0);" class="btn-delete-routine btn btn-red" data-id="<?php echo $rpid; ?>" data-eid="<?php echo $eid; ?>"><small>Delete</small></a>
										</div>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
						else{
							?>
							<div class="row table-head">
								<div class="col-sm-2"><strong>Routine Name</strong></div>
								<div class="col-sm-2 t-center"><strong>Dancers</strong></div>
								<div class="col-sm-2 t-center"><strong>Age Division</strong></div>
								<div class="col-sm-1 t-center"><strong>Category</strong></div>
								<div class="col-sm-1 t-center"><strong>Genre</strong></div>
								<div class="col-sm-1 t-center"><strong>Enter / Exit with / without music</strong></div>
								<div class="col-sm-1 t-center"><strong>Are there Props w/ set up / clean up?</strong></div>
								<div class="col-sm-1 t-center"><strong>Music</strong></div>
								<div class="col-sm-1 t-center"><strong>Fee</strong></div>
							</div>
							<div class="routine-container table-body">
								<?php
								for ($i=1; $i <= 10; $i++) {
									?>
									<div class="row" id="item-<?php echo $i; ?>" data-id="<?php echo $i; ?>">
										<div class="col-sm-2">
											<input name="routinenew[<?php echo $i; ?>][name]" value="" id="routine-name-<?php echo $i; ?>" class="routine-name validate[required]" type="text">
										</div>
										<div class="col-sm-2 t-center">
											<span class="routine-dancers-preview" id="routine-dancers-preview-<?php echo $i; ?>"></span>
											<input class="routine-dancers validate[required]" id="routine-dancers-<?php echo $i; ?>" name="routinenew[<?php echo $i; ?>][dancers]" value="" type="text">
											<a href="javascript:void(0);" class="btn-addroutinedancers btn btn-green" data-id="<?php echo $i; ?>"><small>Edit</small></a>
										</div>
										<div class="col-sm-2 t-center">
											<span id="routine-agediv-preview-<?php echo $i; ?>" class="routine-agediv-preview"></span>
											<input id="routine-agediv-<?php echo $i; ?>" name="routinenew[<?php echo $i; ?>][agediv]" value="" class="routine-agediv" type="hidden">
										</div>
										<div class="col-sm-1 t-center">
											<span id="routine-cat-preview-<?php echo $i; ?>" class="routine-cat-preview"></span>
											<input id="routine-cat-<?php echo $i; ?>" name="routinenew[<?php echo $i; ?>][cat]" value="" class="routine-cat" type="hidden">
										</div>
										<div class="col-sm-1 t-center">
											<select name="routinenew[<?php echo $i; ?>][genre]" class="routine-genre" >
												<option value="">None</option>
												<?php
												$genres = ts_get_routine_genres();
												foreach ($genres as $g) {
													echo '<option value="'. $g['id'] .'">'. $g['title'] .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-1 t-center">
											<select name="routinenew[<?php echo $i; ?>][flows]" class="routine-flows" >
												<option value="">None</option>
												<?php
												$flows = ts_get_routine_flows();
												foreach ($flows as $f) {
													echo '<option value="'. $f['id'] .'">'. $f['title'] .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-1 t-center">
											<select name="routinenew[<?php echo $i; ?>][props]" class="routine-props" >
												<?php
												$props = ts_get_routine_props();
												foreach ($props as $p) {
													echo '<option value="'. $p['id'] .'">'. $p['title'] .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-1 t-center routine-music-container">
											<input class="routine-music" id="routine-music-<?php echo $i; ?>" name="routinenew[<?php echo $i; ?>][music]" value="" type="hidden">
											<a href="javascript:void(0);" class="btn-addroutinemusic btn btn-green" data-id="<?php echo $i; ?>"><small>Add</small></a>
										</div>
										<div class="col-sm-1 t-center">
											<span id="routine-fee-preview-<?php echo $i; ?>" class="routine-fee-preview"></span>
											<input id="routine-fee-<?php echo $i; ?>" name="routinenew[<?php echo $i; ?>][fee]" value="" class="routine-fee" type="hidden">
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
						<div class="col-md-6">
							<?php if(! empty($routine_posts) ) { ?>
								<a href="javascript:void(0);" class="btn-addroutine btn btn-green">Add Routine</a>
							<?php } ?>
						</div>
						<div class="col-md-6 t-right">
							<input type="hidden" name="competition[total_fee]" id="total-fee" value="<?php echo $total_competition_fee; ?>" />
							<strong>Total Fee: $<span id="total-fee-preview"><?php echo number_format( $total_competition_fee, 2 ); ?></span></strong>
						</div>
					</div>
					<?php
				} else {
					echo '<div class="form-container-2 t-center boxed-container">
					<h1>Competition registration is already closed for this city.</h1>
				  </div>';
				}
				?>
				<div class="row form-footer-btns">
					<div class="col-md-4 t-left">
						<a class="btn btn-blue" href="<?php echo TS_STUDIO_DASHBOARD; ?>">Dashboard</a>
						<a class="btn btn-gray" href="<?php echo $base_url .'&step='. $prev_step; ?>">Back</a>
					</div>
					<div class="col-md-8 t-right">
						<a class="btn btn-gray btn-saveforlater" data-nextstep="<?php echo $next_step; ?>" href="javascript:void(0);">Save</a>
						<button class="btn btn-green" type="submit">Continue to Confirmation</button>
					</div>
				</div>
			</form>
			<div id="add-dancers" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Edit Routine Dancers</h4>
						</div>
						<div class="modal-body">
							<?php
							$args = array(
								'orderby' => 'meta_value_num',
								'order' => 'ASC',
								'meta_key' => 'age_cat_order',
								'tax_query' => array(
									array(
										'taxonomy' => 'ts_agediv',
										'field' => 'name',
										'terms' => 'Teacher',
										'operator' => 'NOT IN',
									)
								),
							);
							if(current_user_can('is_studio')) {
								$post_type = 'ts_studio_roster';
							}
							else if(current_user_can('is_individual')){
								$post_type = 'ts_sibling';
							}

							$args['include'] = array_keys($participants);

							$studio_roster = ts_get_user_posts($post_type, -1, false, $args);

							if($studio_roster) {
								?>
								<form method="post" action="" id="add-routine-dancers" name="add-routine-dancers" >
									<input type="hidden" name="routine-id" id="routine-id" value="" />
									<input type="hidden" name="routine-name" id="routine-name" value="" />
									<input type="hidden" name="eid" value="<?php echo $eid; ?>" />
									<div class="dancer-list table-container">
										<div class="row table-head">
											<div class="col-md-6"><strong>Name</strong></div>
											<div class="col-md-6 t-center"><strong>Age Division</strong></div>
										</div>
										<div class="table-body">
											<?php
											foreach($studio_roster as $sr) {
												$rid = $sr->ID;
												$age_div = wp_get_object_terms($rid, 'ts_agediv');
												$name = get_the_title($rid);
												?>
												<div class="row">
													<div class="col-md-6">
														<label><input type="checkbox" name="dancers[]" value="<?php echo $rid; ?>" />
															<?php echo $name; ?></label>
													</div>
													<div class="col-md-6 t-center">
														<?php echo $age_div[0]->name; ?>
													</div>
												</div>
												<?php
											}
											?>
										</div>
									</div>
									<div class="clearfix">
										<input type="submit" value="Add/Edit" class="btn btn-blue">
									</div>
								</form>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
	</div>
	<?php
}

function ts_get_confirmation_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$workshoppage = $steps['workshop']['id'];

	if(ts_is_noworkshopentry($entry_data)) {
		?>
		<div class="form-container-2 t-center boxed-container">
			<h1>Sorry, you cannot complete the registration without workshop entries.</h1>
		</div>
		<script type="text/javascript">
			setTimeout(function(){
				window.location.replace("<?php echo $base_url .'&step='. $workshoppage; ?>");
			}, 5000);
		</script>
		<?php
	}
	else {

		$competition 		= ts_check_value($entry_data, 'competition');
		$workshop 			= ts_check_value($entry_data, 'workshop');
		$participants 		= ts_check_value($workshop, 'participants');
		$observer 			= ts_check_value($workshop, 'observers');
		$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');

		$countObservers = count($observer);
		$countMunchkinObservers = count($munchkin_observer);

		$countMunchkin = 0;
		$countMinis = 0;
		$countJuniors = 0;
		$countTeens = 0;
		$countSeniors = 0;
		$countPros = 0;
		$countTeachers = 0;

		$Munchkin = get_term_by('name', 'Munchkin', 'ts_agediv');
		$Mini = get_term_by('name', 'Mini', 'ts_agediv');
		$Junior = get_term_by('name', 'Junior', 'ts_agediv');
		$Teen = get_term_by('name', 'Teen', 'ts_agediv');
		$Senior = get_term_by('name', 'Senior', 'ts_agediv');
		$Pro = get_term_by('name', 'Pro', 'ts_agediv');
		$Teacher = get_term_by('name', 'Teacher', 'ts_agediv');

		foreach ($participants as $key => $value) {
			$agediv = $value['age_division'];

			if($agediv==$Munchkin->term_id) {
				$countMunchkin++;
			}
			else if($agediv==$Mini->term_id) {
				$countMinis++;
			}
			else if($agediv==$Junior->term_id) {
				$countJuniors++;
			}
			else if($agediv==$Teen->term_id) {
				$countTeens++;
			}
			else if($agediv==$Senior->term_id) {
				$countSeniors++;
			}
			else if($agediv==$Pro->term_id) {
				$countPros++;
			}
			else if($agediv==$Teacher->term_id) {
				$countTeachers++;
			}
		}

		$workshop_fee 					= ts_get_total_workshop_fee($eid);
		$workshop_teacher_discount 		= ts_get_total_teacher_discount($eid);
		$workshop_scholarship_discount 	= ts_get_total_scholarship_discount($eid);
		$workshop_fee_discounted 		= ts_get_discounted_total_workshop_fee($eid);
		$competition_fee 				= ts_get_total_competition_fee($eid);
		$form_action 					= ts_get_form_action();

		$discount_code_applied 			= get_post_meta($entry_id, 'discount_code_applied', true);
		?>
		<div class="studio-confirmation-container">
			<h1 class="heading-title"><?php echo get_the_title($workshop['tour_city']); ?></h1>
			<form name="studio-confirmation" id="studio-confirmation" class="studio-registration registration-form confirmation-page" method="post" action="">
				<?php if( $entry_id ) { ?>
					<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
				<?php } ?>
				<input type="hidden" name="eid" value="<?php echo $eid; ?>">
				<input type="hidden" name="tab" value="confirmation">
				<input type="hidden" name="action" value="<?php echo $form_action;?>">
				<input type="hidden" name="next_step" value="<?php echo $next_step; ?>">
				<div class="row">
					<div class="col-md-6 workshop-fee-breakdown">
						<div class="row">
							<div class="col-md-8"><strong class="outlined">Workshop</strong></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countMunchkin; ?></span> <span>Munchkin</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countMinis; ?></span> <span>Minis</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countJuniors; ?></span> <span>Juniors</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countTeens; ?></span> <span>Teens</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countSeniors; ?></span> <span>Seniors</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countPros; ?></span> <span>Pros</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countTeachers; ?></span> <span>Teachers</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countObservers; ?></span> <span>Observers</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-8"><span class="item-count"><?php echo $countMunchkinObservers; ?></span> <span>Additional Munchkin Observers</span></div>
							<div class="col-md-4"></div>
						</div>
						<div class="row">
							<div class="col-md-12">&nbsp;</div>
						</div>
						<div class="row">
							<div class="col-md-8"><strong class="underlined">Workshop Price Before Discounts</strong></div>
							<div class="col-md-3 t-right"><strong class="amount">$<?php echo number_format($workshop_fee, 2); ?></strong></div>
						</div>
						<div class="row">
							<div class="col-md-12">&nbsp;</div>
						</div>
						<div class="row">
							<div class="col-md-8">Teacher Discounts</div>
							<div class="col-md-3 t-right"><strong>$<?php echo number_format($workshop_teacher_discount, 2); ?></strong></div>
						</div>
						<div class="row">
							<div class="col-md-8">Scholarships/Discounts</div>
							<div class="col-md-3 t-right"><strong>$<?php echo number_format($workshop_scholarship_discount, 2); ?></strong></div>
						</div>
						<div class="row">
							<div class="col-md-12">&nbsp;</div>
						</div>
						<div class="row">
							<div class="col-md-8"><strong class="boxed">Workshop Total</strong></div>
							<div class="col-md-3 t-right">
								<strong class="amount">$<?php echo number_format($workshop_fee_discounted, 2); ?></strong>
							</div>
						</div>
					</div>
					<div class="col-md-6 competition-fee-breakdown">
						<div class="row">
							<div class="col-md-1">&nbsp;</div>
							<div class="col-md-8"><strong class="outlined">Competition</strong></div>
						</div>
						<?php
						$routines = $competition['routines'];

						if(is_array($routines) && ! empty($routines) ) {
							foreach ($routines as $key=>$r) {
								$name = $r['name'];
								$dancers_count = get_post_meta($key, 'dancers_count', true);
								$dancers_count_edited = get_post_meta($key, 'dancers_count_edited', true);
								$dancersCount = $dancers_count_edited ? $dancers_count_edited : $dancers_count;
								//$dancersCount = count(explode(",",$r['dancers']));
								?>
								<div class="row">
									<div class="col-md-1">&nbsp;</div>
									<div class="col-md-7"><?php echo $name; ?></div>
									<div class="col-md-4 t-right"><strong>$<?php echo number_format(ts_get_routine_fee($dancersCount),2); ?></strong></div>
								</div>
								<?php
							}
						}
						?>
						<div class="row">
							<div class="col-md-1">&nbsp;</div>
							<div class="col-md-11"><strong class="underlined">Total Number of Routines <span class="total-routines f-right"><?php echo count($routines); ?></span></strong></div>
						</div>
						<div class="row">
							<div class="col-md-1">&nbsp;</div>
							<div class="col-md-11">
								<strong class="boxed">Competition Total <span class="total-competition-fee f-right">$<?php echo number_format($competition_fee, 2); ?></span></strong>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">&nbsp;</div>
				</div>
				<div class="row">
					<div class="col-md-12 t-right coupon-container">
						<?php
						$grand_total = ts_grand_total($eid, $entry_data);
						if(! $discount_code_applied) {
							if(isset($entry_data['discount_code']) && $entry_data['discount_code']!='' && ts_discounted_grand_total($grand_total, $entry_data['discount_code'], $entry_id) ) { ?>
								<input type="hidden" name="discount_code" value="<?php echo $entry_data['discount_code']; ?>" >
								Discount Code: <strong><?php echo $entry_data['discount_code']; ?></strong>
								<button type="button" data-eid="<?php echo $eid; ?>" class="btn btn-blue btn-removecoupon">Remove</button>
								<?php
							}
							else {
								?>
								<label><input type="text" value="" id="discount-coupon" name="discount-coupon" /></label>
								<button type="button" data-eid="<?php echo $eid; ?>" class="btn btn-blue btn-applycoupon">Apply Voucher</button>
								<?php
							} 
						}	
						?>
					</div>
				</div>
				<div class="row grand-total">
					<?php
					if(isset($entry_data['discount_code']) && $entry_data['discount_code']!='' && ! $discount_code_applied) {
						$grand_total = ts_discounted_grand_total($grand_total, $entry_data['discount_code'], $entry_id);
					}
					$amount_paid = $amount_payable = $amount_credit = '';
					$button_value = 'Confirm and Continue to Payment';
					if(ts_is_paid($entry_id)){
						$amount_paid = absint(get_post_meta($entry_id,'paid_amount',true));
						if( $amount_paid > $grand_total ){
							$amount_credit = ts_return_credit_total($grand_total, $entry_id);
							$button_value = $amount_credit > 0 ? 'Confirm and Continue to receive credit' : 'Confirm and Save your registration';
						} else if( $amount_paid < $grand_total ) {
							$amount_payable = $grand_total - $amount_paid;
							$button_value = 'Confirm and Continue to pay remaining amount';
						} else {
							$button_value = 'Confirm and Save your registration';
						}
					}
					?>
					<div class="col-md-12 t-right">Grand Total:
						$<span id="grand-total"><?php echo number_format($grand_total, 2); ?></span>
					</div>
					<?php
					if( !empty($amount_payable) ){
						?>
						<div class="col-md-12 t-right">Amount Paid:
							$<span id="grand-total"><?php echo number_format($amount_paid, 2); ?></span>
						</div>
						<div class="col-md-12 t-right">Amount Payable:
							$<span id="grand-total"><?php echo number_format($amount_payable, 2); ?></span>
							<input type="hidden" name="remaining_amount" value="<?php echo $amount_payable; ?>" />
						</div>
						<?php
					}
					?>
					<?php
					if( !empty($amount_credit) ){
						?>
						<div class="col-md-12 t-right">Amount Credit:
							$<span id="grand-total"><?php echo number_format($amount_credit, 2); ?></span>
							<input type="hidden" name="amount_credited" value="<?php echo $amount_credit; ?>" />
						</div>
						<?php
					}
					?>
				</div>
				<div class="row form-footer-btns">
					<div class="col-md-4 t-left">
						<a class="btn btn-blue" href="<?php echo TS_STUDIO_DASHBOARD; ?>">Dashboard</a>
						<a class="btn btn-gray" href="<?php echo $base_url .'&step='. $prev_step; ?>">Back</a>
					</div>
					<div class="col-md-8 t-right">
						<a class="btn btn-gray btn-saveforlater" data-nextstep="<?php echo $next_step; ?>" href="javascript:void(0);">Save</a>
						<?php
						$status = get_post_status( $entry_id );
						if($status=='paid' || $status=='paidcheck'){
							?>
							<input class="btn btn-green" type="submit" value="<?php echo $button_value;?>">
							<?php
						}
						else { ?>
							<a class="btn btn-green btn-popupwaiver" href="javascript:void(0);"><?php echo $button_value;?></a>
							<input class="btn hidden btn-submitconfirmation" type="submit" value="Submit">
							<?php
						} ?>
					</div>
				</div>
			</form>
		</div>
		<?php
		ts_waiver_popup();
	}
}

function ts_get_payment_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$confirmation = $steps['confirmation']['id'];
	$form_action = ts_get_form_action();

	if( $entry_id && get_post_meta($entry_id, 'comfirmed', true)) {

		$status = get_post_status( $entry_id );

		if($status=='paid' || $status=='paidcheck'){
			$paid_amount = get_post_meta($entry_id, 'paid_amount', true);
			$grand_total = get_post_meta($entry_id, 'grand_total', true);
			$competition_fee = ts_get_total_competition_fee($entry_id, $entry_data);
			$paid_amount_competition = get_post_meta($entry_id, 'paid_amount_competition', true);
			$amount_credited =  get_post_meta($entry_id, 'amount_credited', true);
			if($paid_amount!=$grand_total) {
				?>
				<div class="form-container-2 t-center boxed-container">
					<?php
					if($paid_amount>$grand_total) {
						echo '<h1>Credit has been applied to your account.</h1>';
						do_action('registration_amount_credited',$entry_id,$amount_credited);
						?>
						<script type="text/javascript">
							setTimeout(function(){
								window.location.replace("<?php echo admin_url('admin.php?page=ts-my-entries'); ?>");
							}, 5000);
						</script>
					<?php
					} 
					else {
						require_once(TS_LIBRARIES .'config.php');

						if(isset($entry_data['discount_code']) && $entry_data['discount_code']!='') {
							$grand_total = ts_discounted_grand_total($grand_total, $entry_data['discount_code'], $entry_id);
						}
						$remaining_total = $grand_total - $paid_amount;
						$current_user = wp_get_current_user();
						?>
						<div class="studio-payment-container payment-form-container form-container-1">
							<div class="row">
								<div class="col-md-12 t-center">OR</div>
							</div>
							<div class="row">
								<div class="col-md-6 t-center stripe-payment-form-container">
									<form action="<?php echo $base_url .'&step='. $next_step .'&completed=1&remaining=1'; ?>" method="post" class="stripe-payment-form">
										<script src="https://checkout.stripe.com/checkout.js"
												class="stripe-button"
												data-key="<?php echo $stripe['publishable_key']; ?>"
												data-amount="<?php echo $remaining_total * 100; ?>"
												data-name="<?php echo get_bloginfo('name'); ?>"
												data-billing-address="true"
												data-email="<?php echo $current_user->user_email; ?>"
												data-description="Remaining payment for Entry #<?php echo $entry_id; ?>">
										</script>
									</form>
								</div>
								<div class="col-md-6 t-center mail-payment-form-container">
									<form name="studio-payment" id="studio-payment" class="studio-registration registration-form validate mail-payment-form" method="post" action="">
										<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
										<input type="hidden" name="eid" value="<?php echo $eid; ?>">
										<input type="hidden" name="tab" value="payment">
										<input type="hidden" name="action" value="<?php echo $form_action;?>">
										<input type="hidden" name="next_step" value="<?php echo $next_step; ?>">
										<input type="hidden" name="remaining" value="1">
										<label><input type="checkbox" name="" class="validate[required]"> Mail in Check</label><br />
										<input class="btn btn-green" type="submit" value="Submit Registration">
									</form>
								</div>
							</div>
							<p class="foot-note">Registration is not complete without full payment, <br>or until check is received in the mail.</p>
						</div>
						<?php
					}
					?>
				</div>
				<?php
			}
			else {
				?>
				<div class="form-container-2 t-center boxed-container">
					<h1>Your changes have been saved.</h1>
					<?php do_action('registration_recompleted',$entry_id); ?>
				</div>
				<script type="text/javascript">
					setTimeout(function(){
						window.location.replace("<?php echo admin_url('admin.php?page=ts-my-entries'); ?>");
					}, 5000);
				</script>
				<?php
			}
		}
		else {

			require_once(TS_LIBRARIES .'config.php');

			$grand_total = ts_grand_total($eid, $entry_data);

			if(isset($entry_data['discount_code']) && $entry_data['discount_code']!='') {
				$grand_total = ts_discounted_grand_total($grand_total, $entry_data['discount_code'], $entry_id);
			}
			$current_user = wp_get_current_user();
			?>
			<div class="studio-payment-container payment-form-container form-container-1">
				<div class="row">
					<div class="col-md-12 t-center">OR</div>
				</div>
				<div class="row">
					<div class="col-md-6 t-center stripe-payment-form-container">
						<form action="<?php echo $base_url .'&step='. $next_step .'&completed=1'; ?>" method="post" class="stripe-payment-form">
							<script src="https://checkout.stripe.com/checkout.js"
									class="stripe-button"
									data-key="<?php echo $stripe['publishable_key']; ?>"
									data-amount="<?php echo $grand_total * 100; ?>"
									data-name="<?php echo get_bloginfo('name'); ?>"
									data-billing-address="true"
									data-email="<?php echo $current_user->user_email; ?>"
									data-description="Payment for Entry #<?php echo $entry_id; ?>">
							</script>
						</form>
					</div>
					<div class="col-md-6 t-center mail-payment-form-container">
						<form name="studio-payment" id="studio-payment" class="studio-registration registration-form validate mail-payment-form" method="post" action="">
							<input type="hidden" name="entry_id" value="<?php echo $entry_id; ?>">
							<input type="hidden" name="eid" value="<?php echo $eid; ?>">
							<input type="hidden" name="tab" value="payment">
							<input type="hidden" name="action" value="<?php echo $form_action;?>">
							<input type="hidden" name="next_step" value="<?php echo $next_step; ?>">
							<label><input type="checkbox" name="" class="validate[required]"> Mail in Check</label><br />
							<input class="btn btn-green" type="submit" value="Submit Registration">
						</form>
					</div>
				</div>
				<p class="foot-note">Registration is not complete without full payment, <br>or until check is received in the mail.</p>
			</div>
			<?php
		}

	}
	else {
		?>
		<script type="text/javascript">
			window.location.replace("<?php echo $base_url .'&step='. $confirmation; ?>");
		</script>
		<?php
	}
}

function ts_get_results_html($entry_data, $entry_id, $eid, $prev_step, $next_step, $base_url, $steps) {

	$confirmation = $steps['confirmation']['id'];

	if( $entry_id ) {

		if($_POST){
			require_once(TS_LIBRARIES .'config.php');
			$charge_amount = $remaining_amount = 0;
			$remaining = false;
			$grand_total = ts_grand_total($eid, $entry_data);

			if(isset($entry_data['discount_code']) && $entry_data['discount_code']!='') {
				$grand_total = ts_discounted_grand_total($grand_total, $entry_data['discount_code'], $entry_id);
			}
			if( isset($entry_data['remaining_amount'] ) && ! empty($entry_data['remaining_amount']) ) {
				$remaining = true;
				$remaining_amount = absint($entry_data['remaining_amount']);
				$charge_amount = $remaining_amount;
			} else {
				$charge_amount = $grand_total;
			}

			$token  = $_POST['stripeToken'];
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;

			$customer = \Stripe\Customer::create(array(
				'email' => $current_user->user_email,
				'card'  => $token
			));

			try {
				$charge = \Stripe\Charge::create(array(
					'customer' => $customer->id,
					'amount'   => $charge_amount * 100,
					'currency' => 'usd'
				));
				do_action('registration_paid', $entry_id, $user_id, 'stripe_payment', $grand_total,	$remaining, $remaining_amount);
				do_action('registration_completed', $entry_id, $user_id, 'stripe_payment');
			}
			catch(\Stripe\Error\Card $e) {
				$body = $e->getJsonBody();
				$error  = $body['error'];
				do_action('registration_payment_failed', $entry_id, $error);
			}
		}

		if(get_post_meta($entry_id, 'completed', true)) {
			?>
			<div class="form-container-2 t-center boxed-container">
				<h1>Thank you for registering for Transcend - we can’t wait to dance with you! You will be receiving a confirmation email shortly.</h1>
			</div>
			<script type="text/javascript">
				setTimeout(function(){
					window.location.replace("<?php echo admin_url('admin.php?page=ts-my-entries'); ?>");
				}, 5000);
			</script>
			<?php
		}
		else if (get_post_meta($entry_id, 'payment_failed', true)==true) {
			$error_msg = get_post_meta($entry_id, 'payment_error_msg', true);
			?>
			<div class="form-container-2 t-center boxed-container">
				<h1><?php echo $error_msg; ?></h1>
			</div>
			<?php
		}
		else {
			?>
			<script type="text/javascript">
				window.location.replace("<?php echo $base_url .'&step='. $confirmation; ?>");
			</script>
			<?php
		}
	}
	else {
		?>
		<script type="text/javascript">
			window.location.replace("<?php echo $base_url .'&step='. $confirmation; ?>");
		</script>
		<?php
	}
}

function ts_pay_invoice_shortcode() {

	ob_start();
	?>
	<style type="text/css">
		body.ts-event-registration .fl-content-full.container {
			width: 100% !important;
			max-width: none !important;
		}
		body.ts-event-registration .fl-post-header {
			max-width: 1020px;
			margin-left: auto;
			margin-right: auto;
		}
		.ts-loginform-wrapper {
			max-width: 320px;
			margin: 0 auto;
		}
		.ts-loginform-wrapper .login-submit input {
			width: auto;
		}
	</style>
	<?php
	if ( ! is_user_logged_in() ) {
		$args = array(
			'echo' => true,
			'redirect' => get_permalink(ts_get_register_page_id()),
			'form_id' => 'loginform',
			'label_username' => __( '' ),
			'label_password' => __( '' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in' => __( 'Log In' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => false,
			'value_username' => NULL,
			'value_remember' => false
		);
		?>
		<div class="ts-loginform-wrapper tml tml-login text-center">
			<?php wp_login_form( $args ); ?>
			<div class="LoginLinks">
				<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="Forgot Password">Forgot Password?</a>&nbsp;&nbsp;
				<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>&forgotusername=1" title="Forgot Username">Forgot Username?</a>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('#user_login').attr('placeholder', 'Username');
			jQuery('#user_pass').attr('placeholder', 'Password');
		</script>
		<?php
	}
	else {
		if(! is_admin()){
			?>
			<a class="logout-url" href="<?php echo wp_logout_url( get_permalink(ts_get_register_page_id()) ); ?>">Logout</a>
			<?php
		}

		$user_id 	= get_current_user_id();
		$entry_id 	= ts_get_entry_id();
		$evid 		= ts_get_current_evid();

		$login_count = absint(get_user_meta($user_id, 'ts_login_count', true));

		if($login_count > 0 && ! is_admin()) {
			?>
			<script type="text/javascript">
				window.location.replace('<?php echo TS_ADMIN_DASHBOARD; ?>');
			</script>
			<?php
		}
		else {
			if( $evid && get_post_status($evid) && ( current_user_can('is_studio') || current_user_can('is_individual') ) ) {
				require_once( TS_INCLUDES . 'shortcodes/pay-invoice.php' );
				ts_pay_invoice_html( $entry_id, $evid, $user_id );
			} else {
				?>
				<script type="text/javascript">
					window.location.replace('<?php echo TS_ADMIN_DASHBOARD; ?>');
				</script>
				<?php
			}
		}
		?>
		<div id="popup-refresh" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
					<span class="sr-only">Loading...</span>
				</div>
			</div>
		</div>
		<?php
	}

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function ts_display_invoice_header_html( $entry_id, $invoice_id, $user_id ) {
	$title = get_the_title( $invoice_id );
	?>
	<div class="steps-btn-container clearfix">
		<ul class="clearfix">
			<li class="li-step">
						<span class="btn-step-outer grad-white">
							<span class="btn-step-inner grad-ligher-white">
								<span class="btn-step grad-light-gray">1</span>
							</span>
						</span>
				<span class="step-title-short"><?php echo $title; ?></span>
			</li>
		</ul>
	</div>
	<?php
}

function ts_display_invoice_content_html( $entry_id, $invoice_id, $user_id ) {
	$invoice_title = get_the_title( $invoice_id );
	$iv_amount = (int) get_post_meta($invoice_id,'invoice_amount',true);
	$note = get_post_meta($invoice_id,'invoice_note',true);

	$user_info = get_userdata( $user_id );
	$email = $user_info->user_email;
	require_once(TS_LIBRARIES .'config.php');
	if($_POST){
		$token  = $_POST['stripeToken'];

		try {
			$charge = \Stripe\Charge::create(array(
				'description' => $invoice_title.' for '.$email,
				'amount'   => $iv_amount * 100,
				'currency' => 'usd',
				'receipt_email' => $email,
				'source' => $token
			));
		}
		catch(\Stripe\Error\Card $e) {

		}
		do_action('invoice_paid', $entry_id, $user_id, 'stripe_payment', $iv_amount, $invoice_id);
		?>
		<div class="form-container-2 t-center boxed-container">
			<h4>Thank you! You will be receiving a receipt email shortly.</h4>
		</div>
		<script type="text/javascript">
			setTimeout(function(){
				window.location.replace("<?php echo admin_url('admin.php?page=ts-my-entries'); ?>");
			}, 5000);
		</script>
		<?php
	} else {
		?>
		<div class="invoice-payment-container payment-form-container form-container-1">
			<div class="row">
				<div class="col-md-12 t-center">
					<h6>Outstanding Amount: $<?php echo $iv_amount;?></h6>
					<p>Note: <?php echo $note;?></p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 t-center invoice-payment-form-container">
					<form action="" method="post" class="invoice-payment-form">
						<script src="https://checkout.stripe.com/checkout.js"
								class="stripe-button"
								data-key="<?php echo $stripe['publishable_key']; ?>"
								data-amount="<?php echo $iv_amount * 100; ?>"
								data-name="<?php echo get_bloginfo('name'); ?>"
								data-billing-address="true"
								data-email="<?php echo $email; ?>"
								data-description="Outstanding Payment for Entry #<?php echo $entry_id; ?>">
						</script>
					</form>
				</div>
			</div>
			<p class="foot-note">Registration is not completed until invoice paid.</p>
		</div>
		<?php
	}
}

function ts_workshop_schedules_shortcode() {

	ob_start();

	wp_enqueue_style('ts-shortcode-style');
	
	$args = array(
		'tax_query' => array(
			array(
				'taxonomy' => 'ts_schedules_type',
				'field'    => 'slug',
				'terms'    => 'workshop',
			),
		),
	);

	$schedules = ts_get_posts('ts_event', -1, $args);

	ts_display_workshop_schedules($schedules);

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function ts_competition_schedules_shortcode() {

	ob_start();

	wp_enqueue_style('ts-shortcode-style');
	
	$args = array(
		'tax_query' => array(
			array(
				'taxonomy' => 'ts_schedules_type',
				'field'    => 'slug',
				'terms'    => 'competition',
			),
		),
	);

	$schedules = ts_get_posts('ts_event', -1, $args);

	ts_display_competition_schedules($schedules);

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function ts_results_shortcode() {

	ob_start();

	wp_enqueue_style('ts-shortcode-style');
	wp_enqueue_script('ts-shortcode-script');
	
	ts_display_results_frontend();

	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}