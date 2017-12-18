<?php
function ts_pre_save_schedule( $schedule_id ){

	if(isset( $_POST['acf']['field_59ce6df7ae6eb'] ) || isset( $_POST['acf']['field_59d2697cc385f'] )) {

		if( isset( $_POST['acf']['field_59d2697cc385f'] ) ) {
			$city_id = $_POST['acf']['field_59d2697cc385f'];
			$status = $_POST['acf']['field_59e474d5debed'];
			$redirect_url = admin_url('admin.php?page=ts-edit-competition-schedule');
			$term = 'Competition';
		} 
		else {
			$city_id = $_POST['acf']['field_59ce6df7ae6eb'];
			$status = $_POST['acf']['field_59e474d5debee'];
			$redirect_url = admin_url('admin.php?page=ts-edit-workshop-schedule');
			$term = 'Workshop';
		}

		$post_status = $status==1 ? 'publish' : 'draft';

		$schedule = array(
			'post_status'  => $post_status,
			'post_title'  => get_the_title($city_id),
			'post_type'  => 'ts_event',
		);

		if( $schedule_id != 'new_schedule' ){
			$schedule['ID'] = $schedule_id;
			wp_update_post($schedule);
			return $schedule_id;
		}
		$schedule_id = wp_insert_post($schedule);
		
		wp_set_object_terms( $schedule_id, $term, 'ts_schedules_type' );
		
		do_action('acf/save_post', $schedule_id);

		wp_redirect(add_query_arg( array(
			'schedule_id' => $schedule_id,
			'tour' => $city_id,
		), $redirect_url));
		exit;
	}
}

function ts_update_schedule($schedule_id) {
	
	$post_type = get_post_type($schedule_id);

	if($post_type=='ts_event') {
		$schedules_type = wp_get_object_terms($schedule_id, 'ts_schedules_type');
		if($schedules_type[0]->name=='Competition'){
			$schedules = get_field('competition_event_schedules', $schedule_id);
			foreach ($schedules as $s) {
				$lineup = $s['lineup'];
				foreach ($lineup as $l) {
					if($l['action']=='Normal') {
						update_post_meta($l['routine'], 'routine_number', $l['number']);
					}
				}
			}
			update_post_meta($schedule_id, 'schedule_saved', true);
			$tour_id = get_post_meta($schedule_id, 'event_city', true);
			ts_generate_tour_music_zip( $schedules, $tour_id );
		}	
	}
}

function ts_change_tour_order( $args ) {
	$args['meta_key'] = 'date_from';
	$args['meta_type'] = 'DATE';
	$args['orderby'] = 'meta_value';
	$args['order'] = 'ASC';
	return $args;
}

function ts_load_sched_status( $value, $post_id, $field ) {

	$post_status = get_post_status($post_id);
    $value = $post_status == 'publish' ? 1 : 0; 

    return $value;
}

function ts_load_tour_city( $value, $post_id, $field ) {

	$tour_id = $_GET['tour'];
	if (isset($tour_id) && $tour_id != '') {
		$value = $tour_id;
	}
    return $value;
}

function ts_load_schedules( $value, $post_id, $field ) {

	$schedule_id = ts_get_param('schedule_id');
	$tour_id = ts_get_param('tour');

	if($tour_id) {
		$schedule_saved = get_post_meta($schedule_id, 'schedule_saved', true);
		$tour_date = get_post_meta($tour_id, 'date_from', true);
		$categories = ts_get_competition_categories();
		$genres = ts_get_routine_genres();
		if(! $schedule_saved){
		    $args = array(
		        'include' => ts_tour_routines_ids($tour_id),
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
		        /*'orderby' => 'meta_value_num',
				'meta_key' => 'agediv_order',
		        'order' => 'ASC',*/
		    );
		    $routines = ts_get_posts('ts_routine',-1,$args);
			if($routines){
				$count = 0;
				$day1 = array();
				$strtotime1 = strtotime($tour_date . '+17 hours');
				$timeday1 = date('F j, Y h:i a', $strtotime1);

				foreach ($routines as $r) {
					$count++;
					$id = $r->ID;
					$studio = ts_post_studio($id);
					$author_role = ts_post_author_role($id);
					if($studio=='' && $author_role=='individual') {
						$studio = 'Independent';
					}
					$agediv = get_post_meta($id, 'agediv', true);
					$cat = get_post_meta($id, 'cat', true);
					$cat_name = $categories[$cat]['title'];
					$genre = get_post_meta($id, 'genre', true);
					$genre_name = $genres[$genre]['title'];
					$time_limit = $categories[$cat]['time_limit'];

					$time_start1 = $strtotime1;
					$time_end1 = $strtotime1+$time_limit;
					$strtotime1 = $time_end1;
					$day1[] = array(
					    'field_59d2674f9703c' => $count,
					    'field_59d2674f973fa' => date('h:i a', $time_start1),
					    'field_5a0aecd9b6bb4' => date('h:i a', $time_end1),
					    'field_59d2674f977de' => $studio,
					    'field_59d2674f97bd8' => $id,
					    'field_59d2674f97fbb' => $agediv,
					    'field_59d2674f9839c' => $cat_name,
					    'field_59d2674f9878a' => $genre_name,
					    'field_59d2674f98ba4' => 'Normal',
					);
				}

				$newvalue = array(
					array(
						'field_59d2674f77b98' => $timeday1,
						'field_59d2674f77f7b' => $day1,
					),
				);
			}
		}
		else {
			$newvalue = $value;
			/*$count = 0;
			foreach ($value as $a => $b) {
				foreach ($lineup as $c => $d) {
					if($d['field_59d2674f98ba4']=='Normal') {
						$count++;
						$newvalue[$a]['field_59d2674f77f7b'][$c]['field_59d2674f9703c'] = $count;
					}	
				}
			}*/			
		}
		$value = $newvalue;
	}
    return $value;
}

function ts_calculate_overall_score( $score_id ) {
  if( empty($_POST['acf']) ) {
      return;
  }
	if( have_rows('field_19d2674b099e9', $score_id) ) {
		while( have_rows('field_19d2674b099e9', $score_id) ) {
		  the_row();
		  if( have_rows('field_19d2674f77f7b', $score_id) ) {
		      while( have_rows('field_19d2674f77f7b', $score_id) ) {
		          the_row();
		          $total_score = 0;
		          $judge_1_score = (int) get_sub_field('field_89e4b7c4a3479', $score_id);
		          $judge_2_score = (int) get_sub_field('field_79e4b7c4a3479', $score_id);
		          $judge_3_score = (int) get_sub_field('field_69e4b7c4a3479', $score_id);
		          $total_score = $judge_1_score+$judge_2_score+$judge_3_score;
		          update_sub_field('field_19e4b7c4a3479', $total_score);
		          $routine_id = (int)get_sub_field('field_19d2674f97bd8', $score_id);
		          update_post_meta($routine_id, 'judges_scores', array($judge_1_score,$judge_2_score,$judge_3_score));
		          update_post_meta($routine_id, 'total_score', $total_score);
		      }
		  }
		}
	}
}

function ts_competition_schedule_updated( $schedule_id ) {

	if( 'publish' === get_post_status( $schedule_id ) ) {
		$schedules	= get_field('competition_event_schedules', $schedule_id);
		$scores_array = ts_create_scores_array( $schedules );

		$tour_id	= get_post_meta($schedule_id, 'event_city', true);
		$args = array(
			'post_status' => array('publish'),
			'meta_query' => array(
				array(
					'key' => 'event_city',
					'value' => $tour_id
				),
			)
		);
		$scores = ts_get_posts('ts_score', 1, $args);
		if( $scores ) {
			foreach( $scores as $score ) {
				setup_postdata($score);
				$score_id = $score->ID;
				update_field('event_city',$tour_id, $score_id);
				update_field('tour_scores',$scores_array, $score_id);
			}
		} else {
			$score = array(
				'post_status'  => 'publish' ,
				'post_title'  => get_the_title($tour_id),
				'post_type'  => 'ts_score',
			);

			$score_id = wp_insert_post($score);
			if( $score_id and !is_wp_error($score_id) ) {
				update_field('event_city',$tour_id,$score_id);
				update_field('tour_scores',$scores_array, $score_id);
			}
		}
		ts_generate_tour_music_zip( $schedules, $tour_id );
	}
}

