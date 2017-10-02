<?php
function ts_entries_page() {
	?>
	<div id="entries-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper entries-wrapper">
			<table id="entries-list" class="ts-data-table" data-length="10" data-sort="asc" data-filter="true" data-colfilter="true" data-exportcol="0,1,2,3,4,5" data-dom="fBrt<'table-footer clearfix'pl>">
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
					<th style="width:50px; text-align:center;">Download</th>
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
						<tr id="item-<?php echo $entry_id; ?>">
							<td><?php echo get_the_title($tour_city); ?></td>
							<td style="text-align:center;"><?php echo $entry_type[0]->name; ?></td>
							<td style="text-align:center;"><?php echo $studio; ?></td>
							<td style="text-align:center;"><?php echo $name;?></td>
							<td style="text-align:center;"><?php echo $status_obj->label; ?></td>
							<td style="text-align:center;"><?php echo $date_paid; ?></td>
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
							<td style="text-align:center;"><a title="downloadallmusic" href="javascript:void(0);" class="btn-downloadallmusic" data-id="<?php echo $entry_id; ?>">Download All Music</a></td>
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
				<tfoot>
				<tr>
					<th>Cities</th>
					<th>Types</th>
					<th>Studios</th>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<th class="hidden"></th>
					<th class="hidden"></th>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?php
}

function ts_workshopentries_page() {
	?>
	<div id="entries-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper entries-wrapper">
			<table style="width: 100%;" id="entries-list" class="ts-data-table" data-length="25" data-sort="asc" data-orderby="1" data-filter="true" data-colfilter="true" data-exportcol="0,1,2,3,4" data-dom="fBrt<'table-footer clearfix'pl>">
				<thead>
				<tr>
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
					'post_status' => array('paid', 'paidcheck', 'outstanding_amount'),
				);
				$entries = ts_get_posts('ts_entry', -1, $args);

				if($entries) {
					$agedivname = '';
					$roster_posts = array();

					foreach ($entries as $entry) {
						setup_postdata($entry);
						$entry_id 		= $entry->ID;
						$author 		= $entry->post_author;
						$studio 		= get_field('studio', 'user_'. $author);
						$entry_type 	= wp_get_object_terms($entry_id, 'ts_entry_type');
						$entrytype_name = $entry_type[0]->name;
						$workshop 		= get_post_meta($entry_id, 'workshop', true);
						$participants 	= $workshop['participants'];
						$city 			= get_post_meta($workshop['tour_city'], 'city', true);
						if(! empty($participants) ){
							$args = array(
								'include' => array_keys($participants),
							);
							if($entrytype_name=='Studio') {
								$post_type = 'ts_studio_roster';
							}
							else if($entrytype_name=='Individual'){
								$post_type = 'ts_sibling';
							}
							$roster_posts = array_merge(ts_get_posts($post_type, -1, $args), $roster_posts);
						}
					}
				}
				if(! empty($roster_posts)) {
					$roster_posts = ts_trim_duplicate_objects($roster_posts);
					//$roster_posts = array_filter($roster_posts,'unique_obj');
					//print_r($roster_posts);


					foreach ($roster_posts as $rp) {
						$rid 			= $rp->ID;
						$age_div 		= wp_get_object_terms($rid, 'ts_agediv');
						$agediv_name 	= $age_div[0]->name;
						$name 			= get_the_title($rid);
						
						/*if($agedivname!==$agediv_name) {
							echo '
							<tr class="agediv-sep"><td colspan="5" align="center"><strong>'. $agediv_name .'<strong></td></tr>
							';
							$agedivname = $agediv_name;
						}*/
						?>
						<tr id="item-<?php echo $entry_id; ?>">
							<td><?php echo $name; ?></td>
							<td style="text-align:center;"><?php echo $agediv_name; ?></td>
							<td style="text-align:center;"><?php echo $entrytype_name; ?></td>
							<td style="text-align:center;"><?php echo $studio; ?></td>
							<td style="text-align:center;"><?php echo $city; ?></td>
						</tr>
						<?php
					}
					?>
					<?php
				}else{
					echo '<tr><td colspan="7">No Workshop Entries Found</td></tr>';
				}
				?>
				</tbody>
				<tfoot>
				<tr>
					<th class="hidden">Name</th>
					<th class="hidden">Age Division</th>
					<th class="hidden">Types</th>
					<th class="hidden">Studios</th>
					<th>Cities</th>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?php
}

function ts_competitionentries_page() {
	?>
	<div id="entries-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper entries-wrapper">
			<table id="entries-list" class="ts-data-table" data-length="25" data-sort="asc" data-filter="true" data-colfilter="true" data-exportcol="0,1,2,3,4" data-dom="fBrt<'table-footer clearfix'pl>">
				<thead>
				<tr>
					<th>Routine Name</th>
					<th style="text-align:center;">Dancers</th>
					<th style="text-align:center;">Age Division</th>
					<th style="text-align:center;">Category</th>
					<th style="text-align:center;">City</th>
					<th style="text-align:center;">Action</th>
					<th style="text-align:center;">Edit</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
					'post_status' => array('paid', 'paidcheck', 'outstanding_amount'),
				);
				$entries = ts_get_posts('ts_entry', -1, $args);

				if($entries) {
					foreach ($entries as $entry) {
						setup_postdata($entry);
						$entry_id 		= $entry->ID;
						$competition 	= get_post_meta($entry_id, 'competition', true);
						$routines 		= $competition['routines'];
						$workshop 		= get_post_meta($entry_id, 'workshop', true);
						$city 			= get_post_meta($workshop['tour_city'], 'city', true);

						if(! empty($routines) ){
							$routine_ids = array_keys($routines);
							$args = array(
								'order'            => 'ASC',
								'include'          => $routine_ids,
							);
							$routine_posts = ts_get_posts('ts_routine', -1, $args);

							foreach ($routine_posts as $rp) {
								$rpid 			= $rp->ID;
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
								?>
								<tr id="item-<?php echo $entry_id; ?>">
									<td><?php echo $name; ?></td>
									<td style="text-align:center;"><?php echo $dancers_string; ?></td>
									<td style="text-align:center;"><?php echo $agediv_name; ?></td>
									<td style="text-align:center;"><?php echo $cat_name; ?></td>
									<td style="text-align:center;"><?php echo $city; ?></td>
									<td style="text-align:center;"><?php echo $musicoutput;?></td>
									<td style="text-align:center;">
										<?php if($musicurl) { ?>
											<a title="edit" href="javascript:void(0);"
											   class="btn btn-blue btn-editmusicinfo"
											   data-id="<?php echo $musicid; ?>"
											   data-title="<?php echo $musictitle; ?>"
											>Rename Music</a>
										<?php } ?>
									</td>
								</tr>
								<?php
							}
						}
					}
					?>
					<?php
				}else{
					echo '<tr><td colspan="7">No Routines Found</td></tr>';
				}
				?>
				</tbody>
				<tfoot>
				<tr>
					<th class="hidden">Routine Name</th>
					<th class="hidden">Dancers</th>
					<th>Age Division</th>
					<th>Category</th>
					<th>Cities</th>
				</tr>
				</tfoot>
			</table>
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
							$date_to 	= get_post_meta($stop_id, 'date_to', true);
							$status 	= get_post_meta($stop_id, 'status', true);
							$disabled 	= ($date_from && ts_get_days_before_date($date_from) <= 0) || ($status==2) ? 'disabled' : '';
							?>
							<option value="<?php echo $stop_id; ?>" <?php echo $disabled; ?> ><?php echo $title; ?></option>
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

function ts_tours_page() {
	?>
	<div id="tours-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?><a class="btn btn-blue btn-addtour" href="javascript:void(0);">Add New</a></h1>
		<div class="ts-admin-wrapper tours-wrapper">
			<table id="tours-list" class="ts-data-table" data-length="10" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">Title</th>
					<th style="text-align:center;">City</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Workshop</th>
					<th style="text-align:center;">Tour</th>
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
						$list_id 	= get_post_meta($tour_id, 'list_id', true);
						?>
						<tr id="item-<?php echo $tour_id; ?>">
							<td style="text-align:left;"><?php echo $title; ?></td>
							<td style="text-align:center;"><?php echo $city; ?></td>
							<td style="text-align:center;"><?php echo $fmt_dfrom; ?></td>
							<td style="text-align:center;"><?php echo $fmt_dto; ?></td>
							<td style="text-align:center;"><?php echo $workshop==2 ? 'Closed' : 'Open'; ?></td>
							<td style="text-align:center;"><?php echo $status==2 ? 'Closed' : 'Open'; ?></td>
							<td style="text-align:center;">
								<a title="edit" href="javascript:void(0);"
								   class="btn btn-blue btn-edittour"
								   data-id="<?php echo $tour_id; ?>"
								   data-title="<?php echo $title; ?>"
								   data-status="<?php echo $status; ?>"
								   data-datefrom="<?php echo $fmt_dfrom; ?>"
								   data-dateto="<?php echo $fmt_dto; ?>"
								   data-venue="<?php echo $venue; ?>"
								   data-city="<?php echo $city; ?>"
								   data-workshop="<?php echo $workshop; ?>"
								   data-listid="<?php echo $list_id; ?>"
								><small>Edit</small></a>
								<a title="delete" href="javascript:void(0);"
								   class="btn btn-red btn-closetour"
								   data-id="<?php echo $tour_id; ?>"
								   data-type="post"
								><small>Close</small></a>
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
					<th style="text-align:center;">Status</th>
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

function ts_credits_page() {
	$autherid = get_current_user_id();
	?>
	<div id="credits-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?> ( $<?php echo number_format(ts_credit_totals($autherid),2);?> ) </h1>
		<div class="ts-admin-wrapper credits-wrapper">
			<table id="credits-list" class="ts-data-table" data-length="10" data-sort="asc">
				<thead>
				<tr>
					<th>City</th>
					<th>Credit Received</th>
					<th>Credit Expiry Date</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$creditArgs = array(
					'author' => $autherid,
				);
				$credits = ts_get_posts( 'ts_credit',-1,$creditArgs );
				if($credits) {
					foreach ($credits as $credit) {
						setup_postdata($credit);
						$credit_id 					= $credit->ID;
						$amount_credited			= (int) get_post_meta($credit_id, 'amount_credited', true);
						$amount_expiry_date 		= get_post_meta($credit_id, 'amount_expiry_date', true);
						$entry_id					= (int) get_post_meta($credit_id, 'entry_id', true);
						$workshop 					= get_post_meta($entry_id, 'workshop', true);
						?>
						<tr id="item-<?php echo $credit_id; ?>">
							<td><?php echo get_the_title($workshop['tour_city']); ?></td>
							<td>$<?php echo number_format($amount_credited, 2); ?></td>
							<td><?php echo $amount_expiry_date; ?></td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="3">No Credits Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}

function ts_schedules_page() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
			<table id="schedules-list" class="ts-data-table" data-length="50" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">City</th>
					<th style="text-align:left;">Type</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$schedules = ts_get_posts('ts_event');
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
						$schedule_action = isset($schedule_type[0]->name) && 'Competition' === $schedule_type[0]->name ? 'ts-view-competition-schedule' : 'ts-view-schedule';
						?>
						<tr id="item-<?php echo $schedule_id; ?>">
							<td style="text-align:left;"><?php echo $city; ?></td>
							<td style="text-align:left;"><?php echo $schedule_type[0]->name;?></td>
							<td style="text-align:center;"><?php echo $date_from; ?></td>
							<td style="text-align:center;"><?php echo $date_to; ?></td>
							<td style="text-align:center;">
								<a title="edit" href="<?php echo admin_url('admin.php?page='.$schedule_action.'&schedule_id='. $schedule_id); ?>"
									class="btn btn-blue"
								><small>Edit</small></a>
								<a title="delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $schedule_id; ?>"
								   data-type="post"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="3">No Schedule Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php	
}

function ts_view_schedule_page() {

	$schedule_id 	= $_GET['schedule_id'];

	if (isset($schedule_id) && $schedule_id != '') {
		$schedule 		= get_post($schedule_id);
		$title 			= $schedule->post_title;
		$date 			= date_format(date_create(get_post_meta($schedule_id, 'event_date', true)),'m/d/Y');
		?>
		<div id="view-schedule-page" class="wrap">
			<h1 class="admin-page-title"><?php echo $title; ?><a class="btn btn-blue btn-addschedule" href="<?php echo admin_url('admin.php?page=ts-new-schedule'); ?>">Add New</a></h1>
			<div class="ts-admin-wrapper schedule-wrapper">
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

function ts_schedpreview_page() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
		</div>
	</div>	
	<div class="inner SampleSched">
		<?php 
        $args = array(
            'post_status' => array('paid', 'paidcheck'),
            'meta_key' => 'tour_date',
            'meta_type' => 'DATE',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        );
        $my_entries = ts_get_user_posts('ts_entry', -1, false, $args);
        if($my_entries) {
            $city_array = array();
            foreach ($my_entries as $entry) {
                setup_postdata($entry);
                $entry_id = $entry->ID;
                $workshop = get_post_meta($entry_id, 'workshop', true);
                $city_array[] = $workshop['tour_city'];
            } 
            $city_array = ts_trim_duplicate($city_array);
			$args = array(
				'meta_query' => array(
					array(
						'key'     => 'event_city',
						'value'   => $city_array,
						'compare' => 'IN',
					),
				),
			);

        	$schedules = ts_get_posts('ts_event', -1, $args);

        	foreach ($schedules as $schedule) {
				$schedule_id = $schedule->ID;
				$counter = 1;

		        while(has_sub_field('event_schedules', $schedule_id)):
					echo '
						<h1 class="t-center">Workshop - '. $schedule->post_title .'</h1>';
			        ?>
			        <div class="SchedTable">
			        	<div class="TableCont">
				            <div id="Day_<?php echo $counter; ?>" class="TableHeading">
				                <?php echo get_sub_field('day'); ?>	
				            </div>
				            <div class="TableBody text-center">
				            	<div class="clearfix RowHeading">
				                	<div>
				                    	<span>Time</span>
				                    </div>
				                    <div>
				                    	<span>Seniors</span>
				                    </div>
				                    <div>
				                    	<span>Teens</span>
				                    </div>
				                    <div>
				                    	<span>Juniors</span>
				                    </div>
				                    <div>
				                    	<span>Minis</span>
				                    </div>
				                    <div>
				                    	<span>Munchkins/Pro/Teachers</span>
				                    </div>
				                </div>
				                <?php $c = 1;  
				                while(has_sub_field('lineup')): ?>
				                    <div class="clearfix Row_<?php echo $c; ?> <?php echo get_sub_field('columns');?>">
				                        <div>
				                            <span><?php echo get_sub_field('time'); ?></span>    
				                        </div>
				                        <div>
				                            <span><?php echo get_sub_field('seniors'); ?></span>	    
				                        </div>
				                        <div>
				                            <span><?php echo get_sub_field('teens'); ?></span>	    
				                        </div>
				                        <div>
				                            <span><?php echo get_sub_field('juniors'); ?></span>	    
				                        </div>
				                        <div>
				                            <span><?php echo get_sub_field('minis'); ?></span>	    
				                        </div>
				                        <div>
				                            <span><?php echo get_sub_field('munchkinsproteachers'); ?></span>    
				                        </div>
				                    </div>
				                <?php 
				                $c++; 
				                endwhile; 
				                ?> 
				            </div>
			            </div>
			        </div>
		        <?php
		        $counter++; 
		        endwhile;

				while(has_sub_field('competition_event_schedules', $schedule_id)):
					echo '
						<h1 class="t-center">Competition - '. $schedule->post_title .'</h1>';
					?>
					<div class="CompetitionSched SchedTable">
						<div class="TableCont">
							<div id="Day_<?php echo $counter; ?>" class="TableHeading">
								<?php echo get_sub_field('day'); ?>
							</div>
							<div class="TableBody text-center">
								<div class="clearfix RowHeading">
									<div>
										<span>Number</span>
									</div>
									<div>
										<span>Time</span>
									</div>
									<div>
										<span>Studio</span>
									</div>
									<div>
										<span>Routine</span>
									</div>
									<div>
										<span>Age Division</span>
									</div>
									<div>
										<span>Category</span>
									</div>
									<div>
										<span>Genre</span>
									</div>
								</div>
								<?php $c = 1;
								while(has_sub_field('lineup')):
								$col =  'Judges Break' === get_sub_field('action') || 'Awards' === get_sub_field('action') ? 'Col_1' : '';
								?>
								<div class="clearfix Row_<?php echo $c; ?> <?php echo $col;?>">
								<div>
									<span><?php echo get_sub_field('number'); ?></span>
								</div>
								<div>
									<span><?php echo get_sub_field('time'); ?></span>
								</div>
								<div>
									<span><?php echo get_sub_field('studio'); ?></span>
								</div>
								<div>
									<span><?php echo get_the_title(get_sub_field('routine')); ?></span>
								</div>
								<div>
									<span><?php echo get_sub_field('age_division'); ?></span>
								</div>
								<div>
									<span><?php echo get_sub_field('category'); ?></span>
								</div>
								<div>
									<span><?php echo get_sub_field('genre'); ?></span>
								</div>
							</div>
							<?php
							$c++;
							endwhile;
							?>
						</div>
					</div>
					</div>
					<?php
					$counter++;
				endwhile;
			}
        }
        ?>
    </div>
	<?php
}

function ts_workshopschedules_page() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?><a class="btn btn-blue btn-addschedule" href="<?php echo admin_url('admin.php?page=ts-new-schedule'); ?>">Add New</a></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
			<table id="schedules-list" class="ts-data-table" data-length="50" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">City</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
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
						?>
						<tr id="item-<?php echo $schedule_id; ?>">
							<td style="text-align:left;"><?php echo $city; ?></td>
							<td style="text-align:center;"><?php echo $date_from; ?></td>
							<td style="text-align:center;"><?php echo $date_to; ?></td>
							<td style="text-align:center;">
								<a title="edit" href="<?php echo admin_url('admin.php?page=ts-view-schedule&schedule_id='. $schedule_id); ?>"
								   class="btn btn-blue"
								><small>Edit</small></a>
								<a title="delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $schedule_id; ?>"
								   data-type="post"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="3">No Schedule Found</td></tr>';
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
			<table id="schedules-list" class="ts-data-table" data-length="50" data-sort="asc">
				<thead>
				<tr>
					<th style="text-align:left;">City</th>
					<th style="text-align:center;">Date Start</th>
					<th style="text-align:center;">Date End</th>
					<th style="text-align:center;">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$args = array(
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
						?>
						<tr id="item-<?php echo $schedule_id; ?>">
							<td style="text-align:left;"><?php echo $city; ?></td>
							<td style="text-align:center;"><?php echo $date_from; ?></td>
							<td style="text-align:center;"><?php echo $date_to; ?></td>
							<td style="text-align:center;">
								<a title="edit" href="<?php echo admin_url('admin.php?page=ts-view-competition-schedule&schedule_id='. $schedule_id); ?>"
								   class="btn btn-blue"
								><small>Edit</small></a>
								<a title="delete" href="javascript:void(0);"
								   class="btn btn-red btn-delete"
								   data-id="<?php echo $schedule_id; ?>"
								   data-type="post"
								><small>Delete</small></a>
							</td>
						</tr>
						<?php
					}
				}else{
					echo '<tr><td colspan="3">No Schedule Found</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}

function ts_view_competition_schedule_page() {

	$schedule_id 	= $_GET['schedule_id'];

	if (isset($schedule_id) && $schedule_id != '') {
		$schedule 		= get_post($schedule_id);
		$title 			= $schedule->post_title;
		$date 			= date_format(date_create(get_post_meta($schedule_id, 'event_date', true)),'m/d/Y');
		?>
		<div id="view-schedule-page" class="wrap">
			<h1 class="admin-page-title"><?php echo $title; ?><a class="btn btn-blue btn-addschedule" href="<?php echo admin_url('admin.php?page=ts-new-competition-schedule'); ?>">Add New</a></h1>
			<div class="ts-admin-wrapper schedule-wrapper">
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
					'field_groups' => array('group_59d2674ac404f'),
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
