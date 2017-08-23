<?php
function ts_entries_page() {
	?>
	<div id="entries-page" class="wrap">	
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper entries-wrapper">
			<table id="entries-list" class="ts-data-table" data-length="10" data-sort="asc">
                <thead>
                    <tr>
                        <th style="width:80px; text-align:center; display:none;">#</th>
                        <th>City</th>
                        <th style="text-align:center;">Type</th>
                        <th style="text-align:center;">Studio</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Payment</th>
                        <th style="width:50px; text-align:center;">View</th>
                        <th style="width:50px; text-align:center;">Delete</th>
                    </tr>
                </thead>
                <tbody>
					<?php
					$args = array(
						'post_status' => array('unpaid', 'paid', 'unpaidcheck', 'paidcheck', 'outstanding_amount'),
						'meta_key' => 'tour_date',
						'meta_type' => 'DATE',
						'orderby' => 'meta_value',
						'order' => 'ASC',
					);
					$entries = ts_get_posts('ts_entry', -1, $args); 
					if($entries) {
						$count=0;
						foreach ($entries as $entry) { 
							$count++;
							setup_postdata($entry);
		                    $entry_id 		= $entry->ID;
		                    $author 		= $entry->post_author;
		                    $post_status 	= $entry->post_status;
		                    $status_obj 	= get_post_status_object($post_status);
		                    $studio 		= get_field('studio', 'user_'. $author);
		                    $entry_type 	= wp_get_object_terms($entry_id, 'ts_entry_type');
		                    $workshop 		= get_post_meta($entry_id, 'workshop', true);
		                    $tour_city 		= $workshop['tour_city'];
		                    //$tour_date 		= get_post_meta($entry_id, 'tour_date', true);
		                	?>
		                    <tr id="item-<?php echo $entry_id; ?>">
		                    	<td style="text-align:center; display:none;"><?php echo $count; ?></td>
		                    	<td><?php echo get_the_title($tour_city); ?></td>
		                    	<td style="text-align:center;"><?php echo $entry_type[0]->name; ?></td>
		                    	<td style="text-align:center;"><?php echo $studio; ?></td>
		                    	<td style="text-align:center;"><?php echo $status_obj->label; ?></td>
		                        <td style="text-align:center;">
		                        	<?php 
		                        	if($post_status=='unpaidcheck') {
			                        	echo '<a title="mark as paid" href="javascript:void(0);" class="btn-markpaid" data-id="'. $entry_id .'">Mark as Paid</a>';
		                        	}
		                        	else if($post_status=='paid') { 
										echo 'Credit Card (Stripe)';
									}	
		                        	else if($post_status=='paidcheck') { 
										echo 'Mail in Check';
		                        	}
		                        	?>
		                        </td>
		                        <td style="text-align:center;"><a title="edit" href="<?php echo admin_url('admin.php?page=ts-view-entry&entry_id='. $entry_id); ?>">View</a></td>
		                        <td style="text-align:center;"><a title="delete" href="javascript:void(0);" class="btn-delete" data-id="<?php echo $entry_id; ?>" data-type="post">Delete</a></td>
		                    </tr>
		                <?php
		                }
		                ?>
			            <?php
		            }else{
		            	echo '<tr><td colspan="7">No Entries Found</td></tr>';
		            }
		            ?>
                </tbody>
			</table>
		</div>
	</div>
	<?php
}

function ts_view_entry_page() {

	$entry_id 		= $_GET['entry_id'];
	$profile 		= get_post_meta($entry_id, 'profile', true);
	$workshop 		= get_post_meta($entry_id, 'workshop', true);		
	$competition 	= get_post_meta($entry_id, 'competition', true);
	$grand_total 	= get_post_meta($entry_id, 'grand_total', true);
	$discount_code 	= get_post_meta($entry_id, 'discount_code', true);

	$entry 			= get_post($entry_id);
	$user_id 		= $entry->post_author;
    $post_status 	= $entry->post_status;
    $status_obj 	= get_post_status_object($post_status);

	$user_meta 		= get_userdata($user_id);
	$user_roles 	= $user_meta->roles;
	$email 			= $user_meta->user_email;

	$studio 	= get_field('studio', 'user_'. $user_id); 
	$director 	= get_field('director', 'user_'. $user_id); 
	$address 	= get_field('address', 'user_'. $user_id); 
	$city 		= get_field('city', 'user_'. $user_id); 
	$state 		= get_field('state', 'user_'. $user_id); 
	$zipcode 	= get_field('zipcode', 'user_'. $user_id); 
	$country 	= get_field('country', 'user_'. $user_id); 
	$phone 		= get_field('phone', 'user_'. $user_id); 
	$cell 		= get_field('cell', 'user_'. $user_id); 
	$contact 	= get_field('contact', 'user_'. $user_id); 

	$tour_city 			= get_the_title($workshop['tour_city']);
	$free_teacher_ids 	= ts_get_free_teacher_ids($entry_id);
	$participants 		= ts_check_value($workshop, 'participants');
	$observer 			= ts_check_value($workshop, 'observers');
	$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');
	?>
	<div id="view-entry-page" class="wrap">	
		<h1 class="admin-page-title"><?php echo $tour_city; ?></h1>
		<div class="ts-admin-wrapper">
			<div class="row">
				<div class="entry-status col-md-12"><strong>Status:</strong> <?php echo $status_obj->label; ?></div>
			</div>
			<?php if(in_array('studio', $user_roles)) { ?>
				<h2 class="admin-sub-title">Profile</h2>
				<div class="" style="max-width:640px;">
					<!-- <div class="row table-head">
						<div class="col-md-12"><strong>Profile</strong></div>
					</div> -->
					<div class="">
						<div class="row">
							<div class="col-md-4"> 
								<strong>Studio Name:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $studio; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Director's Name:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $director; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Address:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $address; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>City:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $city; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>State:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $state; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Zip Code:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $zipcode; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Country:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $country; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Studio Phone Number:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $phone; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Email:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $email; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Cell:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $cell; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4"> 
								<strong>Studio Contact Name:</strong>
							</div>
							<div class="col-md-8"> 
								<?php echo $contact; ?>
							</div>
						</div>
					</div>	
				</div>
			<?php 
			} 
			if(! empty($participants) ){ ?>
				<h2 class="admin-sub-title">Participants</h2>
				<div class="table-container">
					<div class="row table-head">
						<div class="col-md-2"><strong>Name</strong></div> 
						<div class="col-md-2 t-center"><strong>Age Division</strong></div> 
						<div class="col-md-3 t-center"><strong>Discount/Scholarship</strong></div> 
						<div class="col-md-2 t-center"><strong>Full Weekend/1-Day</strong></div> 
						<div class="col-md-2 t-center"><strong>Fee</strong></div> 
					</div>
					<div class="roster-container table-body participants-list">
						<?php
						$args = array(
							'orderby'          => 'meta_value_num',
							'order'            => 'ASC',
							'include'          => array_keys($participants),
							'meta_key'         => 'age_cat_order',
						);


						if(in_array('studio', $user_roles)) {
							$post_type = 'ts_studio_roster';
						}
						else if(in_array('individual', $user_roles)){
							$post_type = 'ts_sibling';
						}	
						$roster_posts = ts_get_user_posts($post_type, -1, $user_id, $args);

						foreach ($roster_posts as $rp) {
							$rid 			= $rp->ID;
							$name 			= get_the_title($rid);
							$age_div 		= wp_get_object_terms($rid, 'ts_agediv');
							$discount_id 	= $participants[$rid]['discount'];
							$duration_id 	= $participants[$rid]['duration'];
							$base_fee 		= ts_get_workshop_fee($rid, $duration_id, $entry_id, $workshop['tour_city']);
							$discounted_fee = ts_get_discounted_workshop_fee($base_fee, $discount_id);
							$fee_preview 	= in_array($rid, $free_teacher_ids) ? 'Free' : '$'. number_format($discounted_fee, 2);
							?>
							<div class="row participant" id="item-<?php echo $rid; ?>" data-id="<?php echo $rid; ?>">
								<div class="col-md-2">
									<span class="participant-name-preview"><?php echo $name; ?></span>
								</div> 
								<div class="col-md-2 t-center">
									<?php echo $age_div[0]->name; ?>
								</div> 
								<div class="col-md-3 t-center">
									<?php
									$discounts 	= ts_get_discounts();
									$discount 	= ts_get_array_index($discounts, $discount_id);
									echo $discount['title'];
									?>
								</div> 
								<div class="col-md-2 t-center <?php echo $disabled_class; ?>-container">
									<?php
									$durations 	= ts_get_workshop_durations();
									$duration 	= ts_get_array_index($durations, $duration_id);
									echo $duration['title'];
									?>
								</div> 
								<div class="col-md-2 t-center">
									<span id="fee-preview-<?php echo $rid; ?>"><?php echo $fee_preview; ?></span>
								</div> 
							</div>
							<?php
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
										<span class="observer-name-preview"><?php echo $value['name']; ?></span>
									</div> 
									<div class="col-md-2 t-center">N/A</div> 
									<div class="col-md-3 t-center">N/A</div> 
									<div class="col-md-2 t-center">N/A</div> 
									<div class="col-md-2 t-center">
										<span class="observer-fee-preview">$<?php echo number_format($fee, 2); ?></span>
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
										<span class="observer-name-preview"><?php echo $value['name']; ?></span>
									</div> 
									<div class="col-md-2 t-center">N/A</div> 
									<div class="col-md-3 t-center">N/A</div> 
									<div class="col-md-2 t-center">N/A</div> 
									<div class="col-md-2 t-center">
										<span class="observer-fee-preview">$<?php echo number_format($value['fee'], 2); ?></span>
									</div> 
								</div>
								<?php
							}
						}
						?>	
					</div>					
				</div>
			<?php
			} 
			$routines = $competition['routines'];
			$routine_ids = ! empty($routines) ? array_keys($routines) : 0;
			$args = array(
				'order'            => 'ASC',
				'include'          => $routine_ids,
			);
			$routine_posts = ts_get_user_posts('ts_routine', -1, $user_id, $args);

			if($routine_ids!==0 && $routine_posts) {
				?>
				<h2 class="admin-sub-title">Routines</h2>
				<div class="table-container">	
					<div class="row table-head">
						<div class="col-sm-2"><strong>Routine Name</strong></div> 
						<div class="col-sm-2 t-center"><strong>Dancers</strong></div> 
						<div class="col-sm-1 t-center"><strong>Age Division</strong></div> 
						<div class="col-sm-1 t-center"><strong>Category</strong></div> 
						<div class="col-sm-1 t-center"><strong>Genre</strong></div> 
						<div class="col-sm-1 t-center"><strong>Enter / Exit with / without music</strong></div> 
						<div class="col-sm-1 t-center"><strong>Are there Props w/ set up / clean up?</strong></div> 
						<div class="col-sm-2 t-center"><strong>Music</strong></div> 
						<div class="col-sm-1 t-center"><strong>Fee</strong></div> 
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

							$countDancers = count($dancersArray);
							$fee 	 	  = ts_get_routine_fee($countDancers);
							?>

							<div class="row" id="item-<?php echo $rpid; ?>" data-id="<?php echo $rpid; ?>">
								<div class="col-sm-2">
									<?php echo $name; ?>
								</div> 
								<div class="col-sm-2 t-center">
									<span class="routine-dancers-preview" id="routine-dancers-preview-<?php echo $rpid; ?>">
										<?php
										$ids = $dancersArray;
										$count_d = count($ids);
										$age_total = 0;
										if(! empty($ids)){
											foreach ($ids as $d) {
												if(ts_post_exists_by_id($d)){
													echo get_the_title($d) .', ';
													$birth_date = get_post_meta($d, 'birth_date', true);
													$age = ts_get_the_age($birth_date);
													$age_total = $age_total + $age;
												}
											}
											$age_ave = round($age_total / $count_d);
											$age_div_name = ts_get_routine_agediv_name($age_ave);
										}
										?>
									</span>
								</div>
								<div class="col-sm-1 t-center">
									<span class="routine-agediv-preview" id="routine-agediv-preview-<?php echo $rpid; ?>"><?php echo $age_div_name; ?></span>
								</div> 
								<div class="col-sm-1 t-center">
									<span class="routine-cat-preview" id="routine-cat-preview-<?php echo $rpid; ?>">
										<?php
										$categories = ts_get_competition_categories();
										echo $categories[$cat]['title'];
										?>
									</span>
								</div> 
								<div class="col-sm-1 t-center">
									<?php
									$genres = ts_get_routine_genres();
									$thisgenre = ts_get_array_index($genres, $genre);
									echo $thisgenre['title'];
									?>
								</div> 
								<div class="col-sm-1 t-center">
									<?php
									$flows = ts_get_routine_flows();
									$thisflow = ts_get_array_index($flows, $flow);
									echo $thisflow['title'];
									?>
								</div> 
								<div class="col-sm-1 t-center">
									<?php
									$props = ts_get_routine_props();
									$thisprop = ts_get_array_index($props, $prop);
									echo $thisprop['title'];
									?>
								</div> 
								<div class="col-sm-2 t-center routine-music-container">
									<input class="routine-music" id="routine-music-<?php echo $rpid; ?>" name="routinecurr[<?php echo $rpid; ?>][music]" value="<?php echo $music; ?>" type="hidden">
									<?php 
									if($music){
										$music_filename = basename(get_attached_file($music));
										$music_url = wp_get_attachment_url($music);
										echo '
										<div><small><a href="'. $music_url .'" target="_blank">'. $music_filename .'</a></small></div>';
									} 
									?>
								</div> 
								<div class="col-sm-1 t-center">
									<span class="routine-fee-preview" id="routine-fee-preview-<?php echo $rpid; ?>">$<?php echo number_format($fee, 2); ?></span>
								</div> 
							</div>
							<?php
						}
						?>
					</div>	
				</div>	
				<?php
			} ?>
			<h2 class="admin-sub-title">Summary</h2>
			<div class="table-container entry-summary">	
				<div class="row table-head">
					<div class="col-sm-12 t-center"><strong><?php echo $tour_city; ?></strong></div> 
				</div>
				<div class="table-body">	
					<div class="row">	
						<div class="col-sm-12">
							<?php echo ts_display_entry_details($entry_id, $user_id); ?>
						</div>
					</div>
				</div>
			</div>		
		</div>
	</div>
	<?php
}

function ts_my_entries_page() {
	?>
	<div id="my-entries-page" class="wrap">	
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<h2 class="admin-sub-title">New Registrations</h2>
		<div class="ts-admin-wrapper">
			<form name="new-registration" id="new-registration" class="validate new-registration-form" method="post" action="">
				<span>Select City</span>
				<select name="id">
					<?php
					$args = array(
						'meta_key' => 'date_from',
						'meta_type' => 'DATE',
						'orderby' => 'meta_value',
						'order' => 'ASC',
					);
					$tour_stops = ts_get_posts('ts_tour', -1, $args);
					if($tour_stops) {
						$count=0;
						foreach ($tour_stops as $stop) {
							$count++; 
							setup_postdata($stop);
		                    $stop_id 	= $stop->ID;
		                    $title 		= get_the_title($stop_id);
		                    $date_from 	= get_post_meta($stop_id, 'date_from', true);
		                    $date_to 	= get_post_meta($stop_id, 'date_to', true);
		                    $disabled 	= $date_from && ts_get_days_before_date($date_from) <= 0 ? 'disabled' : '';
		                	?>
		                    <option value="<?php echo $stop_id; ?>" <?php echo $disabled; ?>><?php echo $title; ?></option>
		                <?php
		                }
		            }
		            ?>
		        </select>
		        <button type="submit" class="btn btn-green btn-new-registration">Register</button>
			</form>
		</div>  
		<h2 class="admin-sub-title">Current Registrations</h2>
		<div class="ts-admin-wrapper entries-wrapper">
			<table id="entries-list" class="ts-data-table" data-length="10" data-sort="asc">
                <thead>
                    <tr>
                        <th style="width:80px; text-align:center; display:none;">#</th>
                        <th>City</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;">Edit</th>
                        <th style="text-align:center;">Delete</th>
                    </tr>
                </thead>
                <tbody>
					<?php
					$args = array(
						'post_status' => array('unpaid', 'paid', 'unpaidcheck', 'paidcheck'),
						'meta_key' => 'tour_date',
						'meta_type' => 'DATE',
						'orderby' => 'meta_value',
						'order' => 'ASC',
					);
					$my_entries = ts_get_user_posts('ts_entry', -1, false, $args);
					if($my_entries) {
						?>
		                <?php
						foreach ($my_entries as $entry) { 
							setup_postdata($entry);
		                    $entry_id = $entry->ID;
		                    $workshop = get_post_meta($entry_id, 'workshop', true);
		                    $status = get_post_status_object($entry->post_status);
		                    $saved = get_post_meta($entry_id, 'save_for_later', true);
		                	$step = $saved ? $saved : 1;
		                	?>
		                    <tr id="item-<?php echo $entry_id; ?>">
		                    	<td style="text-align:center; display:none;"><?php echo $count; ?></td>
		                    	<td><?php echo get_the_title($workshop['tour_city']); ?></td>
		                    	<td style="text-align:center;"><?php echo $status->label; ?></td>
		                        <td style="text-align:center;"><a title="edit" href="javascript:void(0);" class="btn btn-blue btn-edit-entry" data-eid="<?php echo $entry_id; ?>" data-url="<?php echo admin_url('admin.php?page=ts-edit-entry&action=edit&step='. $step .'&id='. $entry_id); ?>"><small>Edit</small></a></td>
		                        <td style="text-align:center;"><a title="delete" href="javascript:void(0);" class="btn btn-red btn-delete" data-id="<?php echo $entry_id; ?>" data-type="post"><small>Delete</small></a></td>
		                    </tr>
		                <?php
		                }
		                ?>
		            <?php
		            }else{
		            	echo '<tr><td colspan="4">No Entries Found</td></tr>';
		            }
		            ?>
                </tbody>
			</table>
		</div>
		<h2 class="admin-sub-title">Outstanding Invoices</h2>
		<div class="ts-admin-wrapper entries-wrapper">
			<table id="invoices-list" class="ts-data-table" data-length="10" data-sort="asc">
				<thead>
				<tr>
					<th style="width:80px; text-align:center; display:none;">#</th>
					<th>City</th>
					<th style="text-align:center;">Note</th>
					<th style="text-align:center;">Amount</th>
					<th style="text-align:center;">Pay Now</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'post_status' => array('outstanding_amount'),
					'meta_key' => 'tour_date',
					'meta_type' => 'DATE',
					'orderby' => 'meta_value',
					'order' => 'ASC',
				);
				$my_entries = ts_get_user_posts('ts_entry', -1, false, $args);
				if($my_entries) {
					?>
					<?php
					foreach ($my_entries as $entry) {
						setup_postdata($entry);
						$entry_id = $entry->ID;
						$workshop = get_post_meta($entry_id, 'workshop', true);
						$invoice_note = get_post_meta($entry_id, 'ts_entry_invoice_note', true);
						$invoice_amount = get_post_meta($entry_id, 'ts_entry_invoice_amount', true);
						$invoice_id  = get_post_meta($entry_id, 'invoice_id', true);
						?>
						<tr id="item-<?php echo $entry_id; ?>">
							<td style="text-align:center; display:none;"><?php echo $count; ?></td>
							<td><?php echo get_the_title($workshop['tour_city']); ?></td>
							<td style="text-align:center;"><?php echo $invoice_note; ?></td>
							<td style="text-align:center;"><?php echo '$'. $invoice_amount;?></td>
							<td style="text-align:center;"><a title="payinvoice" href="javascript:void(0);" class="btn btn-blue btn-pay-invoice" data-ivid="<?php echo $invoice_id; ?>" data-eid="<?php echo $entry_id; ?>" data-url="<?php echo admin_url('admin.php?page=ts-entry-pay-invoice&action=pay_invoice&id='. $entry_id.'&evid='.$invoice_id); ?>"><small>Pay Now</small></a></td>
						</tr>
						<?php
					}
					?>
					<?php
				}else{
					echo '<tr><td colspan="4">No Invoices Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
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

function ts_post_entry_page() {
	?>
	<div id="post-entry-page" class="wrap">	
		<?php echo do_shortcode('[ts-event-registration-form]'); ?>
	</div>
	<?php
}

function ts_vouchers_page() {
	?>
	<div id="vouchers-page" class="wrap">	
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?><a class="btn btn-blue btn-addvoucher" href="javascript:void(0);">Add New</a></h1>
		<div class="ts-admin-wrapper vouchers-wrapper">
			<table id="vouchers-list" class="ts-data-table" data-length="10" data-sort="asc">
                <thead>
                    <tr>
                        <th style="text-align:center;">Code</th>
                        <th style="text-align:center;">Discount</th>
                        <th style="text-align:center;">Workshop</th>
                        <th style="text-align:center;">Competition</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
					<?php
					$vouchers = ts_get_posts('ts_coupon'); 
					if($vouchers) {
						foreach ($vouchers as $voucher) { 
							setup_postdata($voucher);
		                    $voucher_id 			= $voucher->ID;
		                    $voucher_code 			= $voucher->post_title;
		                    $voucher_discount 		= get_post_meta($voucher_id, 'discount', true);
		                    $voucher_workshop 		= get_post_meta($voucher_id, 'workshop', true);
		                    $voucher_competition 	= get_post_meta($voucher_id, 'competition', true);
		                	?>
		                    <tr id="item-<?php echo $voucher_id; ?>">
		                        <td style="text-align:center;"><?php echo $voucher_code; ?></td>
		                        <td style="text-align:center;"><?php echo $voucher_discount; ?></td>
		                        <td style="text-align:center;"><?php echo $voucher_workshop==1 ? 'Enabled' : 'Disabled'; ?></td>
		                        <td style="text-align:center;"><?php echo $voucher_competition==1 ? 'Enabled' : 'Disabled'; ?></td>
		                        <td style="text-align:center;">
		                        	<a title="edit" href="javascript:void(0);" 
		                        		class="btn btn-blue btn-editvoucher" 
		                        		data-id="<?php echo $voucher_id; ?>" 
		                        		data-code="<?php echo $voucher_code; ?>"
		                        		data-discount="<?php echo $voucher_discount; ?>"
		                        		data-workshop="<?php echo $voucher_workshop; ?>"
		                        		data-competition="<?php echo $voucher_competition; ?>"
		                        		><small>Edit</small></a>
		                        	<a title="delete" href="javascript:void(0);" 
		                        		class="btn btn-red btn-delete" 
		                        		data-id="<?php echo $voucher_id; ?>" 
		                        		data-type="post"
		                        		><small>Delete</small></a>
		                        </td>
		                    </tr>
		                <?php
		                }
		            }else{
		            	echo '<tr><td colspan="5">No Vouchers Found</td></tr>';
		            }
		            ?>
                </tbody>
			</table>
		</div>
	</div>
	<div id="popup-save-voucher" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Add Voucher</h4>
				</div>
				<div class="modal-body">
					<form method="post" action="" id="form-save-voucher" name="form-save-voucher" >
						<input type="hidden" name="voucher-id" id="voucher-id" value="" />
						<p>Code <br /><input type="text" name="voucher-code" id="voucher-code" value="" /></p>
						<p>Discount <br /><input type="text" name="voucher-discount" id="voucher-discount" value="" /></p>
						<p><label><input type="checkbox" name="voucher-workshop" id="voucher-workshop" value="1" /> Apply to Workshop</label></p>
						<p><label><input type="checkbox" name="voucher-competition" id="voucher-competition" value="1" /> Apply to Competition</label></p>
						<input type="submit" value="Save" class="btn btn-blue">
					</form>	
				</div>
			</div>
		</div>
	</div>	
	<?php	
}

function ts_post_pay_invoice_page() {
	?>
	<div id="post-pay-invoice-page" class="wrap">
		<?php echo do_shortcode('[ts-pay-invoice-form]'); ?>
	</div>
	<?php
}
