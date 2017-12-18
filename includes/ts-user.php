<?php
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
						<th class="hidden">#</th>
						<th>City</th>
						<th style="text-align:center;">Status</th>
						<th style="text-align:center; width: 60px;">Results</th>
						<th style="text-align:center; width: 150px;">Actions</th>
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
							$status = get_post_status_object($entry->post_status);
							$saved = get_post_meta($entry_id, 'save_for_later', true);
							$step = $saved ? $saved : 1;
							$tour_city = get_post_meta($entry_id, 'tour_city', true);
							$tour_status = get_post_meta($tour_city, 'status', true);
							$results_status = get_post_meta($tour_city, 'results_status', true)
							?>
							<tr id="item-<?php echo $entry_id; ?>">
								<td class="hidden"><?php echo $count; ?></td>
								<td><?php echo get_the_title($tour_city); ?></td>
								<td style="text-align:center;"><?php echo $status->label; ?></td>
								<td style="text-align: center;">
									<?php 
									if($results_status=='publish') { ?>
										<a class="btn btn-green" href="<?php echo admin_url('admin.php?page=ts-my-results&tour='. $tour_city); ?>"><small>View</small></a>
									<?php 
									}
									else { ?>
										<span class="btn btn-gray"><small>View</small></span>
									<?php 
									} ?>
								</td>
								<td style="text-align:center;">
									<?php 
									if($tour_status !=2) { ?>
										<a title="edit" href="javascript:void(0);" class="btn btn-blue btn-edit-entry" data-eid="<?php echo $entry_id; ?>" data-url="<?php echo admin_url('admin.php?page=ts-edit-entry&action=edit&step='. $step .'&id='. $entry_id); ?>"><small>Edit</small></a>
									<?php 
									}
									else { ?>
										<span class="btn btn-gray"><small>Edit</small></span>
									<?php 
									} ?>
									<a title="delete" href="javascript:void(0);" class="btn btn-red btn-delete" data-id="<?php echo $entry_id; ?>" data-type="post"><small>Delete</small></a>
								</td>
							</tr>
							<?php
						}
						?>
						<?php
					}else{
						echo '<tr><td colspan="5">No Entries Found</td></tr>';
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
						<th class="hidden">#</th>
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
								<td class="hidden"><?php echo $count; ?></td>
								<td><?php echo get_the_title($workshop['tour_city']); ?></td>
								<td style="text-align:center;"><?php echo $invoice_note; ?></td>
								<td style="text-align:center;"><?php echo '$'. $invoice_amount;?></td>
								<td style="text-align:center;"><a title="payinvoice" href="javascript:void(0);" class="btn btn-blue btn-pay-invoice" data-ivid="<?php echo $invoice_id; ?>" data-eid="<?php echo $entry_id; ?>" data-url="<?php echo admin_url('admin.php?page=ts-entry-pay-invoice&action=pay_invoice&id='. $entry_id.'&evid='.$invoice_id); ?>"><small>Pay Now</small></a></td>
							</tr>
							<?php
						}
						?>
						<?php
					}
					else{
						echo '<tr><td colspan="5">No Invoices Found</td></tr>';
					}
					?>
				</tbody>
			</table>
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

function ts_mysched_preview() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
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
	            $routines_array = array();
	            foreach ($my_entries as $entry) {
	                setup_postdata($entry);
	                $entry_id = $entry->ID;
	                $workshop = get_post_meta($entry_id, 'workshop', true);
	                $tour_city = absint($workshop['tour_city']);
	                if(! in_array($tour_city, $city_array)) {
	                	$city_array[] = $tour_city;
	            	}
	                $competition = get_post_meta($entry_id, 'competition', true);
	                $routines = $competition['routines'];
	                if(! empty($routines)) {
		                $routine_ids = array_keys($routines);
		                $routines_array = array_merge($routine_ids, $routines_array); 
	                }
	            } 

				$args = array(
					'meta_query' => array(
						array(
							'key'     => 'event_city',
							'value'   => $city_array,
							'compare' => 'IN',
						),
					),
					'tax_query' => array(
						array(
							'taxonomy' => 'ts_schedules_type',
							'field'    => 'slug',
							'terms'    => 'workshop',
						),
					),
				);

	        	$workshop_schedules = ts_get_posts('ts_event', -1, $args);

	        	if(! empty($workshop_schedules))
	        		echo '<h1 class="t-center mysched-heading">Workshop</h1>';

	        	ts_display_workshop_schedules($workshop_schedules);

				$args = array(
					'meta_query' => array(
						array(
							'key'     => 'event_city',
							'value'   => $city_array,
							'compare' => 'IN',
						),
					),
					'tax_query' => array(
						array(
							'taxonomy' => 'ts_schedules_type',
							'field'    => 'slug',
							'terms'    => 'competition',
						),
					),
				);

	        	$competition_schedules = ts_get_posts('ts_event', -1, $args);

	        	if(! empty($competition_schedules))
	        		echo '<h1 class="t-center mysched-heading">Competition</h1>';

	        	ts_display_competition_schedules($competition_schedules, $routines_array);
	        }
	        ?>
		</div>
	</div>	
	<?php
}

function ts_workshopsched_preview() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
			<?php 
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
		    ?>
		</div>
	</div>	
	<?php
}

function ts_competitionsched_preview() {
	?>
	<div id="schedules-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper schedules-wrapper">
			<?php 
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
		    ?>
		</div>
	</div>	
	<?php
}

function ts_my_results_preview() {

	$tour_id = ts_get_param('tour');
	$user_id = get_current_user_id();
	?>
	<div id="results-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper results-wrapper">
			<p><?php ts_select_tour_city(admin_url('admin.php') .'?page=ts-my-results', $tour_id); ?></p>
			<?php
			if($tour_id) { ?>
				<h3>Routine Results:</h3>
				<table class="ts-data-table" data-length="-1" data-dom="frt<'table-footer clearfix'p>">
					<thead>
						<tr>
							<th style="text-align: center; width: 60px;">#</th>
							<th>Routine Name</th>
							<th style="text-align: center;">Score</th>
							<th style="text-align: center;">Adjudicated Awards</th>
							<th style="text-align: center;">Category High Scores</th>
							<th style="text-align: center;">Overall High Scores</th>
							<th style="text-align: center;">Special Awards</th>
							<th>Critique</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$args = array(
					        'orderby' => 'meta_value_num',
							'meta_key' => 'routine_number',
					        'order' => 'ASC',
					        'post__in' => ts_tour_routines_ids($tour_id),
						);
			        	$routines = ts_get_user_posts('ts_routine', -1, false, $args);
						foreach ($routines as $r) { 
							$id 			= $r->ID;
							$name 			= get_the_title($id);
							$number 		= get_post_meta($id, 'routine_number', true);
							$score  		= get_post_meta($id, 'total_score', true);
							$adjudicated  	= ts_adjudicated_award($score);
							$category_hs   	= ts_routine_cat_hs($id, $tour_id);
							$overall_hs   	= ts_routine_overall_hs($id, $tour_id);
							$critique_id 	= get_post_meta($id, 'critique', true);
							$critique_file  = basename(get_attached_file($critique_id));
							$critique_url 	= wp_get_attachment_url($critique_id);
							$special_award 	= get_post_meta($id, 'special_award', true);
							?>
							<tr id="routine-<?php echo $id; ?>">	
								<td style="text-align: center;"><?php echo $number; ?></td>
								<td><?php echo $name; ?></td>
								<td style="text-align: center;"><?php echo $score; ?></td>
								<td style="text-align: center;"><?php echo $adjudicated; ?></td>
								<td style="text-align: center;"><?php echo $category_hs; ?></td>
								<td style="text-align: center;"><?php echo $overall_hs; ?></td>
								<td style="text-align: center;"><?php echo $special_award; ?></td>
								<td><a href="<?php echo $critique_url; ?>" target="_blank"><?php echo $critique_file; ?></a></td>
							</tr>	
							<?php
						} ?>
					</tbody>
				</table>
				<?php
				$special_awards = get_post_meta($tour_id, 'special_awards', true);
				$studio_innovator = isset($special_awards['studio_innovator']) ? $special_awards['studio_innovator'] : '';
				if($studio_innovator==get_field('studio', 'user_'. $user_id)) {
					?>
					<h3>Studio Innovator: <strong>Won</strong></h3>
					<?php
				} 

				$scholarships = get_post_meta($tour_id, 'scholarships', true);
				if(! empty($scholarships)) {
					$args = array(
						'post__in' => array_keys($scholarships),
					);
					if(current_user_can('is_studio')) {
						$post_type = 'ts_studio_roster';
					}
					else if(current_user_can('is_individual')){
						$post_type = 'ts_sibling';
					}
					$scholars = ts_get_user_posts($post_type, -1, $user_id, $args);
					?>
					<h3>Scholarships:</h3>
					<div class="scholarships-container">
						<table class="ts-data-table" data-length="-1" data-dom="frt<'table-footer clearfix'p>">
							<thead>
								<tr>
									<th>Name</th>
									<th style="text-align: center;">Age Division</th>
									<th style="text-align: center;">Scholarship Number</th>
									<th style="text-align: center;">Scholarship</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($scholars as $s) {
									$id = $s->ID;
									$name = get_the_title($id);
									$agediv = ts_participant_agediv($id);
									$number = get_post_meta($id, 'number', true);
									$scholarship = get_post_meta($id, 'scholarship', true);
									?>
									<tr id="item-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
										<td><?php echo $name; ?></td>
										<td style="text-align: center;"><?php echo $agediv; ?></td>
										<td style="text-align: center;"><?php echo $number; ?></td>
										<td style="text-align: center;"><?php echo $scholarship; ?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>	
				<?php
				}
			} ?>	
		</div>
	</div>
	<?php	
}

function ts_results_preview() {
	?>
	<div id="results-page" class="wrap">
		<h1 class="admin-page-title"><?php echo get_admin_page_title(); ?></h1>
		<div class="ts-admin-wrapper results-wrapper">
			<?php ts_display_results_frontend(); ?>
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

function ts_post_pay_invoice_page() {
	?>
	<div id="post-pay-invoice-page" class="wrap">
		<?php echo do_shortcode('[ts-pay-invoice-form]'); ?>
	</div>
	<?php
}