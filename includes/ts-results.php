<?php
function ts_display_results() {

	wp_enqueue_style('jquery-ui-css');
	$status = 'draft';

	if(isset($_GET['tour']) && $_GET['tour']!='') {
		$tour_id = $_GET['tour'];
		$status = get_post_meta($tour_id, 'results_status', true);
	}	
	if(is_admin()) {
		$base_url = admin_url('admin.php?page=ts-results&');
	}
	else {
		$base_url = get_permalink() .'?';
	}
	?>
	<p><select name="tour_city" class="select-redirect">
		<option value="">Select City</option>
		<?php
		$args = array(
			'meta_key' => 'date_from',
			'meta_type' => 'DATE',
			'orderby' => 'meta_value',
			'order' => 'ASC',
		);
		$tour_cities = ts_get_posts('ts_tour', -1, $args);
		if($tour_cities) {
			$count=0;
			foreach ($tour_cities as $ct) {
				$count++;
				setup_postdata($ct);
				$ct_id 		= $ct->ID;
				$title 		= get_the_title($ct_id);
				$selected 	= $tour_id == $ct_id ? 'selected' : '';
				?>
				<option <?php echo $selected; ?> value="<?php echo $ct_id; ?>" data-url="<?php echo $base_url . 'tour=' . $ct_id; ?>" ><?php echo $title; ?></option>
				<?php
			}
		}
		?>
	</select></p>
	<?php
	if($tour_id && (current_user_can('is_organizer') || $status=='publish')) { 
		?>
		<h3>Adjudicated Awards:</h3>
		<div class="adjudicated-container">
			<?php
			$routine_ids = ts_tour_routines_ids($tour_id);

			if(! empty($routine_ids)) {
				$args = array(
					'include' => $routine_ids,
			        'orderby' => 'meta_value_num',
					'meta_key' => 'routine_number',
			        'order' => 'ASC',
				);
				$routines = ts_get_posts('ts_routine', -1, $args);
				?>
				<div class="table-container table-pad">
					<div class="row table-head">
						<div class="col-md-2">#</div>
						<div class="col-md-3">Name</div>
						<div class="col-md-3">Studio</div>
						<div class="col-md-4">Award</div>
					</div>
					<div class="table-body">
						<?php
						foreach ($routines as $r) { 
							$id = $r->ID;
							$number = get_post_meta($id, 'routine_number', true);
							$score = get_post_meta($id, 'total_score', true);
							$name = get_the_title($id);
							$studio = ts_post_studio($id);
							?>
							<div class="row" id="routine-<?php echo $id; ?>">
								<div class="col-md-2"><?php echo $number; ?></div>
								<div class="col-md-3"><?php echo $name; ?></div>
								<div class="col-md-3"><?php echo $studio; ?></div>
								<div class="col-md-4"><?php echo ts_adjudicated_award($score); ?></div>
							</div>
							<?php
						} ?>
					</div>
				</div>
				<?php
			}	
			?>
		</div>	
		<h3>Category High Scores:</h3>
		<div class="category-container ts-tabs">
			<ul>
				<li><a href="#tabs-1">Solo</a></li>
				<li><a href="#tabs-2">Duo/Trio</a></li>
				<li><a href="#tabs-3">Small Group</a></li>
				<li><a href="#tabs-4">Large Group</a></li>
				<li><a href="#tabs-5">Line</a></li>
				<li><a href="#tabs-6">Production</a></li>
			</ul>			
			<div class="row" id="tabs-1">
				<?php
				$hiscore_solo_mini = ts_hiscore_solo_mini($tour_id);
				if(! empty($hiscore_solo_mini)){
					?>
					<div class="col-md-6">
						<h4>Solo - Mini</h4>
						<?php ts_display_awards_table($hiscore_solo_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_junior = ts_hiscore_solo_junior($tour_id);
				if(! empty($ts_hiscore_solo_junior)){
					?>
					<div class="col-md-6">
						<h4>Solo - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_solo_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_teen = ts_hiscore_solo_teen($tour_id);
				if(! empty($ts_hiscore_solo_teen)){
					?>
					<div class="col-md-6">
						<h4>Solo - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_solo_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_senior = ts_hiscore_solo_senior($tour_id);
				if(! empty($ts_hiscore_solo_senior)){
					?>
					<div class="col-md-6">
						<h4>Solo - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_solo_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_pro = ts_hiscore_solo_pro($tour_id);
				if(! empty($ts_hiscore_solo_pro)){
					?>
					<div class="col-md-6">
						<h4>Solo - Pro</h4>
						<?php ts_display_awards_table($ts_hiscore_solo_pro); ?>
					</div>
					<?php
				} 
				?>
			</div>	
			<div class="row" id="tabs-2">
				<?php
				$hiscore_duotrio_mini = ts_hiscore_duotrio_mini($tour_id);
				if(! empty($hiscore_duotrio_mini)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Mini</h4>
						<?php ts_display_awards_table($hiscore_duotrio_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_junior = ts_hiscore_duotrio_junior($tour_id);
				if(! empty($ts_hiscore_duotrio_junior)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_duotrio_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_teen = ts_hiscore_duotrio_teen($tour_id);
				if(! empty($ts_hiscore_duotrio_teen)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_duotrio_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_senior = ts_hiscore_duotrio_senior($tour_id);
				if(! empty($ts_hiscore_duotrio_senior)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_duotrio_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_pro = ts_hiscore_duotrio_pro($tour_id);
				if(! empty($ts_hiscore_duotrio_pro)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Pro</h4>
						<?php ts_display_awards_table($ts_hiscore_duotrio_pro); ?>
					</div>
					<?php
				} 
				?>
			</div>	
			<div class="row" id="tabs-3">
				<?php
				$hiscore_smallgroup_mini = ts_hiscore_smallgroup_mini($tour_id);
				if(! empty($hiscore_smallgroup_mini)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Mini</h4>
						<?php ts_display_awards_table($hiscore_smallgroup_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_junior = ts_hiscore_smallgroup_junior($tour_id);
				if(! empty($ts_hiscore_smallgroup_junior)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_smallgroup_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_teen = ts_hiscore_smallgroup_teen($tour_id);
				if(! empty($ts_hiscore_smallgroup_teen)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_smallgroup_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_senior = ts_hiscore_smallgroup_senior($tour_id);
				if(! empty($ts_hiscore_smallgroup_senior)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_smallgroup_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_pro = ts_hiscore_smallgroup_pro($tour_id);
				if(! empty($ts_hiscore_smallgroup_pro)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Pro</h4>
						<?php ts_display_awards_table($ts_hiscore_smallgroup_pro); ?>
					</div>
					<?php
				} 
				?>
			</div>	
			<div class="row" id="tabs-4">
				<?php
				$hiscore_largegroup_mini = ts_hiscore_largegroup_mini($tour_id);
				if(! empty($hiscore_largegroup_mini)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Mini</h4>
						<?php ts_display_awards_table($hiscore_largegroup_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_junior = ts_hiscore_largegroup_junior($tour_id);
				if(! empty($ts_hiscore_largegroup_junior)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_largegroup_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_teen = ts_hiscore_largegroup_teen($tour_id);
				if(! empty($ts_hiscore_largegroup_teen)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_largegroup_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_senior = ts_hiscore_largegroup_senior($tour_id);
				if(! empty($ts_hiscore_largegroup_senior)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_largegroup_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_pro = ts_hiscore_largegroup_pro($tour_id);
				if(! empty($ts_hiscore_largegroup_pro)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Pro</h4>
						<?php ts_display_awards_table($ts_hiscore_largegroup_pro); ?>
					</div>
					<?php
				} 
				?>
			</div>
			<div class="row" id="tabs-5">
				<?php
				$hiscore_line_mini = ts_hiscore_line_mini($tour_id);
				if(! empty($hiscore_line_mini)){
					?>
					<div class="col-md-6">
						<h4>Line - Mini</h4>
						<?php ts_display_awards_table($hiscore_line_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_junior = ts_hiscore_line_junior($tour_id);
				if(! empty($ts_hiscore_line_junior)){
					?>
					<div class="col-md-6">
						<h4>Line - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_line_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_teen = ts_hiscore_line_teen($tour_id);
				if(! empty($ts_hiscore_line_teen)){
					?>
					<div class="col-md-6">
						<h4>Line - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_line_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_senior = ts_hiscore_line_senior($tour_id);
				if(! empty($ts_hiscore_line_senior)){
					?>
					<div class="col-md-6">
						<h4>Line - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_line_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_pro = ts_hiscore_line_pro($tour_id);
				if(! empty($ts_hiscore_line_pro)){
					?>
					<div class="col-md-6">
						<h4>Line - Pro</h4>
						<?php ts_display_awards_table($ts_hiscore_line_pro); ?>
					</div>
					<?php
				} 
				?>
			</div>	
			<div class="row" id="tabs-6">
				<?php
				$hiscore_production_mini = ts_hiscore_production_mini($tour_id);
				if(! empty($hiscore_production_mini)){
					?>
					<div class="col-md-6">
						<h4>Production - Mini</h4>
						<?php ts_display_awards_table($hiscore_production_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_junior = ts_hiscore_production_junior($tour_id);
				if(! empty($ts_hiscore_production_junior)){
					?>
					<div class="col-md-6">
						<h4>Production - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_production_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_teen = ts_hiscore_production_teen($tour_id);
				if(! empty($ts_hiscore_production_teen)){
					?>
					<div class="col-md-6">
						<h4>Production - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_production_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_senior = ts_hiscore_production_senior($tour_id);
				if(! empty($ts_hiscore_production_senior)){
					?>
					<div class="col-md-6">
						<h4>Production - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_production_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_pro = ts_hiscore_production_pro($tour_id);
				if(! empty($ts_hiscore_production_pro)){
					?>
					<div class="col-md-6">
						<h4>Production - Pro</h4>
						<?php ts_display_awards_table($ts_hiscore_production_pro); ?>
					</div>
					<?php
				} 
				?>
			</div>													
		</div>
		<h3>Overall High Scores:</h3>
		<div class="overall-container">
			<div class="row">
				<?php
				$hiscore_overall_mini = ts_hiscore_overall_mini($tour_id);
				if(! empty($hiscore_overall_mini)){
					?>
					<div class="col-md-6">
						<h4>Overall - Mini</h4>
						<?php ts_display_awards_table($hiscore_overall_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_junior = ts_hiscore_overall_junior($tour_id);
				if(! empty($ts_hiscore_overall_junior)){
					?>
					<div class="col-md-6">
						<h4>Overall - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_overall_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_teen = ts_hiscore_overall_teen($tour_id);
				if(! empty($ts_hiscore_overall_teen)){
					?>
					<div class="col-md-6">
						<h4>Overall - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_overall_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_senior = ts_hiscore_overall_senior($tour_id);
				if(! empty($ts_hiscore_overall_senior)){
					?>
					<div class="col-md-6">
						<h4>Overall - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_overall_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_pro = ts_hiscore_overall_pro($tour_id);
				if(! empty($ts_hiscore_overall_pro)){
					?>
					<div class="col-md-6">
						<h4>Overall - Pro</h4>
						<?php ts_display_awards_table($ts_hiscore_overall_pro); ?>
					</div>
					<?php
				} 
				?>
			</div>	
		</div>	
		<h3>Special Awards:</h3>
		<div class="awards-container">
			<?php
			$special_awards = get_post_meta($tour_id, 'special_awards', true);
			$scholarships = get_post_meta($tour_id, 'scholarships', true);
			?>
			<h4>(for all 12 and under)</h4>
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
						<div class="col-md-2 t-center"><?php echo $special_awards['twelve_below']['choreography']['routine_number']; ?></div>
						<div class="col-md-3 t-center routine-name"><?php echo get_the_title($special_awards['twelve_below']['choreography']['routine_id']);?></div>
						<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($special_awards['twelve_below']['choreography']['routine_id']);?></div>
					</div>
					<div class="row" id="item-2">
						<div class="col-md-4">Judges Standout Nominee:</div>
						<div class="col-md-2 t-center"><?php echo $special_awards['twelve_below']['standout_nominee']['routine_number']; ?></div>
						<div class="col-md-3 t-center routine-name"><?php echo get_the_title($special_awards['twelve_below']['standout_nominee']['routine_id']);?></div>
						<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($special_awards['twelve_below']['standout_nominee']['routine_id']);?></div>
					</div>
					<div class="row" id="item-3">
						<div class="col-md-4">Judges Standout Winner:</div>
						<div class="col-md-2 t-center"><?php echo $special_awards['twelve_below']['standout_winner']['routine_number']; ?></div>
						<div class="col-md-3 t-center routine-name"><?php echo get_the_title($special_awards['twelve_below']['standout_winner']['routine_id']);?></div>
						<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($special_awards['twelve_below']['standout_winner']['routine_id']);?></div>
					</div>
				</div>	
			</div>
			<h4>(for all 13 and above)</h4>
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
						<div class="col-md-2 t-center"><?php echo $special_awards['thirteen_above']['choreography']['routine_number']; ?></div>
						<div class="col-md-3 t-center routine-name"><?php echo get_the_title($special_awards['thirteen_above']['choreography']['routine_id']);?></div>
						<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($special_awards['thirteen_above']['choreography']['routine_id']);?></div>
					</div>
					<div class="row" id="item-5">
						<div class="col-md-4">Judges Standout Nominee:</div>
						<div class="col-md-2 t-center"><?php echo $special_awards['thirteen_above']['standout_nominee']['routine_number']; ?></div>
						<div class="col-md-3 t-center routine-name"><?php echo get_the_title($special_awards['thirteen_above']['standout_nominee']['routine_id']);?></div>
						<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($special_awards['thirteen_above']['standout_nominee']['routine_id']);?></div>
					</div>
					<div class="row" id="item-6">
						<div class="col-md-4">Judges Standout Winner:</div>
						<div class="col-md-2 t-center"><?php echo $special_awards['thirteen_above']['standout_winner']['routine_number']; ?></div>
						<div class="col-md-3 t-center routine-name"><?php echo get_the_title($special_awards['thirteen_above']['standout_winner']['routine_id']);?></div>
						<div class="col-md-3 t-center routine-studio"><?php echo ts_post_studio($special_awards['thirteen_above']['standout_winner']['routine_id']);?></div>
					</div>
				</div>	
			</div>
			<h4>Studio Innovator:</h4>
			<div class="table-container">
				<div class="table-body">
					<div class="row">
						<div class="col-md-4">Studio Name:</div>
						<div class="col-md-8"><?php echo $special_awards['studio_innovator']; ?></div>
					</div>		
				</div>	
			</div>
		</div>
		<h3>Scholarships:</h3>
		<div class="scholarships-container">
			<div class="table-container scholarship-wrapper">
				<?php
				$scholarships = get_post_meta($tour_id, 'scholarships', true);
				$participants = ts_tour_participants($tour_id);
				if(! empty($scholarships)) {
					?>
					<div class="row table-head">
						<div class="col-sm-3"><strong>Name</strong></div>
						<div class="col-sm-2"><strong>Age Division</strong></div>
						<div class="col-sm-2"><strong>Studio</strong></div>
						<div class="col-sm-3"><strong>Scholarship</strong></div>
					</div>
					<div class="scholarship-container table-body">
						<?php
						foreach ($scholarships as $key=>$val) {
							$id = $key;
							if($val=='') continue;
							?>
							<div class="row" id="item-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
								<div class="col-sm-3"><?php echo get_the_title($id); ?></div>
								<div class="col-sm-2 age-division"><?php echo ts_participant_agediv($id); ?></div>
								<div class="col-sm-2 studio-name"><?php echo ts_post_studio($id); ?></div>
								<div class="col-sm-3 participant-scholarship"><?php echo $val; ?></div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>	
	<?php
	}
}

function ts_hiscore_solo_mini($tour_id) {
	$winners = ts_winners_array($tour_id, 'Mini', 1);
    return $winners;
}

function ts_hiscore_solo_junior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Junior', 1);
    return $winners;
}

function ts_hiscore_solo_teen($tour_id) {
	$winners = ts_winners_array($tour_id, 'Teen', 1);
    return $winners;
}

function ts_hiscore_solo_senior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Senior', 1);
    return $winners;
}

function ts_hiscore_solo_pro($tour_id) {
	$winners = ts_winners_array($tour_id, 'Pro', 1);
    return $winners;
}

/************************/

function ts_hiscore_duotrio_mini($tour_id) {
	$winners = ts_winners_array($tour_id, 'Mini', 2);
}

function ts_hiscore_duotrio_junior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Junior', 2);
    return $winners;
}

function ts_hiscore_duotrio_teen($tour_id) {
	$winners = ts_winners_array($tour_id, 'Teen', 2);
    return $winners;
}

function ts_hiscore_duotrio_senior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Senior', 2);
    return $winners;
}

function ts_hiscore_duotrio_pro($tour_id) {
	$winners = ts_winners_array($tour_id, 'Pro', 2);
    return $winners;
}

/************************/

function ts_hiscore_smallgroup_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 3, 3);
    return $winners;
}

/************************/

function ts_hiscore_largegroup_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 4, 3);
    return $winners;
}

/************************/

function ts_hiscore_line_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 5, 3);
    return $winners;
}

function ts_hiscore_line_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 5, 3);
    return $winners;
}

function ts_hiscore_line_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 5, 3);
    return $winners;
}

function ts_hiscore_line_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 5, 3);
    return $winners;
}

function ts_hiscore_line_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 5, 3);
    return $winners;
}

/************************/

function ts_hiscore_production_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 6, 3);
    return $winners;
}

function ts_hiscore_production_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 6, 3);
    return $winners;
}

function ts_hiscore_production_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 6, 3);
    return $winners;
}

function ts_hiscore_production_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 6, 3);
    return $winners;
}

function ts_hiscore_production_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 6, 3);
    return $winners;
}

/************************/

function ts_hiscore_overall_mini($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Mini');
    return $winners;
}

function ts_hiscore_overall_junior($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Junior');
    return $winners;
}

function ts_hiscore_overall_teen($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Teen');
    return $winners;
}

function ts_hiscore_overall_senior($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Senior');
    return $winners;
}

function ts_hiscore_overall_pro($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Pro');
    return $winners;
}