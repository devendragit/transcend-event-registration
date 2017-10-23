<?php
function ts_display_results() {

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
	if($tour_id && (current_user_can('is_organizer') || $status=='publish')) { ?>
		<h3>Adjudicated Awards:</h3>
		<div class="adjudicated-container">
			<?php
			$routine_ids = ts_tour_routines_ids($tour_id);

			if(! empty($routine_ids)) {
				$args = array(
					'include' => $routine_ids,
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
							?>
							<div class="row" id="routine-<?php echo $id; ?>">
								<div class="col-md-2"><?php echo $id; ?></div>
								<div class="col-md-3">Routine Name</div>
								<div class="col-md-3">Studio Name</div>
								<div class="col-md-4">Platinum</div>
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
		<div class="category-container">
			<div class="row">
				<?php
				$hiscore_solo_mini = ts_hiscore_solo_mini();
				if(! empty($hiscore_solo_mini)){
					?>
					<div class="col-md-6">
						<h4>Solo - Mini</h4>
						<?php ts_display_awards_table($hiscore_solo_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_junior = ts_hiscore_solo_junior();
				if(! empty($ts_hiscore_solo_junior)){
					?>
					<div class="col-md-6">
						<h4>Solo - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_solo_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_teen = ts_hiscore_solo_teen();
				if(! empty($ts_hiscore_solo_teen)){
					?>
					<div class="col-md-6">
						<h4>Solo - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_solo_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_senior = ts_hiscore_solo_senior();
				if(! empty($ts_hiscore_solo_senior)){
					?>
					<div class="col-md-6">
						<h4>Solo - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_solo_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_solo_pro = ts_hiscore_solo_pro();
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
			<div class="row">
				<?php
				$hiscore_duotrio_mini = ts_hiscore_duotrio_mini();
				if(! empty($hiscore_duotrio_mini)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Mini</h4>
						<?php ts_display_awards_table($hiscore_duotrio_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_junior = ts_hiscore_duotrio_junior();
				if(! empty($ts_hiscore_duotrio_junior)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_duotrio_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_teen = ts_hiscore_duotrio_teen();
				if(! empty($ts_hiscore_duotrio_teen)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_duotrio_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_senior = ts_hiscore_duotrio_senior();
				if(! empty($ts_hiscore_duotrio_senior)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_duotrio_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_duotrio_pro = ts_hiscore_duotrio_pro();
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
			<div class="row">
				<?php
				$hiscore_smallgroup_mini = ts_hiscore_smallgroup_mini();
				if(! empty($hiscore_smallgroup_mini)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Mini</h4>
						<?php ts_display_awards_table($hiscore_smallgroup_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_junior = ts_hiscore_smallgroup_junior();
				if(! empty($ts_hiscore_smallgroup_junior)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_smallgroup_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_teen = ts_hiscore_smallgroup_teen();
				if(! empty($ts_hiscore_smallgroup_teen)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_smallgroup_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_senior = ts_hiscore_smallgroup_senior();
				if(! empty($ts_hiscore_smallgroup_senior)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_smallgroup_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_smallgroup_pro = ts_hiscore_smallgroup_pro();
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
			<div class="row">
				<?php
				$hiscore_largegroup_mini = ts_hiscore_largegroup_mini();
				if(! empty($hiscore_largegroup_mini)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Mini</h4>
						<?php ts_display_awards_table($hiscore_largegroup_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_junior = ts_hiscore_largegroup_junior();
				if(! empty($ts_hiscore_largegroup_junior)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_largegroup_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_teen = ts_hiscore_largegroup_teen();
				if(! empty($ts_hiscore_largegroup_teen)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_largegroup_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_senior = ts_hiscore_largegroup_senior();
				if(! empty($ts_hiscore_largegroup_senior)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_largegroup_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_largegroup_pro = ts_hiscore_largegroup_pro();
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
			<div class="row">
				<?php
				$hiscore_line_mini = ts_hiscore_line_mini();
				if(! empty($hiscore_line_mini)){
					?>
					<div class="col-md-6">
						<h4>Line - Mini</h4>
						<?php ts_display_awards_table($hiscore_line_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_junior = ts_hiscore_line_junior();
				if(! empty($ts_hiscore_line_junior)){
					?>
					<div class="col-md-6">
						<h4>Line - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_line_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_teen = ts_hiscore_line_teen();
				if(! empty($ts_hiscore_line_teen)){
					?>
					<div class="col-md-6">
						<h4>Line - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_line_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_senior = ts_hiscore_line_senior();
				if(! empty($ts_hiscore_line_senior)){
					?>
					<div class="col-md-6">
						<h4>Line - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_line_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_line_pro = ts_hiscore_line_pro();
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
			<div class="row">
				<?php
				$hiscore_production_mini = ts_hiscore_production_mini();
				if(! empty($hiscore_production_mini)){
					?>
					<div class="col-md-6">
						<h4>Production - Mini</h4>
						<?php ts_display_awards_table($hiscore_production_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_junior = ts_hiscore_production_junior();
				if(! empty($ts_hiscore_production_junior)){
					?>
					<div class="col-md-6">
						<h4>Production - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_production_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_teen = ts_hiscore_production_teen();
				if(! empty($ts_hiscore_production_teen)){
					?>
					<div class="col-md-6">
						<h4>Production - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_production_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_senior = ts_hiscore_production_senior();
				if(! empty($ts_hiscore_production_senior)){
					?>
					<div class="col-md-6">
						<h4>Production - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_production_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_production_pro = ts_hiscore_production_pro();
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
				$hiscore_overall_mini = ts_hiscore_overall_mini();
				if(! empty($hiscore_overall_mini)){
					?>
					<div class="col-md-6">
						<h4>Overall - Mini</h4>
						<?php ts_display_awards_table($hiscore_overall_mini); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_junior = ts_hiscore_overall_junior();
				if(! empty($ts_hiscore_overall_junior)){
					?>
					<div class="col-md-6">
						<h4>Overall - Junior</h4>
						<?php ts_display_awards_table($ts_hiscore_overall_junior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_teen = ts_hiscore_overall_teen();
				if(! empty($ts_hiscore_overall_teen)){
					?>
					<div class="col-md-6">
						<h4>Overall - Teen</h4>
						<?php ts_display_awards_table($ts_hiscore_overall_teen); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_senior = ts_hiscore_overall_senior();
				if(! empty($ts_hiscore_overall_senior)){
					?>
					<div class="col-md-6">
						<h4>Overall - Senior</h4>
						<?php ts_display_awards_table($ts_hiscore_overall_senior); ?>
					</div>
					<?php
				} 
				$ts_hiscore_overall_pro = ts_hiscore_overall_pro();
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
			$awards = get_post_meta($tour_id, 'special_awards', true);
			//print_r($awards);
			?>
		</div>
	<?php
	}
}

function ts_hiscore_solo_mini() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_solo_junior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_solo_teen() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_solo_senior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_solo_pro() {

	$winners = ts_winners_placeholder();

	return $winners;
}

/************************/

function ts_hiscore_duotrio_mini() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_duotrio_junior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_duotrio_teen() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_duotrio_senior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_duotrio_pro() {

	$winners = ts_winners_placeholder();

	return $winners;
}

/************************/

function ts_hiscore_smallgroup_mini() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_smallgroup_junior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_smallgroup_teen() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_smallgroup_senior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_smallgroup_pro() {

	$winners = ts_winners_placeholder();

	return $winners;
}

/************************/

function ts_hiscore_largegroup_mini() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_largegroup_junior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_largegroup_teen() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_largegroup_senior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_largegroup_pro() {

	$winners = ts_winners_placeholder();

	return $winners;
}

/************************/

function ts_hiscore_line_mini() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_line_junior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_line_teen() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_line_senior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_line_pro() {

	$winners = ts_winners_placeholder();

	return $winners;
}

/************************/

function ts_hiscore_production_mini() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_production_junior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_production_teen() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_production_senior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_production_pro() {

	$winners = ts_winners_placeholder();

	return $winners;
}

/************************/

function ts_hiscore_overall_mini() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_overall_junior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_overall_teen() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_overall_senior() {

	$winners = ts_winners_placeholder();

	return $winners;
}

function ts_hiscore_overall_pro() {

	$winners = ts_winners_placeholder();

	return $winners;
}