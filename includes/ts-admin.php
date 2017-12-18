<?php
function ts_entries_page() {
	?>
	<div id="entries-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper entries-wrapper">
			<table id="entries-list" class="ts-data-table" data-length="25" data-filter="true" data-colfilter="[0,1,4,2]" data-exporttitle="All Registrations" data-exportcol="0,1,2,3" data-dom="fBrt<'table-footer clearfix'pl>" data-trimtrigger="0" data-trimtarget="2" data-titleswitch="1">
				<thead>
				<tr>
					<th>City</th>
					<th style="text-align:center;">Type</th>
					<th style="text-align:center;">Studio</th>
					<th style="text-align:center;">Name</th>
					<th style="text-align:center;">Status</th>
					<th style="text-align:center;">Date Completed</th>
					<th style="text-align:center;">Payment</th>
					<th style="width:50px; text-align:center;">View</th>
					<th style="width:50px; text-align:center;">Delete</th>
					<!-- <th style="width:50px; text-align:center;">Download</th> -->
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
						$date_paid 		= get_post_meta($entry_id, 'date_paid', true);
						$date_paid 		= $date_paid ? date_format(date_create($date_paid),'m/d/Y') : '';
						$user_meta 		= get_userdata($author);
						$email 			= $user_meta->user_email;
						$user_roles 	= $user_meta->roles;
						$name 			= '';
						if(in_array('studio', $user_roles)) {
							$name = get_field('director', 'user_'. $author);
						}
						else if(in_array('individual', $user_roles)){
							$name = get_field('name', 'user_'. $author);
						}
						?>
						<tr id="item-<?php echo $entry_id; ?>" data-author="<?php echo $author; ?>">
							<td id="tour-<?php echo $tour_city; ?>"><?php echo get_the_title($tour_city); ?></td>
							<td style="text-align:center;"><?php echo ucwords($user_roles[0]); ?></td>
							<td style="text-align:center;"><?php echo $studio; ?></td>
							<td style="text-align:center;"><?php echo $name;?></td>
							<td style="text-align:center;"><?php echo $status_obj->label; ?></td>
							<td style="text-align:center;" data-datecreated="<?php echo get_the_date('m/d/Y', $entry_id); ?>"><?php echo $date_paid; ?></td>
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
							<!-- <td style="text-align:center;"><a title="downloadallmusic" href="javascript:void(0);" class="btn-downloadallmusic" data-id="<?php echo $entry_id; ?>">Download All Music</a></td> -->
						</tr>
						<?php
					}
					?>
					<?php
				}else{
					echo '<tr><td colspan="9">No Entries Found</td></tr>';
				}
				?>
				</tbody>
				<tfoot>
				<tr>
					<th>All Cities</th>
					<th>All Types</th>
					<th data-sort="true">All Studios</th>
					<th class="hidden"></th>
					<th>Status</th>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<!-- <th class="hidden"></th> -->
				</tr>
				</tfoot>
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

	$tour_id 			= $workshop['tour_city'];
	$tour_city 			= get_the_title($tour_id);
	$free_teacher_ids 	= ts_get_free_teacher_ids($entry_id);
	$participants 		= ts_check_value($workshop, 'participants');
	$observer 			= ts_check_value($workshop, 'observers');
	$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');
	$date_paid 			= get_post_meta($entry_id, 'date_paid', true);
	$tour_date 			= get_post_meta($tour_id, 'date_from', true);
	$force_early 		= $date_paid && ts_get_days_before_date($tour_date, $date_paid) > 30 ? true : false; 
	?>
	<div id="view-entry-page" class="wrap">
		<h1 class="admin-page-title"><?php echo $tour_city; ?></h1>
		<div class="ts-admin-wrapper">
			<div class="row">
				<div class="entry-status col-md-12"><strong>Status:</strong> <?php echo $status_obj->label; ?></div>
			</div>
			<?php 
			if(in_array('studio', $user_roles)) { 
				?>
				<h2 class="admin-sub-title">Profile</h2>
				<div class="" style="max-width:640px;">
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
			else if(in_array('individual', $user_roles)) { 
				$name 		= get_field('name', 'user_'. $user_id);
				$birth_date = get_field('birth_date', 'user_'. $user_id);
				$parent 	= get_field('parent', 'user_'. $user_id);
				?>
				<h2 class="admin-sub-title">Profile</h2>
				<div class="" style="max-width:640px;">
					<div class="">
						<div class="row">
							<div class="col-md-4">
								<strong>Name:</strong>
							</div>
							<div class="col-md-8">
								<?php echo $name; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<strong>Date of Birth:</strong>
							</div>
							<div class="col-md-8">
								<?php echo $birth_date; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<strong>Parent's Name:</strong>
							</div>
							<div class="col-md-8">
								<?php echo $parent; ?>
							</div>
						</div>
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
							$base_fee 		= ts_get_workshop_fee($rid, $duration_id, $entry_id, $tour_id, $force_early);
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

							$dancers_count = get_post_meta($rpid, 'dancers_count', true);
							$dancers_count_edited = get_post_meta($rpid, 'dancers_count_edited', true);
							$countDancers = $dancers_count_edited ? $dancers_count_edited : $dancers_count;
							//$countDancers = count($dancersArray); 
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
			} 
			?>
			<h2 class="admin-sub-title">Summary</h2>
			<div class="table-container entry-summary">
				<div class="row table-head">
					<div class="col-sm-12 t-center"><strong><?php echo $tour_city; ?></strong></div>
				</div>
				<div class="table-body">
					<div class="row">
						<div class="col-sm-12">
							<?php echo ts_display_entry_details($entry_id, $user_id, true, true); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function ts_workshopentries_page() {
	$tour_id = ts_get_param('tour');
	?>
	<div id="entries-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?> <?php ts_select_tour_city(admin_url('admin.php') .'?page=ts-workshop-entries', $tour_id); ?></h1>
		<div class="ts-admin-wrapper entries-wrapper">
			<?php if($tour_id) { ?>	
			<table style="width: 100%;" id="entries-list" class="ts-data-table" data-length="25"  data-filter="true" data-colfilter="[5,2]" data-exporttitle="Workshop Registrations" data-exportcol="1,2,3,4,5" data-dom="fBrt<'table-footer clearfix'pl>">
				<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th style="text-align:center;">Age Division</th>
					<th style="text-align:center;">Type</th>
					<th style="text-align:center;">Studio</th>
					<th style="text-align:center;">City</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'meta_key' => 'tour_city',
					'meta_value' => $tour_id,
					'post_status' => array('paid', 'paidcheck', 'outstanding_amount'),
				);
				$entries = ts_get_posts('ts_entry', -1, $args);
				if($entries) {
					$agedivname = '';
					$roster_posts = array();
					$participantsArray = array();
					foreach ($entries as $entry) {
						setup_postdata($entry);
						$entry_id 		= $entry->ID;
						$workshop 		= get_post_meta($entry_id, 'workshop', true);
						$participants 	= $workshop['participants'];
						$tour_city 		= $workshop['tour_city'];
						$city 			= get_post_meta($tour_city, 'city', true);
						$user_id 		= $entry->post_author;
						$user_meta 		= get_userdata($user_id);
						$user_roles 	= $user_meta->roles;
						$studio 		= get_field('studio', 'user_'. $user_id);
						if(! empty($participants)) {
							$participants_ids  = array_keys($participants);
							$participantsArray = array_merge($participants_ids, $participantsArray);
						}	
					}
					if(! empty($participantsArray) ){
						$participantsArray = ts_trim_duplicate($participantsArray);
						$args = array(
							'meta_key' => 'age_cat_order',
							'orderby' => 'meta_value_num',
							'order' => 'ASC',								
							'include' => $participantsArray,
						);
						$roster_posts = ts_get_posts(array('ts_studio_roster', 'ts_sibling'), -1, $args);

						if(! empty($roster_posts)) {
							$count=0;
							foreach ($roster_posts as $rp) {
								$count++;
								$rid 			= $rp->ID;
								$age_div 		= wp_get_object_terms($rid, 'ts_agediv');
								$agediv_name 	= $age_div[0]->name;
								$name 			= get_the_title($rid);
								?>
								<tr id="item-<?php echo $entry_id; ?>">
									<td><?php echo $count; ?></td>
									<td><?php echo $name; ?></td>
									<td style="text-align:center;"><?php echo $agediv_name; ?></td>
									<td style="text-align:center;"><?php echo ucwords($user_roles[0]); ?></td>
									<td style="text-align:center;"><?php echo $studio; ?></td>
									<td style="text-align:center;"><?php echo $city; ?></td>
								</tr>
								<?php
							}
						}	
					}
				}
				else{
					echo '<tr><td colspan="6" align="center">No Workshop Participants Found</td></tr>';
				}
				?>	
				</tbody>
				<tfoot>
				<tr>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<th>All Age Divisions</th>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<th class="hidden">All Cities</th>
				</tr>
				</tfoot>
			</table>
			<?php } ?>
		</div>
	</div>
	<?php
}

function ts_competitionentries_page() {
	$tour_id = ts_get_param('tour');
	?>
	<div id="entries-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?> <?php ts_select_tour_city(admin_url('admin.php') .'?page=ts-competition-entries', $tour_id); ?></h1>
		<div class="ts-admin-wrapper entries-wrapper">
			<?php if($tour_id) { ?>	
			<table id="entries-list" class="ts-data-table" data-length="25" data-filter="true" data-colfilter="[5,3,4]" data-exporttitle="Competition Registrations" data-exportcol="1,2,3,4,5" data-dom="fBrt<'table-footer clearfix'pl>">
				<thead>
				<tr>
					<th>#</th>
					<th style="width: 200px;">Routine Name</th>
					<th>Dancers</th>
					<th style="text-align:center; width: 180px;">Age Division</th>
					<th style="text-align:center; width: 90px;">Category</th>
					<th style="text-align:center; width: 160px;">City</th>
					<!-- <th style="text-align:center;">Action</th>
					<th style="text-align:center;">Edit</th> -->
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'meta_key' => 'tour_city',
					'meta_value' => $tour_id,
					'post_status' => array('paid', 'paidcheck', 'outstanding_amount'),
				);
				$entries = ts_get_posts('ts_entry', -1, $args);

				if($entries) {
					$routinesArray = array();
					foreach ($entries as $entry) {
						setup_postdata($entry);
						$entry_id 		= $entry->ID;
						$competition 	= get_post_meta($entry_id, 'competition', true);
						$routines 		= $competition['routines'];
						$workshop 		= get_post_meta($entry_id, 'workshop', true);
						$city 			= get_post_meta($workshop['tour_city'], 'city', true);
						if(! empty($routines)){
							$routine_ids  = array_keys($routines);
							$routinesArray = array_merge($routine_ids, $routinesArray);
						}	
					}
					if(! empty($routinesArray) ){
						$routine_ids = array_keys($routines);
						$args = array(
						    'meta_query' => array(
						        'relation' => 'AND',
						        'agedivorder' => array(
						            'key' => 'agediv_order',
						            'compare' => 'EXISTS',
						        ),
						        'catorder' => array(
						            'key' => 'cat_order',
						            'compare' => 'EXISTS',
						        ), 
						    ),
						    'orderby' => array( 
						        'agedivorder' => 'ASC',
						        'catorder' => 'ASC',
						    ),							
							/*'meta_key' => 'agediv_order',
							'orderby' => 'meta_value_num',
							'order' => 'ASC',*/								
							'include' => $routinesArray,
						);
						$routine_posts = ts_get_posts('ts_routine', -1, $args);
						$count=0;
						foreach ($routine_posts as $rp) {
							$count++;
							$rpid 		= $rp->ID;
							$name 	 	= get_the_title($rpid);
							$cat 	 	= get_post_meta($rpid, 'cat', true);
							$categories = ts_get_competition_categories();
							$cat_name 	= $categories[$cat]['title'];
							$dancers 	= get_post_meta($rpid, 'dancers', true);
							$dancers_array  = is_array($dancers) ? $dancers : explode(",", $dancers);
							$dancers_string = '';
							$ids = $dancers_array;
							$count_d = count($ids);
							$age_total = 0;
							if(! empty($ids)){
								foreach ($ids as $d) {
									if(ts_post_exists_by_id($d)){
										$dancers_string.= get_the_title($d) .', ';
										$birth_date = get_post_meta($d, 'birth_date', true);
										$age = ts_get_the_age($birth_date);
										$age_total = $age_total + $age;
									}
								}
								$age_ave = round($age_total / $count_d);
								$agediv_name = ts_get_routine_agediv_name($age_ave);
							}
							$musicid = (int)$routines[$rpid]['music'];
							$musicurl = $musicid ? wp_get_attachment_url($musicid) : false;
							$musicoutput = $musicurl ? '<a download class="btn btn-blue btn-downloadmusic" href="'.$musicurl.'">Download Music</a>' : 'No Music';
							$musictitle = $musicid ? get_the_title($musicid) : false;
							$agediv = get_term_by('name', $agediv_name, 'ts_agediv');
							//$num = get_term_meta($agediv->term_id, 'div_order', true);
							?>
							<tr id="item-<?php echo $entry_id; ?>">
								<td><?php echo $count; ?></td>
								<td><?php echo $name; ?></td>
								<td><?php echo $dancers_string; ?></td>
								<td style="text-align:center; width: 180px;"><?php echo $agediv_name; ?></td>
								<td style="text-align:center;"><?php echo $cat_name; ?></td>
								<td style="text-align:center;"><?php echo $city; ?></td>
								<!-- <td style="text-align:center;"><?php echo $musicoutput;?></td>
								<td style="text-align:center;">
									<?php if($musicurl) { ?>
										<a title="edit" href="javascript:void(0);"
										   class="btn btn-blue btn-editmusicinfo"
										   data-id="<?php echo $musicid; ?>"
										   data-title="<?php echo $musictitle; ?>"
										>Rename Music</a>
									<?php } ?>
								</td> -->
							</tr>
							<?php
						}
					}
					?>
					<?php
				}
				else{
					echo '<tr><td colspan="6" align="center">No Routines Found</td></tr>';
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<th class="hidden"></th>
						<th class="hidden"></th>
						<th class="hidden"></th>
						<th>All Age Division</th>
						<th>All Categories</th>
						<th class="hidden">All Cities</th>
						<!-- <th class="hidden"></th>
						<th class="hidden"></th> -->
					</tr>
				</tfoot>
			</table>
			<?php } ?>
			<div id="popup-save-music-info" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Edit Music Info</h4>
						</div>
						<div class="modal-body">
							<form method="post" action="" id="form-save-music-info" name="form-save-music-info" >
								<input type="hidden" name="music-id" id="music-id" value="" />
								<p>Rename <br /><input type="text" name="music-title" id="music-title" value="" /></p>
								<input type="submit" value="Save" class="btn btn-blue">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function ts_tours_page() {
	?>
	<div id="tours-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?><a class="btn btn-blue btn-addtour" href="javascript:void(0);">Add New</a></h1>
		<div class="ts-admin-wrapper tours-wrapper">
			<table id="tours-list" class="ts-data-table" data-length="10" data-sort="asc" data-orderby="2">
				<thead>
				<tr>
					<th style="text-align:left;">Title</th>
					<th style="text-align:center;">City</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Workshop</th>
					<th style="text-align:center;">Status</th>
					<th style="text-align:center;">Music</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$tours = ts_get_posts('ts_tour');
				if($tours) {
					foreach ($tours as $tour) {
						setup_postdata($tour);
						$tour_id 	= $tour->ID;
						$title 		= $tour->post_title;
						$status 	= get_post_meta($tour_id, 'status', true);
						$date_from 	= get_post_meta($tour_id, 'date_from', true);
						$date_to 	= get_post_meta($tour_id, 'date_to', true);
						$fmt_dfrom 	= date_format(date_create($date_from),'m/d/Y');
						$fmt_dto 	= date_format(date_create($date_to),'m/d/Y');
						$venue 		= get_post_meta($tour_id, 'venue', true);
						$city 		= get_post_meta($tour_id, 'city', true);
						$workshop 	= get_post_meta($tour_id, 'workshop', true);
                        $competition= get_post_meta($tour_id, 'competition', true);
						$list_id 	= get_post_meta($tour_id, 'list_id', true);
						$wstattext  = $workshop==2 ? 'Closed' : 'Open';
						$stattext 	= $status==2 ? 'Closed' : 'Open';
						$btntext 	= $status==2 ? 'Open' : 'Close';
						$musiczip_filename = get_post_meta($tour_id, 'musiczip_filename', true);
						$downloadmusiczip = $musiczip_filename ? '<a href="'.TS_ZIP_ATTACHMENTS_URL.'/ts-music-download.php?ts_pretty_filename='.sanitize_file_name($title).'&ts_real_filename='.$musiczip_filename.'">Download</a>' : '';
						?>
						<tr id="item-<?php echo $tour_id; ?>">
							<td style="text-align:left;width:20%"><?php echo $title; ?></td>
							<td style="text-align:center;"><?php echo $city; ?></td>
							<td style="text-align:center;"><?php echo $fmt_dfrom; ?></td>
							<td style="text-align:center;"><?php echo $fmt_dto; ?></td>
							<td style="text-align:center;" class="workshop-status"><?php echo $wstattext; ?></td>
							<td style="text-align:center;" class="tour-status"><?php echo $stattext; ?></td>
							<td style="text-align:center;" class="tour-musics"><?php echo $downloadmusiczip;?></td>
							<td style="text-align:center;">
								<a title="Edit" href="javascript:void(0);"
								   class="btn btn-blue btn-edittour"
								   data-id="<?php echo $tour_id; ?>"
								   data-title="<?php echo $title; ?>"
								   data-status="<?php echo $status; ?>"
								   data-datefrom="<?php echo $fmt_dfrom; ?>"
								   data-dateto="<?php echo $fmt_dto; ?>"
								   data-venue="<?php echo $venue; ?>"
								   data-city="<?php echo $city; ?>"
								   data-workshop="<?php echo $workshop; ?>"
                                   data-competition="<?php echo $competition; ?>"
								   data-listid="<?php echo $list_id; ?>"
								><small>Edit</small></a>
								<a title="<?php echo $btntext; ?>" href="javascript:void(0);"
								   class="btn btn-green btn-closetour"
								   data-id="<?php echo $tour_id; ?>"
								><small><?php echo $btntext; ?></small></a>
								<a title="delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $tour_id; ?>"
								   data-type="post"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="5">No Tours Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<div id="popup-save-tour" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Add Tour</h4>
				</div>
				<div class="modal-body">
					<form method="post" action="" id="form-save-tour" name="form-save-tour" >
						<input type="hidden" name="tour-id" id="tour-id" value="" />
						<p><label><input type="checkbox" name="tour-status" id="tour-status" value="1" checked="true" /> Enable Tour</label></p>
						<p><label><input type="checkbox" name="tour-workshop" id="tour-workshop" value="1" checked="true" /> Enable Workshop</label></p>
                        <p><label><input type="checkbox" name="tour-competition" id="tour-competition" value="1" checked="true" /> Enable Competition</label></p>
						<p>Title <br /><input type="text" name="tour-title" id="tour-title" value="" /></p>
						<p>City <br /><input type="text" name="tour-city" id="tour-city" value="" /></p>
						<p>Venue <br /><input type="text" name="tour-venue" id="tour-venue" value="" /></p>
						<p>Date Start <br /><input type="text" name="tour-datefrom" id="tour-datefrom" value="" maxlength="10" class="validate[required,custom[date_format]] formatted-date ts-date-picker" placeholder="MM/DD/YYYY" /></p>
						<p>Date End <br /><input type="text" name="tour-dateto" id="tour-dateto" value="" maxlength="10" class="validate[required,custom[date_format]] formatted-date ts-date-picker" placeholder="MM/DD/YYYY" /></p>
						<p>Mailchimp List ID <br /><input type="text" name="tour-listid" id="tour-listid" value="" /></p>
						<input type="submit" value="Save" class="btn btn-blue">
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function ts_schedules_page() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
			<table id="schedules-list" class="ts-data-table" data-length="25" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">City</th>
					<th style="text-align:left;">Type</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Status</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'post_status' => 'any',
				);				
				$schedules = ts_get_posts('ts_event', -1, $args);
				if($schedules) {
					foreach ($schedules as $schedule) {
						setup_postdata($schedule);
						$schedule_id 	= $schedule->ID;
						$title 			= $schedule->post_title;
						$tour_id 		= get_post_meta($schedule_id, 'event_city', true);
						$city 			= get_post_meta($tour_id, 'city', true);
						$date_from 		= date('m/d/Y', strtotime(get_post_meta($tour_id, 'date_from', true)));
						$date_to 		= date('m/d/Y', strtotime(get_post_meta($tour_id, 'date_to', true)));
						$schedule_type 	= wp_get_object_terms($schedule_id, 'ts_schedules_type');
						$schedule_action = isset($schedule_type[0]->name) && 'Competition' === $schedule_type[0]->name ? 'ts-edit-competition-schedule' : 'ts-edit-workshop-schedule';
						$status 		= $schedule->post_status;
						$btnstatus_label = $status === 'publish' ? 'Unpublish' : 'Publish';
						?>
						<tr id="item-<?php echo $schedule_id; ?>">
							<td style="text-align:left;"><?php echo $city; ?></td>
							<td style="text-align:left;"><?php echo ucwords($schedule_type[0]->name);?></td>
							<td style="text-align:center;"><?php echo $date_from; ?></td>
							<td style="text-align:center;"><?php echo $date_to; ?></td>
							<td style="text-align:center;" class="schedule-status"><?php echo $status === 'publish' ? 'Published' : 'Draft'; ?></td>
							<td style="text-align:center;">
								<a title="Edit" href="<?php echo admin_url('admin.php?page='.$schedule_action.'&schedule_id='. $schedule_id .'&tour='. $tour_id); ?>"
									class="btn btn-blue"
								><small>Edit</small></a>
								<a title="<?php echo $btnstatus_label; ?>" href="javascript:void(0);"
								   class="btn btn-green btn-publish"
								   data-id="<?php echo $schedule_id; ?>"
								><small><?php echo $btnstatus_label; ?></small></a>
								<a title="Delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $schedule_id; ?>"
								   data-type="post"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="6" align="center">No Schedules Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php	
}

function ts_workshopschedules_page() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?><a class="btn btn-blue btn-addschedule" href="<?php echo admin_url('admin.php?page=ts-new-workshop-schedule'); ?>">Add New</a></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
			<table id="schedules-list" class="ts-data-table" data-length="25" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">City</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Status</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'post_status' => 'any',
					'tax_query' => array(
						array(
							'taxonomy' => 'ts_schedules_type',
							'field' => 'name',
							'terms' => 'Workshop',
							'operator' => 'IN'
						),
					)
				);
				$schedules = ts_get_posts('ts_event',-1,$args);
				if($schedules) {
					foreach ($schedules as $schedule) {
						setup_postdata($schedule);
						$schedule_id 	= $schedule->ID;
						$title 			= $schedule->post_title;
						$tour_id 		= get_post_meta($schedule_id, 'event_city', true);
						$city 			= get_post_meta($tour_id, 'city', true);
						$date_from 		= date('m/d/Y', strtotime(get_post_meta($tour_id, 'date_from', true)));
						$date_to 		= date('m/d/Y', strtotime(get_post_meta($tour_id, 'date_to', true)));
						$status 		= $schedule->post_status;
						$btnstatus_label = $status === 'publish' ? 'Unpublish' : 'Publish';
						?>
						<tr id="item-<?php echo $schedule_id; ?>">
							<td style="text-align:left;"><?php echo $city; ?></td>
							<td style="text-align:center;"><?php echo $date_from; ?></td>
							<td style="text-align:center;"><?php echo $date_to; ?></td>
							<td style="text-align:center;" class="schedule-status"><?php echo $status === 'publish' ? 'Published' : 'Draft'; ?></td>
							<td style="text-align:center;">
								<a title="Edit" href="<?php echo admin_url('admin.php?page=ts-edit-workshop-schedule&schedule_id='. $schedule_id .'&tour='. $tour_id); ?>"
									class="btn btn-blue"
								><small>Edit</small></a>
								<a title="<?php echo $btnstatus_label; ?>" href="javascript:void(0);"
								   class="btn btn-green btn-publish"
								   data-id="<?php echo $schedule_id; ?>"
								><small><?php echo $btnstatus_label; ?></small></a>
								<a title="Delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $schedule_id; ?>"
								   data-type="post"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="5">No Schedule Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}

function ts_competitionschedules_page() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?><a class="btn btn-blue btn-addschedule" href="<?php echo admin_url('admin.php?page=ts-new-competition-schedule'); ?>">Add New</a></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
			<table id="schedules-list" class="ts-data-table" data-length="25" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">City</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Status</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'post_status' => 'any',
					'tax_query' => array(
						array(
							'taxonomy' => 'ts_schedules_type',
							'field' => 'name',
							'terms' => 'Competition',
							'operator' => 'IN'
						),
					)
				);
				$schedules = ts_get_posts('ts_event',-1,$args);
				if($schedules) {
					foreach ($schedules as $schedule) {
						setup_postdata($schedule);
						$schedule_id 	= $schedule->ID;
						$title 			= $schedule->post_title;
						$tour_id 		= get_post_meta($schedule_id, 'event_city', true);
						$city 			= get_post_meta($tour_id, 'city', true);
						$date_from 		= date('m/d/Y', strtotime(get_post_meta($tour_id, 'date_from', true)));
						$date_to 		= date('m/d/Y', strtotime(get_post_meta($tour_id, 'date_to', true)));
						$status 		= $schedule->post_status;
						$btnstatus_label = $status === 'publish' ? 'Unpublish' : 'Publish';
						?>
						<tr id="item-<?php echo $schedule_id; ?>">
							<td style="text-align:left;"><?php echo $city; ?></td>
							<td style="text-align:center;"><?php echo $date_from; ?></td>
							<td style="text-align:center;"><?php echo $date_to; ?></td>
							<td style="text-align:center;" class="schedule-status"><?php echo $status === 'publish' ? 'Published' : 'Draft'; ?></td>
							<td style="text-align:center;">
								<a title="Edit" href="<?php echo admin_url('admin.php?page=ts-edit-competition-schedule&schedule_id='. $schedule_id .'&tour='. $tour_id); ?>"
									class="btn btn-blue"
								><small>Edit</small></a>
								<a title="<?php echo $btnstatus_label; ?>" href="javascript:void(0);"
								   class="btn btn-green btn-publish"
								   data-id="<?php echo $schedule_id; ?>"
								><small><?php echo $btnstatus_label; ?></small></a>
								<a title="Delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $schedule_id; ?>"
								   data-type="post"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="4">No Schedule Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}

function ts_post_workshop_schedule() {

	$schedule_id = $_GET['schedule_id'];

	if (isset($schedule_id) && $schedule_id != '') {
		$schedule_id 	= absint($schedule_id);
		$schedule 		= get_post($schedule_id);
		$title 			= $schedule->post_title;
		?>
		<div id="view-schedule-page" class="wrap">
			<h1 class="admin-page-title"><?php echo $title; ?><a class="btn btn-blue btn-addschedule" href="<?php echo admin_url('admin.php?page=ts-new-workshop-schedule'); ?>">Add New</a></h1>
			<div class="ts-admin-wrapper schedule-wrapper">
                <div class="row">
                    <div class="col-md-12 t-right">
                        <a href="javascript:void(0)" class="btn btn-green btn-previewworkshopschedule">Preview</a>
                    </div>
                </div>
	        	<?php
                $options = array(
                    'post_id'  => $schedule_id,
                    'form_attributes'  => array(
                        'class'  => 'schedule_settings'
                    ),
                    'html_field_open'  => '<div class="field">',
                    'html_field_close'  => '</div>',
                    'html_before_fields'  => '',
                    'html_after_fields'  => '',
                    'submit_value'  => 'Update Schedule',
                    'updated_message'  => 'Schedule Updated.',
                );
                acf_form($options);
                ?>  
			</div>
            <div id="popup-workshopsched-preview" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Workshop Schedule Preview</h4>
                        </div>
                        <div id="downloadschedule" class="modal-body">
                            <?php
                            $schedule = get_post($schedule_id);
                            ts_display_workshop_schedules(array($schedule));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<?php	
	}
	else {
		?>
		<div id="view-schedule-page" class="wrap">
			<h1 class="admin-page-title">New Schedule</h1>
			<div class="ts-admin-wrapper schedule-wrapper">
	        	<?php
                $options = array(
                    'post_id'  => 'new_schedule',
                    'form_attributes'  => array(
                        'class'  => 'schedule_settings'
                    ),
                    'field_groups' => array('group_59c21e47cc2b5'),
                    'html_field_open'  => '<div class="field">',
                    'html_field_close'  => '</div>',
                    'html_before_fields'  => '',
                    'html_after_fields'  => '',
                    'submit_value'  => 'Save Schedule',
                    'updated_message'  => 'Schedule Saved.',
                );
                acf_form($options);
                ?>  
			</div>
		</div>
		<?php		
	}
}

function ts_post_competition_schedule() {

	$schedule_id = $_GET['schedule_id'];
	$tour_id = $_GET['tour'];

	if (isset($schedule_id) && $schedule_id != '') {
		$schedule 		= get_post($schedule_id);
		$title 			= $schedule->post_title;
		?>
		<div id="view-schedule-page" class="wrap">
			<h1 class="admin-page-title"><?php echo $title; ?><a class="btn btn-blue btn-addschedule" href="<?php echo admin_url('admin.php?page=ts-new-competition-schedule'); ?>">Add New</a></h1>
			<div class="ts-admin-wrapper schedule-wrapper">
				<div class="row">
					<div class="col-md-12 t-right">
						<a href="javascript:void(0)" class="btn btn-green btn-downloadschedule">Download</a>
						<button class="btn btn-red btn-resetschedule" data-id="<?php echo $schedule_id; ?>" data-return="<?php echo admin_url('admin.php?page=ts-edit-competition-schedule&schedule_id='. $schedule_id .'&tour='. $tour_id); ?>">Reset</button>&nbsp;&nbsp;
						<a href="javascript:void(0)" class="btn btn-green btn-previewschedule">Preview</a>
					</div>
				</div>
				<?php
				/*if($tour_id) {
					$schedule_saved = get_post_meta($schedule_id, 'schedule_saved', true);
					$tour_date = get_post_meta($tour_id, 'date_from', true);
					$categories = ts_get_competition_categories();
					$genres = ts_get_routine_genres();
					if(! $schedule_saved){
					    $args = array(
					        'posts_per_page' => -1,
					        'include' => ts_tour_routines_ids($tour_id),
					        'orderby' => 'meta_value_num',
							'meta_key' => 'agediv_order',
					        'order' => 'ASC',
					    );
					    $routines = ts_get_posts('ts_routine',-1,$args);
						if($routines){
							$count_total = count($routines);
							$count_perday = $count_total <= 5 ? absint($count_total/3)+1 : absint($count_total/3);
							$count = 0;
							$day1 = $day2 = $day3 = array();

							$strtotime1 = strtotime($tour_date . '+17 hours');
							$strtotime2 = strtotime($tour_date . '+1 days 17 hours');
							$strtotime3 = strtotime($tour_date . '+2 days 17 hours');
							$timeday1 = date('F j, Y g:i a', $strtotime1);
							$timeday2 = date('F j, Y g:i a', $strtotime2);
							$timeday3 = date('F j, Y g:i a', $strtotime3);

							foreach ($routines as $r) {
								$count++;
								$id = $r->ID;
								$studio = ts_post_studio($id);
								$agediv = get_post_meta($id, 'agediv', true);
								$cat = get_post_meta($id, 'cat', true);
								$cat_name = $categories[$cat]['title'];
								$genre = get_post_meta($id, 'genre', true);
								$genre_name = $genres[$genre]['title'];
								$time_limit = $categories[$cat]['time_limit'];

								if($count <= $count_perday) {
									$time_start1 = $strtotime1;
									$time_end1 = $strtotime1+$time_limit;
									$strtotime1 = $time_end1;
									$day1[] = array(
									    'field_59d2674f9703c' => $count,
									    'field_59d2674f973fa' => date('g:i a', $time_start1),
									    'field_5a0aecd9b6bb4' => date('g:i a', $time_end1),
									    'field_59d2674f977de' => $studio,
									    'field_59d2674f97bd8' => $id,
									    'field_59d2674f97fbb' => $agediv,
									    'field_59d2674f9839c' => $cat_name,
									    'field_59d2674f9878a' => $genre_name,
									    'field_59d2674f98ba4' => 'Normal',
									);
								}
								else if($count > $count_perday && $count <= $count_perday*2) {
									$time_start2 = $strtotime2;
									$time_end2 = $strtotime2+$time_limit;
									$strtotime2 = $time_end2;
									$day2[] = array(
									    'field_59d2674f9703c' => $count,
									    'field_59d2674f973fa' => date('g:i a', $time_start2),
									    'field_5a0aecd9b6bb4' => date('g:i a', $time_end2),
									    'field_59d2674f977de' => $studio,
									    'field_59d2674f97bd8' => $id,
									    'field_59d2674f97fbb' => $agediv,
									    'field_59d2674f9839c' => $cat_name,
									    'field_59d2674f9878a' => $genre_name,
									    'field_59d2674f98ba4' => 'Normal',
									);
								}
								else {
									$time_start3 = $strtotime3;
									$time_end3 = $strtotime3+$time_limit;
									$strtotime3 = $time_end3;
									$day3[] = array(
									    'field_59d2674f9703c' => $count,
									    'field_59d2674f973fa' => date('g:i a', $time_start3),
									    'field_5a0aecd9b6bb4' => date('g:i a', $time_end3),
									    'field_59d2674f977de' => $studio,
									    'field_59d2674f97bd8' => $id,
									    'field_59d2674f97fbb' => $agediv,
									    'field_59d2674f9839c' => $cat_name,
									    'field_59d2674f9878a' => $genre_name,
									    'field_59d2674f98ba4' => 'Normal',
									);
								}
							}

							$newvalue = array(
								array(
									'field_59d2674f77b98' => $timeday1,
									'field_59d2674f77f7b' => $day1,
								),
								array(
									'field_59d2674f77b98' => $timeday2,
									'field_59d2674f77f7b' => $day2,
								),
								array(
									'field_59d2674f77b98' => $timeday3,
									'field_59d2674f77f7b' => $day3,
								),
							);
						}
					}
					else {
						$newvalue = $value;
						$count = 0;
						foreach ($value as $a => $b) {
							$start = strtotime($b['field_59d2674f77b98']);
							$lineup = $b['field_59d2674f77f7b'];
							foreach ($lineup as $c => $d) {
								if($d['field_59d2674f98ba4']=='Normal') {
									$count++;
									$newvalue[$a]['field_59d2674f77f7b'][$c]['field_59d2674f9703c'] = $count;
								}	
								$end = strtotime($d['field_5a0aecd9b6bb4']);
								$newvalue[$a]['field_59d2674f77f7b'][$c]['field_59d2674f973fa'] = date('g:i a', $start);
								$newvalue[$a]['field_59d2674f77f7b'][$c]['field_5a0aecd9b6bb4'] = date('g:i a', $end);
								$start = $end;
							}
						}
					}
					$value = $newvalue;
				}*/
				?>

				<?php
				$options = array(
					'post_id'  => $schedule_id,
					'form_attributes'  => array(
						'class'  => 'schedule_settings'
					),
					'field_groups' => array('group_59d2674ac404f'),
					'html_field_open'  => '<div class="field">',
					'html_field_close'  => '</div>',
					'html_before_fields'  => '',
					'html_after_fields'  => '',
					'submit_value'  => 'Update Schedule',
					'updated_message'  => 'Schedule Updated.',
				);
				acf_form($options);
				?>
			</div>
			<div id="popup-competitionsched-preview" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Competition Schedule Preview</h4>
						</div>
						<div id="downloadschedule" class="modal-body">
							<?php 
							$schedule = get_post($schedule_id);
							ts_display_competition_schedules(array($schedule)); 
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	else {
		?>
		<div id="view-schedule-page" class="wrap">
			<h1 class="admin-page-title">New Schedule</h1>
			<div class="ts-admin-wrapper schedule-wrapper">
				<?php 
				$off = ts_tours_with_competition_schedule();
				ts_select_tour_city(admin_url('admin.php') .'?page=ts-edit-competition-schedule', $tour_id, $off);
				?>
				<?php
				if (isset($tour_id) && $tour_id != '') {
					$options = array(
						'post_id'  => 'new_schedule',
						'form_attributes'  => array(
							'class'  => 'schedule_settings'
						),
						'field_groups' => array('group_59d2674ac404f'),
						'html_field_open'  => '<div class="field">',
						'html_field_close'  => '</div>',
						'html_before_fields'  => '',
						'html_after_fields'  => '',
						'submit_value'  => 'Save Schedule',
						'updated_message'  => 'Schedule Saved.',
					);
					acf_form($options);
				}
				?>
			</div>
		</div>
		<?php
	}
}

function ts_scores_page() {
	$tour_id = ts_get_param('tour');
	?>
	<div id="scores-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper scores-wrapper">
			<div class="row">
				<div class="col-md-6">
					<?php 
					ts_select_tour_city(admin_url('admin.php') .'?page=ts-competition-scores', $tour_id);
					?>
				</div>
				<div class="col-md-6 t-right">
				</div>
			</div>
			<?php 
			if($tour_id) {
				?>
				<div id="routine-scores">
					<form method="post" action="" id="form-submit-scores" name="form-submit-scores" >
						<table id="routines-list" class="ts-data-table" data-sort="asc" data-orderby="0" data-length="-1">
							<thead>
							<tr>
								<th style="text-align:center;">#</th>
								<th style="text-align:center;">Studio</th>
								<th style="text-align:center;">Routine</th>
								<th style="text-align:center;">Category</th>
								<th style="text-align:center;">Genre</th>
								<th style="text-align:center; width:80px;">Judge 1</th>
								<th style="text-align:center; width:80px;">Judge 2</th>
								<th style="text-align:center; width:80px;">Judge 3</th>
								<th style="text-align:center;">Total</th>
								<th style="text-align:center;">Action</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$schedule_id = ts_get_scheduleid_by_tourid($tour_id);
							$schedules = get_field('competition_event_schedules', $schedule_id);
							if($schedules) {
								foreach ($schedules as $s) {
									$lineup = $s['lineup'];
									if(! empty($lineup)){
										foreach ($lineup as $l) {
											if($l['action']=='Normal') {
												$routine_id 	= $l['routine'];
												$routine_num 	= $l['number'];
												$studio 		= $l['studio'];
												$category 		= $l['category'];
												$genre 			= $l['genre'];
												$routine_name 	= get_the_title($routine_id);
												$judges_scores 	= get_post_meta($routine_id, 'judges_scores', true);
												$total_score 	= get_post_meta($routine_id, 'total_score', true);
												$routine_num 	= get_post_meta($routine_id, 'routine_number', true);
												?>
												<tr id="routine-<?php echo $routine_id; ?>">
													<td style="text-align:center;"><?php echo $routine_num; ?></td>
													<td style="text-align:center;"><?php echo $studio; ?></td>
													<td style="text-align:center;"><?php echo $routine_name; ?></td>
													<td style="text-align:center;"><?php echo $category; ?></td>
													<td style="text-align:center;"><?php echo $genre; ?></td>
													<td style="text-align:center;"><input class="score-judge1" type="text" name="scores[<?php echo $routine_id?>][judge1]" value="<?php echo $judges_scores[0]; ?>"></td>
													<td style="text-align:center;"><input class="score-judge2" type="text" name="scores[<?php echo $routine_id?>][judge2]" value="<?php echo $judges_scores[1]; ?>"></td>
													<td style="text-align:center;"><input class="score-judge3" type="text" name="scores[<?php echo $routine_id?>][judge3]" value="<?php echo $judges_scores[2]; ?>"></td>
													<td style="text-align:center;" class="total-score"><?php echo $total_score; ?></td>
													<td style="text-align:center;"><button class="btn-submitscore" data-id="<?php echo $routine_id; ?>">Submit</button></td>
												</tr>
												<?php
											}
										}
									}
								}
							}else{
								echo '<tr><td colspan="10" align="center">Schedule not found or not publish yet.</td></tr>';
							}
							?>
							</tbody>
						</table>	
					</form>				
				</div>	
			<?php
			} ?>
		</div>
	</div>
	<?php	
}

function ts_special_awards_page() {
	if(isset($_GET['tour']) && $_GET['tour']!='') {
		$tour_id = $_GET['tour'];
	}
	?>
	<div id="awards-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper awards-wrapper">
			<form name="form-special-awards" id="form-special-awards" class="validate" method="post" action="">
				<p><?php ts_select_tour_city(admin_url('admin.php') .'?page=ts-specialty-awards', $tour_id); ?></p>
				<?php 
				if($tour_id) { 
					$special_awards 		= get_post_meta($tour_id, 'special_awards', true); 

					$choreo12below_id 		= isset($special_awards['twelve_below']['choreography']['routine_id']) 			? $special_awards['twelve_below']['choreography']['routine_id'] : '';
					$choreo12below_num 		= get_post_meta($choreo12below_id, 'routine_number', true);
					$standnom12below_id 	= isset($special_awards['twelve_below']['standout_nominee']['routine_id']) 		? $special_awards['twelve_below']['standout_nominee']['routine_id'] : '';
					$standnom12below_num 	= get_post_meta($standnom12below_id, 'routine_number', true);
					$standwin12below_id 	= isset($special_awards['twelve_below']['standout_winner']['routine_id']) 		? $special_awards['twelve_below']['standout_winner']['routine_id'] : '';
					$standwin12below_num 	= get_post_meta($standwin12below_id, 'routine_number', true);
					$choreo13above_id 		= isset($special_awards['thirteen_above']['choreography']['routine_id']) 		 ? $special_awards['thirteen_above']['choreography']['routine_id'] : '';
					$choreo13above_num 		= get_post_meta($choreo13above_id, 'routine_number', true);
					$standnom13above_id 	= isset($special_awards['thirteen_above']['standout_nominee']['routine_id']) 	 ? $special_awards['thirteen_above']['standout_nominee']['routine_id'] : '';
					$standnom13above_num 	= get_post_meta($standnom13above_id, 'routine_number', true);
					$standwin13above_id 	= isset($special_awards['thirteen_above']['standout_winner']['routine_id']) 	 ? $special_awards['thirteen_above']['standout_winner']['routine_id'] : '';
					$standwin13above_num 	= get_post_meta($standwin13above_id, 'routine_number', true);

					$studio_innovator 		= isset($special_awards['studio_innovator']) ? get_field('studio', 'user_' . $special_awards['studio_innovator']) : '';
					?>
					<h3>For all 12 and under:</h3>
					<div class="table-container">
						<div class="row table-head">
							<div class="col-md-4">Award</div>
							<div class="col-md-2 t-center">Routine #</div>
							<div class="col-md-3 t-center">Routine Name</div>
							<div class="col-md-3 t-center">Studio</div>
						</div>
						<div class="table-body">
							<div class="row" id="item-1">
								<div class="col-md-4">Choreography Award:</div>
								<div class="col-md-2 t-center">
									<input type="text" name="special_awards[twelve_below][choreography][routine_number]" value="<?php echo $choreo12below_num; ?>" class="validate[custom[onlyNumberSp]] change-routine-number" data-tourid="<?php echo $tour_id; ?>">
									<input type="hidden" name="special_awards[twelve_below][choreography][routine_id]" value="<?php echo $choreo12below_id; ?>" class="routine-id">
								</div>
								<div class="col-md-3 t-center routine-name"><?php echo get_the_title($choreo12below_id);?></div>
								<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($choreo12below_id);?></div>
							</div>
							<div class="row" id="item-2">
								<div class="col-md-4">Judges Standout Nominee:</div>
								<div class="col-md-2 t-center">
									<input type="text" name="special_awards[twelve_below][standout_nominee][routine_number]" value="<?php echo $standnom12below_num; ?>" class="validate[custom[onlyNumberSp]] change-routine-number" data-tourid="<?php echo $tour_id; ?>">
									<input type="hidden" name="special_awards[twelve_below][standout_nominee][routine_id]" value="<?php echo $standnom12below_id; ?>" class="routine-id">
								</div>
								<div class="col-md-3 t-center routine-name"><?php echo get_the_title($standnom12below_id);?></div>
								<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($standnom12below_id);?></div>
							</div>
							<div class="row" id="item-3">
								<div class="col-md-4">Judges Standout Winner:</div>
								<div class="col-md-2 t-center">
									<input type="text" name="special_awards[twelve_below][standout_winner][routine_number]" value="<?php echo $standwin12below_num; ?>" class="validate[custom[onlyNumberSp]] change-routine-number" data-tourid="<?php echo $tour_id; ?>">
									<input type="hidden" name="special_awards[twelve_below][standout_winner][routine_id]" value="<?php echo $standwin12below_id; ?>" class="routine-id">
								</div>
								<div class="col-md-3 t-center routine-name"><?php echo get_the_title($standwin12below_id);?></div>
								<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($standwin12below_id);?></div>
							</div>
						</div>	
					</div>
					<h3>For all 13 and above:</h3>
					<div class="table-container">
						<div class="row table-head">
							<div class="col-md-4">Award</div>
							<div class="col-md-2 t-center">Routine #</div>
							<div class="col-md-3 t-center">Routine Name</div>
							<div class="col-md-3 t-center">Studio</div>
						</div>
						<div class="table-body">
							<div class="row" id="item-4">
								<div class="col-md-4">Choreography Award:</div>
								<div class="col-md-2 t-center">
									<input type="text" name="special_awards[thirteen_above][choreography][routine_number]" value="<?php echo $choreo13above_num; ?>" class="validate[custom[onlyNumberSp]] change-routine-number" data-tourid="<?php echo $tour_id; ?>">
									<input type="hidden" name="special_awards[thirteen_above][choreography][routine_id]" value="<?php echo $choreo13above_id; ?>" class="routine-id">
								</div>
								<div class="col-md-3 t-center routine-name"><?php echo get_the_title($choreo13above_id);?></div>
								<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($choreo13above_id);?></div>
							</div>
							<div class="row" id="item-5">
								<div class="col-md-4">Judges Standout Nominee:</div>
								<div class="col-md-2 t-center">
									<input type="text" name="special_awards[thirteen_above][standout_nominee][routine_number]" value="<?php echo $standnom13above_num; ?>" class="validate[custom[onlyNumberSp]] change-routine-number" data-tourid="<?php echo $tour_id; ?>">
									<input type="hidden" name="special_awards[thirteen_above][standout_nominee][routine_id]" value="<?php echo $standnom13above_id; ?>" class="routine-id">
								</div>
								<div class="col-md-3 t-center routine-name"><?php echo get_the_title($standnom13above_id);?></div>
								<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($standnom13above_id);?></div>
							</div>
							<div class="row" id="item-6">
								<div class="col-md-4">Judges Standout Winner:</div>
								<div class="col-md-2 t-center">
									<input type="text" name="special_awards[thirteen_above][standout_winner][routine_number]" value="<?php echo $standwin13above_num; ?>" class="validate[custom[onlyNumberSp]] change-routine-number" data-tourid="<?php echo $tour_id; ?>">
									<input type="hidden" name="special_awards[thirteen_above][standout_winner][routine_id]" value="<?php echo $standwin13above_id; ?>" class="routine-id">
								</div>
								<div class="col-md-3 t-center routine-name"><?php echo get_the_title($standwin13above_id);?></div>
								<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($standwin13above_id);?></div>
							</div>
						</div>	
					</div>
					<br /><br />
					<h3>Studio Innovator:<input type="text" name="special_awards[studio_innovator]" value="<?php echo $studio_innovator; ?>"></h3>
					<div class="form-footer-btns">
						<input class="btn btn-green" type="submit" value="Save Changes" />
					</div>
				<?php 
				} ?>
			</form>
		</div>
	</div>
	<?php	
}

function ts_scholarships_page() {
	if(isset($_GET['tour']) && $_GET['tour']!='') {
		$tour_id = $_GET['tour'];
	}
	?>
	<div id="scholarships-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper scholarships-wrapper">
			<form name="form-scholarships" id="form-scholarships" class="validate" method="post" action="">
				<p><?php ts_select_tour_city(admin_url('admin.php') .'?page=ts-scholarships', $tour_id); ?></p>
				<?php 
				if($tour_id) { 
					$scholarships 		= get_post_meta($tour_id, 'scholarships', true);
					?>
					<h3>Scholarships:</h3>
					<div class="table-container scholarship-wrapper">
						<?php
						$scholarships = get_post_meta($tour_id, 'scholarships', true);
						$participants = ts_tour_participants($tour_id);
						if(! empty($scholarships)) {
							?>
							<div class="row table-head">
								<div class="col-sm-2"><strong>Name</strong></div>
								<div class="col-sm-2"><strong>Age Division</strong></div>
								<div class="col-sm-2"><strong>Studio</strong></div>
								<div class="col-sm-2"><strong>Scholarship Number</strong></div>
								<div class="col-sm-2"><strong>Scholarship</strong></div>
								<div class="col-sm-2 t-center"><strong>Delete</strong></div>
							</div>
							<div class="scholarship-container table-body">
								<?php
								foreach ($scholarships as $key=>$val) {
									$id = $key;
									if(empty($val)) continue;
									?>
									<div class="row" id="item-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
										<div class="col-sm-2">
											<select class="scholarship" data-id="<?php echo $id; ?>">
												<option value="">None</option>
												<?php
												foreach ($participants as $p) {
													echo '<option value="'. $p .'" '. ( $id==$p ? 'selected' : '' ) .'>'. get_the_title($p) .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-2 age-division"><?php echo ts_participant_agediv($id); ?></div>
										<div class="col-sm-2 studio-name"><?php echo ts_post_studio($id); ?></div>
										<div class="col-sm-2 participant-number">
											<input type="text" name="scholarships[<?php echo $id; ?>][number]" value="<?php echo $val['number']; ?>">
										</div>
										<div class="col-sm-2 participant-scholarship">
											<input type="text" name="scholarships[<?php echo $id; ?>][title]" value="<?php echo $val['title']; ?>">
										</div>
										<div class="col-sm-2 t-center">
											<a href="javascript:void(0);" class="btn-remove btn btn-red"><small>Remove</small></a>
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
								<div class="col-sm-2"><strong>Name</strong></div>
								<div class="col-sm-2"><strong>Age Division</strong></div>
								<div class="col-sm-2"><strong>Studio</strong></div>
								<div class="col-sm-2"><strong>Scholarship Number</strong></div>
								<div class="col-sm-2"><strong>Scholarship</strong></div>
								<div class="col-sm-2 t-center"><strong>Delete</strong></div>
							</div>
							<div class="scholarship-container table-body">
								<?php
								for ($i=1; $i <= 5; $i++) {
									$id = $i;
									?>
									<div class="row" id="item-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
										<div class="col-sm-2">
											<select class="scholarship" data-id="<?php echo $id; ?>" >
												<option value="">Select Name</option>
												<?php
												foreach ($participants as $p) {
													echo '<option value="'. $p .'" '. ( $key==$p ? 'selected' : '' ) .'>'. get_the_title($p) .'</option>';
												}
												?>
											</select>
										</div>
										<div class="col-sm-2 age-division"></div>
										<div class="col-sm-2 studio-name"></div>
										<div class="col-sm-2 participant-scholarship">
											<input type="text" class="scholarship-num" name="scholarships[][number]" value="">
										</div>
										<div class="col-sm-2 participant-scholarship">
											<input type="text" class="scholarship-item" name="scholarships[][title]" value="">
										</div>
										<div class="col-sm-2 t-center">
											<a href="javascript:void(0);" class="btn-remove btn btn-red"><small>Remove</small></a>
										</div>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
						<a href="javascript:void(0);" class="btn-addscholarship btn btn-gray"><small>Add Scholarship</small></a>
					</div>
					<div class="form-footer-btns">
						<input class="btn btn-green" type="submit" value="Save Changes" />
					</div>
				<?php 
				} ?>
			</form>
		</div>
	</div>
	<?php	
}

function ts_results_page() {
	$publish_button = '';
	$tour_id = ts_get_param('tour');
	if($tour_id) {
		$status = get_post_meta($tour_id, 'results_status', true);
		$btn_label = ! $status || $status == 'draft' ? 'Publish Results' : 'Unpublish Results';
		$publish_button = '<button class="btn btn-blue btn-publishresults" data-id="'. $tour_id .'">'. $btn_label .'</button>';
	}	
	?>
	<div id="results-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?> <?php ts_select_tour_city(admin_url('admin.php?page=ts-results'), $tour_id); ?> <?php echo $publish_button; ?></h1>
		<div class="ts-admin-wrapper results-wrapper">
			<?php ts_display_results($tour_id); ?>
		</div>
	</div>
	<?php	
}

function ts_critiques_page() {
	$tour_id = ts_get_param('tour');
	wp_enqueue_media();
	?>
	<div id="critiques-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?> <?php ts_select_tour_city(admin_url('admin.php') .'?page=ts-critiques', $tour_id); ?></h1>
		<div class="ts-admin-wrapper critiques-wrapper">
			<?php
			$routines = ts_tour_routines_by_number($tour_id);
			if(! empty($routines)) {
				?>
				<a href="javascript:void(0);" class="btn-uploadcritiques btn btn-green"><small>Upload Critiques</small></a>
				<div class="table-container table-pad table-critiques">
					<div class="row table-head">
						<div class="col-md-1 t-center">Routine #</div>
						<div class="col-md-3">Routine Name</div>
						<div class="col-md-3">Studio</div>
						<div class="col-md-4">Critique</div>
					</div>
					<div class="table-body">
						<?php
						foreach ($routines as $r) {
							$id 		= $r->ID;
							$name 		= $r->post_title;
							$author 	= $r->post_author;
							$studio 	= get_field('studio', 'user_'. $author);
							$critique 	= absint(get_post_meta($id, 'critique', true));
							$number 	= absint(get_post_meta($id, 'routine_number', true));
							?>
							<div class="row" id="routine-<?php echo $id; ?>">
								<div class="col-md-1 t-center"><?php echo $number; ?></div>
								<div class="col-md-3"><?php echo $name; ?></div>
								<div class="col-md-3"><?php echo $studio; ?></div>
								<div class="col-md-5 routine-critique-container">
									<?php
									if(! $critique ) {
										echo '
										<a href="javascript:void(0);" class="btn-addroutinecritique btn btn-green" data-id="'. $id .'"><small>Upload</small></a>';
									}
									else{
										$critique_filename = basename(get_attached_file($critique));
										$critique_url = wp_get_attachment_url($critique);
										echo '
										<div><a target="_blank" href="'. $critique_url .'">'. $critique_filename .'</a>
										<a href="javascript:void(0);" class="btn-removeroutinecritique" data-id="'. $id .'"><small>Remove</small></a></div>';
									}
									?>
								</div>
							</div>
							<?php
						} ?>
					</div>
				</div>
				<?php
			}	
			?>			
		</div>
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

function ts_invoices_page() {
	?>
	<div id="invoices-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper vouchers-wrapper">
			<table id="invoices-list" class="ts-data-table" data-length="10" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:center;">Invoice ID</th>
					<th style="text-align:center;">Status</th>
					<th style="text-align:center;">Amount</th>
					<th style="text-align:center;">Note</th>
					<th style="text-align:center;">Action</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'post_status' => array('unpaid', 'paid', 'unpaidcheck', 'paidcheck', 'outstanding_amount'),
				);
				$invoices = ts_get_posts('ts_invoice',-1,$args);
				if($invoices) {
					foreach ($invoices as $invoice) {
						setup_postdata($invoice);
						$invoice_id 			= $invoice->ID;
						$invoice_code 			= $invoice->post_title;
						$invoice_status 		= $invoice->post_status;
						$invoice_amount 		= get_post_meta($invoice_id, 'invoice_amount', true);
						$invoice_note 		    = get_post_meta($invoice_id, 'invoice_note', true);
						?>
						<tr id="item-<?php echo $invoice_id; ?>">
							<td style="text-align:center;"><?php echo $invoice_code; ?></td>
							<td style="text-align:center;"><?php echo $invoice_status; ?></td>
							<td style="text-align:center;">$<?php echo number_format_i18n($invoice_amount,2); ?></td>
							<td style="text-align:center;"><?php echo $invoice_note; ?></td>
							<td style="text-align:center;">
								<a title="delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $invoice_id; ?>"
								   data-type="invoice"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="5">No Invoices Created</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}

/*** Old Pages ***/

function ts_score_page() {
	?>
	<div id="scores-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper scores-wrapper">
			<table id="scores-list" class="ts-data-table" data-length="10" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">Title</th>
					<th style="text-align:center;">Awards</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$scores = ts_get_posts('ts_score');
				if($scores) {
					foreach ($scores as $score) {
						setup_postdata($score);
						$score_id 	= $score->ID;
						$title 		= $score->post_title;
						$award_id 	= (int) get_post_meta($score_id,'award_id',true);
						$award_button 	= isset($award_id) && 'publish' === get_post_status($award_id) ? '<a title="edit" href="'.admin_url('admin.php?page=ts-view-awards&award_id='. $award_id).'"
								   class="btn btn-blue btn-viewaward"
								><small>View</small></a>' : '';
						?>
						<tr id="item-<?php echo $score_id; ?>">
							<td style="text-align:left;"><?php echo $title; ?></td>
							<td style="text-align:center;"><?php echo $award_button; ?></td>
							<td style="text-align:center;">
								<a title="edit" href="<?php echo admin_url('admin.php?page=ts-view-scores&score_id='. $score_id); ?>"
								   class="btn btn-blue btn-editscore"
								><small>Edit</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="5">No Scores Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}

function ts_view_scores_page() {
    $score_id 	= $_GET['score_id'];

    if (isset($score_id) && $score_id != '') {
        $score 		= get_post($score_id);
        $title 		= $score->post_title;
        ?>
        <div id="view-score-page" class="wrap">
            <h1 class="admin-page-title"><?php echo $title; ?></h1>
            <div class="ts-admin-wrapper score-wrapper">
                <?php
                $options = array(
                    'post_id'  => $score_id,
                    'form_attributes'  => array(
                        'class'  => 'score_settings'
                    ),
                    'field_groups' => array('group_19d2674ac404f'),
                    'html_field_open'  => '<div class="field">',
                    'html_field_close'  => '</div>',
                    'html_before_fields'  => '',
                    'html_after_fields'  => '',
                    'submit_value'  => 'Update Score',
                    'updated_message'  => 'Score Updated.',
                );
                acf_form($options);
                ?>
            </div>
        </div>
        <?php
    }
    else {
        ?>
        <div id="view-score-page" class="wrap">
            <h1 class="admin-page-title">New Score</h1>
            <div class="ts-admin-wrapper score-wrapper">
                <?php
                $options = array(
                    'post_id'  => 'new_score',
                    'form_attributes'  => array(
                        'class'  => 'score_settings'
                    ),
                    'field_groups' => array('group_19d2674ac404f'),
                    'html_field_open'  => '<div class="field">',
                    'html_field_close'  => '</div>',
                    'html_before_fields'  => '',
                    'html_after_fields'  => '',
                    'submit_value'  => 'Update Score',
                    'updated_message'  => 'Score Updated.',
                );
                acf_form($options);
                ?>
            </div>
        </div>
        <?php
    }
}

function ts_award_page() {
    ?>
    <div id="awards-page" class="wrap">
        <h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
        <div class="ts-admin-wrapper awards-wrapper">
            <table id="awards-list" class="ts-data-table" data-length="10" data-sort="asc">
                <thead>
                <tr>
                    <th style="text-align:left;">Title</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $awards = ts_get_posts('ts_award');
                if ($awards) {
                    foreach ($awards as $award) {
                        setup_postdata($award);
                        $award_id = $award->ID;
                        $title = $award->post_title;
                        ?>
                        <tr id="item-<?php echo $award_id; ?>">
                            <td style="text-align:left;"><?php echo $title; ?></td>
                            <td style="text-align:center;">
                                <a title="edit"
                                   href="<?php echo admin_url('admin.php?page=ts-view-awards&award_id=' . $award_id); ?>"
                                   class="btn btn-blue btn-editaward"
                                >
                                    <small>View</small>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="5">No Awards Generated</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function ts_view_awards_page() {
	$award_id 	= $_GET['award_id'];

	if (isset($award_id) && $award_id != '') {
		$score_id 		= (int) get_post_meta($award_id,'score_id',true);
		$title			= get_the_title($score_id);
		?>
		<div id="view-award-page" class="wrap">
			<h1 class="admin-page-title"><?php echo $title; ?></h1>
			<div class="ts-admin-wrapper awards-wrapper">
				<?php ts_display_awards_wrapper($score_id);?>
			</div>
		</div>
		<?php
	}
	else {
		echo 'Score is not generated yet';
	}
}