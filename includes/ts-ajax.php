<?php
function ajax_studio_registration() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$tab 		   = $_POST['tab'];
		$next_step 	   = $_POST['next_step'];
		$entry_id 	   = $_POST['entry_id'];
		$eid 	 	   = $_POST['eid'];
		$profile 	   = $_POST['profile'];
		$rosternew 	   = $_POST['rosternew'];
		$rostercurr    = $_POST['rostercurr'];
		$rosteredit    = $_POST['rosteredit'];
		$workshop      = $_POST['workshop'];
		$competition   = $_POST['competition'];
		$routinecurr   = $_POST['routinecurr'];
		$routinenew    = $_POST['routinenew'];
		$payment  	   = $_POST['payment'];
		$discount_code = $_POST['discount_code'];
		$save 		   = $_POST['save_for_later'];
        $remaining        = absint($_POST['remaining']);
        $remaining_amount = absint($_POST['remaining_amount']);
        $amount_credited = absint($_POST['amount_credited']);

		$response = array(
			'success' => false,
			'redirect' => false, 
			'message' => array(),
		);

		$has_error = true;
		$completed = '';
		$comfirmed = false;

		$user_id 	= get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid);
		$temp_data 	= $entry_data;
		$curr_step 	= ((int)$next_step)-1;

		if($tab=='profile') {
			if($profile){
				$temp_data['profile'] = $profile;

				$studio 	= $profile['studio_name'];
				$director 	= $profile['studio_director'];
				$address 	= $profile['studio_address'];
				$city 		= $profile['studio_city'];
				$state 		= $profile['studio_state'];
				$zipcode 	= $profile['studio_zipcode'];
				$country 	= $profile['studio_country'];
				$phone 		= $profile['studio_phone'];
				$email 		= $profile['studio_email'];
				$cell 		= $profile['studio_cell'];
				$contact 	= $profile['studio_contact'];

				update_field('studio', $studio, 'user_'. $user_id);
				update_field('director', $director, 'user_'. $user_id);
				update_field('address', $address, 'user_'. $user_id);
				update_field('city', $city, 'user_'. $user_id);
				update_field('state', $state, 'user_'. $user_id);
				update_field('zipcode', $zipcode, 'user_'. $user_id);
				update_field('country', $country, 'user_'. $user_id);
				update_field('phone', $phone, 'user_'. $user_id);
				update_field('email', $email, 'user_'. $user_id);
				update_field('cell', $cell, 'user_'. $user_id);
				update_field('contact', $contact, 'user_'. $user_id);

				$args2 = array(
				    'ID'         => $user_id,
				    'user_email' => $email
				);
				wp_update_user( $args2 );				
			}
		}
		if($tab=='roster') {

			$participants = ts_check_value($temp_data, 'workshop', 'participants');
			$participantArray = array();

			if($rostercurr){
				foreach ($rostercurr as $rc) {

					$age_div = wp_get_object_terms($rc, 'ts_agediv');
					$fee = ts_get_workshop_fee($rc, 2, $eid);

					$newParticipant = array(
		            	'age_division' => $age_div[0]->term_id, 
		                'discount' => '',
		                'duration' => 1,
		                'fee' => $fee
					);
					$participantArray[$rc] = $newParticipant;
				}
			}
			
			if($rosteredit){
				foreach ($rosteredit as $key=>$re) {
					if(current_user_can('edit_studio_roster', $key)){

						$first_name 	= $re['first_name'];
						$last_name 		= $re['last_name'];
						$birth_date 	= $re['birth_date'];
						$roster_type 	= $re['roster_type'];
						$selected 		= $re['selected'];

						$rosterArgs = array(
							'ID' => (int)$key,
							'post_title' => $first_name .' '. $last_name,
							'post_type' => 'ts_studio_roster',
						);
						$newRoster = wp_update_post($rosterArgs, true);

						if($newRoster && !is_wp_error($newRoster)) {
							update_post_meta($newRoster, 'first_name', $first_name);
							update_post_meta($newRoster, 'last_name', $last_name);
							update_post_meta($newRoster, 'birth_date', $birth_date);

							wp_set_object_terms($newRoster, (int)$roster_type, 'ts_rostertype');
							ts_set_age_division($newRoster, $birth_date);

							if($selected){
								$age_div = wp_get_object_terms($newRoster, 'ts_agediv');
								$fee = ts_get_workshop_fee($newRoster, 2, $eid);

								$newParticipant = array(
					            	'age_division' => $age_div[0]->term_id, 
					                'discount' => '',
					                'duration' => 1,
					                'fee' => $fee
								);
								$participantArray[$newRoster] = $newParticipant;
							}
						}
					}
				}
			}

			if($rosternew) {
				foreach ($rosternew as $rn) {
					if($rn['first_name'] !=''){

						$first_name 	= $rn['first_name'];
						$last_name 		= $rn['last_name'];
						$birth_date 	= $rn['birth_date'];
						$roster_type 	= $rn['roster_type'];
						$selected 		= $rn['selected'];

						$rosterArgs = array(
							'post_title' => $first_name .' '. $last_name,
							'post_type' => 'ts_studio_roster',
							'post_status' => 'publish',
							'author' => $user_id
						);
						$newRoster = wp_insert_post($rosterArgs, true);

						if($newRoster && !is_wp_error($newRoster)) {
							update_post_meta($newRoster, 'first_name', $first_name);
							update_post_meta($newRoster, 'last_name', $last_name);
							update_post_meta($newRoster, 'birth_date', $birth_date);

							wp_set_object_terms($newRoster, (int)$roster_type, 'ts_rostertype');
							ts_set_age_division($newRoster, $birth_date);

							if($selected){
								$age_div = wp_get_object_terms($newRoster, 'ts_agediv');
								$fee = ts_get_workshop_fee($newRoster, 2, $eid);

								$newParticipant = array(
					            	'age_division' => $age_div[0]->term_id, 
					                'discount' => '',
					                'duration' => 1,
					                'fee' => $fee
								);
								$participantArray[$newRoster] = $newParticipant;
							}
						}
					}	
				}
			}
			$temp_data['workshop']['participants'] = $participantArray;
		}
		if($tab=='workshop') {
			if($workshop){

				if((! isset($workshop['tour_city']) || $workshop['tour_city']=='') || ts_is_paid($entry_id) || (isset($workshop['tour_city']) && get_post_meta($workshop['tour_city'], 'status', true)==2) ) {
					$workshop['tour_city'] = $temp_data['workshop']['tour_city'];
				}

				$temp_data['workshop'] = $workshop;

				$participants 		= ts_check_value($workshop, 'participants');
				$observer 			= ts_check_value($workshop, 'observers');
				$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');

				$countObservers 			= count($observer);
				$countMunchkinObservers 	= count($munchkin_observer);

				$countMunchkin  = 0;
				$countMinis 	= 0;
				$countJuniors 	= 0;
				$countTeens 	= 0;
				$countSeniors 	= 0;
				$countPros 		= 0;
				$countTeachers 	= 0;

				$Munchkin 	= get_term_by('name', 'Munchkin', 'ts_agediv');
				$Mini 		= get_term_by('name', 'Mini', 'ts_agediv');
				$Junior 	= get_term_by('name', 'Junior', 'ts_agediv');
				$Teen 		= get_term_by('name', 'Teen', 'ts_agediv');
				$Senior 	= get_term_by('name', 'Senior', 'ts_agediv');
				$Pro 		= get_term_by('name', 'Pro', 'ts_agediv');
				$Teacher 	= get_term_by('name', 'Teacher', 'ts_agediv');

				if(! empty($participants)) {
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
				}

				$temp_data['workshop']['count_munchkins'] 		 = $countMunchkin;
				$temp_data['workshop']['count_minis'] 			 = $countMinis;
				$temp_data['workshop']['count_juniors'] 		 = $countJuniors;
				$temp_data['workshop']['count_teens'] 			 = $countTeens;
				$temp_data['workshop']['count_seniors'] 		 = $countSeniors;
				$temp_data['workshop']['count_pros'] 			 = $countPros;
				$temp_data['workshop']['count_teachers'] 		 = $countTeachers;
				$temp_data['workshop']['count_observer'] 		 = $countObservers;
				$temp_data['workshop']['count_munchkinobserver'] = $countMunchkinObservers;

				$workshop_fee 			= ts_get_total_workshop_fee($eid, $temp_data);
				$teacher_discount 		= ts_get_total_teacher_discount($eid, $temp_data);
				$scholarship_discount 	= ts_get_total_scholarship_discount($eid, $temp_data);
				$discounted_total 		= ts_get_discounted_total_workshop_fee($eid, $temp_data);

				$temp_data['workshop']['total_fee'] 			 = $workshop_fee;
				$temp_data['workshop']['teacher_discount'] 		 = $teacher_discount;
				$temp_data['workshop']['scholarship_discount'] 	 = $scholarship_discount;
				$temp_data['workshop']['discounted_total'] 		 = $discounted_total;
			}
		}
		if($tab=='competition') {

			if($competition){
				$temp_data['competition'] = $competition;
			}

			$routineArray = array();

			if($routinecurr){
				foreach ($routinecurr as $key=>$rn) {
					if($rn['name'] !=''){
						$id 	 = $key;
						$name 	 = $rn['name'];
						$dancers = $rn['dancers'];
						$agediv  = $rn['agediv'];
						$cat 	 = $rn['cat'];
						$genre 	 = $rn['genre'];
						$flows 	 = $rn['flows'];
						$props 	 = $rn['props'];
						$music 	 = $rn['music'];
						$fee 	 = $rn['fee'];

						$routineArgs = array(
							'ID' => (int)$key,
							'post_title' => $name,
							'post_type' => 'ts_routine',
						);

						$newRoutine = wp_update_post($routineArgs, true);

						if($newRoutine && !is_wp_error($newRoutine)) {
							update_post_meta($newRoutine, 'dancers', $dancers);
							update_post_meta($newRoutine, 'agediv', $agediv);
							update_post_meta($newRoutine, 'cat', $cat);
							update_post_meta($newRoutine, 'genre', $genre);
							update_post_meta($newRoutine, 'flows', $flows);
							update_post_meta($newRoutine, 'props', $props);
							update_post_meta($newRoutine, 'music', $music);
							update_post_meta($newRoutine, 'fee', $fee);

				            $term = get_term_by('name', $agediv, 'ts_agediv');
				            $agediv_order = get_term_meta($term->term_id, 'div_order', true);
							update_post_meta($newRoutine, 'agediv_order', $agediv_order);

							$newRoutineInfo = array(
								'name' => $name, 
								'dancers' => $dancers, 
								'agediv' => $agediv, 
								'cat' => $cat, 
								'genre' => $genre, 
								'flows' => $flows, 
								'props' => $props, 
								'music' => $music, 
								'fee' => $fee, 
							);
							$routineArray[$newRoutine] = $newRoutineInfo;
						}
					}	
				}
			}
			
			if($routinenew) {
				foreach ($routinenew as $rn) {
					if($rn['name'] !=''){

						$name 	 = $rn['name'];
						$dancers = $rn['dancers'];
						$agediv  = $rn['agediv'];
						$cat 	 = $rn['cat'];
						$genre 	 = $rn['genre'];
						$flows 	 = $rn['flows'];
						$props 	 = $rn['props'];
						$music 	 = $rn['music'];
						$fee 	 = $rn['fee'];

						$routineArgs = array(
							'post_title' => $name,
							'post_type' => 'ts_routine',
							'post_status' => 'publish',
							'author' => $user_id
						);
						$newRoutine = wp_insert_post($routineArgs, true);

						if($newRoutine && !is_wp_error($newRoutine)) {
							update_post_meta($newRoutine, 'dancers', $dancers);
							update_post_meta($newRoutine, 'agediv', $agediv);
							update_post_meta($newRoutine, 'cat', $cat);
							update_post_meta($newRoutine, 'genre', $genre);
							update_post_meta($newRoutine, 'flows', $flows);
							update_post_meta($newRoutine, 'props', $props);
							update_post_meta($newRoutine, 'music', $music);
							update_post_meta($newRoutine, 'fee', $fee);

				            $term = get_term_by('name', $agediv, 'ts_agediv');
				            $agediv_order = get_term_meta($term->term_id, 'div_order', true);
							update_post_meta($newRoutine, 'agediv_order', $agediv_order);

							$newRoutineInfo = array(
								'name' => $name, 
								'dancers' => $dancers, 
								'agediv' => $agediv, 
								'cat' => $cat, 
								'genre' => $genre, 
								'flows' => $flows, 
								'props' => $props, 
								'music' => $music, 
								'fee' => $fee, 
							);
							$routineArray[$newRoutine] = $newRoutineInfo;
						}
					}	
				}
			}

			$temp_data['competition']['routines'] = $routineArray;
		}
		if($tab=='confirmation') {

			$temp_data['discount_code'] = $discount_code;
			$comfirmed = true;

			$status = get_post_status($entry_id);
			if($status=='paid' || $status=='paidcheck'){
				$paid_amount = get_post_meta($entry_id, 'paid_amount', true);
				$grand_total = get_post_meta($entry_id, 'grand_total', true);
				if($paid_amount!=$grand_total) {
					do_action('registration_edited', $entry_id, $user_id, $remaining_amount);
				}
				if( $amount_credited > 0 ){
				    update_post_meta($entry_id,'amount_credited',$amount_credited);
                }
			}
		}
		if($tab=='payment') {
			
			do_action('registration_completed', $entry_id, $user_id, 'mail_in_check');
            ts_change_post_status($entry_id, 'unpaidcheck');
			$completed = '&completed=1';
		}
		if($tab!='payment'){

			$entry = array(
				'post_title' => 'Entry',
				'post_type' => 'ts_entry',
				'author' => $user_id
			);

			if($entry_id) {
				$entry_id = (int)$entry_id;
				if(current_user_can('edit_entry', $entry_id)){
					$entry['ID'] = $entry_id;
					$entry['post_title'] = 'Entry #'. $entry_id;
					$updated = wp_update_post($entry, true);
				}
			}
			else{
				$entry['post_status'] = 'unpaid';
				$entry['post_content'] = '';
				$updated = wp_insert_post($entry, true);
			}
			
			if($updated && !is_wp_error($updated)) {
				if(! $entry_id) {
					$updated = wp_update_post(array('ID' => $updated, 'post_title' => 'Entry #'. $updated), true);
				}

				$entry_type = get_term_by('name', 'Studio', 'ts_entry_type');
				wp_set_object_terms($updated, $entry_type->term_id, 'ts_entry_type');

				$grand_total = ts_grand_total($eid, $temp_data);
				if(isset($temp_data['discount_code']) && $temp_data['discount_code']!='') {
					$grand_total = ts_discounted_grand_total($grand_total, $temp_data['discount_code'], $entry_id);
					update_post_meta($updated, 'discount_code', $discount_code);
				}

				update_post_meta($updated, 'profile', $temp_data['profile']);
				update_post_meta($updated, 'workshop', $temp_data['workshop']);		
				update_post_meta($updated, 'competition', $temp_data['competition']);
				update_post_meta($updated, 'grand_total', $grand_total);
				update_post_meta($updated, 'comfirmed', $comfirmed);
				update_post_meta($updated, 'save_for_later', $curr_step);				

	            if(isset($temp_data['workshop']['tour_city'])) {
	            	$date_from 	= get_post_meta($temp_data['workshop']['tour_city'], 'date_from', true);
                    $date_to =  get_post_meta($temp_data['workshop']['tour_city'], 'date_to', true);
	            	update_post_meta($entry_id, 'tour_date', $date_from);
                    update_post_meta($entry_id, 'tour_end_date', $date_to);
					update_post_meta($updated, 'tour_city', $temp_data['workshop']['tour_city']);				
	            }

				$eid = $updated;
				$entry_id = $updated;
			}	
		}	

		ts_set_session_entry_data($temp_data, $eid, $user_id);

		$has_error = false;

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['success'] = true;
			$base_url = ts_get_base_url($entry_id, $eid);
			$next_step = $save ? $curr_step : $next_step;
			$response['redirect'] = $base_url .'&step='. $next_step . $completed;
		}
	
		echo json_encode($response);

	endif;

    die();		
}

function ajax_individual_registration() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$tab 		   = $_POST['tab'];
		$next_step 	   = $_POST['next_step'];
		$entry_id 	   = $_POST['entry_id'];
		$eid 	 	   = $_POST['eid'];
		$profile 	   = $_POST['profile'];
		$newsiblings   = $_POST['newsiblings'];
		$currsiblings  = $_POST['currsiblings'];
		$workshop      = $_POST['workshop'];
		$competition   = $_POST['competition'];
		$routinecurr   = $_POST['routinecurr'];
		$routinenew    = $_POST['routinenew'];
		$payment  	   = $_POST['payment'];
		$discount_code = $_POST['discount_code'];
		$save 		   = $_POST['save_for_later'];
        $remaining        = absint($_POST['remaining']);
        $remaining_amount = absint($_POST['remaining_amount']);
        $amount_credited = absint($_POST['amount_credited']);

		$response = array(
			'success' => false,
			'redirect' => false, 
			'message' => array(),
		);

		$has_error = true;
		$completed = '';
        $comfirmed = false;

        $user_id 	= get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid);
		$temp_data 	= $entry_data;
		$curr_step 	= ((int)$next_step)-1;

		if($tab=='profile') {

			$siblingsArray 		= array();
			$participantsArray 	= array();

			if($profile){
				$temp_data['profile'] = $profile;

				$name 		= $profile['name'];
				$birth_date = $profile['birth_date'];
				$parent 	= $profile['parent'];
				$studio 	= $profile['studio'];
				$address 	= $profile['address'];
				$city 		= $profile['city'];
				$state 		= $profile['state'];
				$zipcode 	= $profile['zipcode'];
				$country 	= $profile['country'];
				$cell 		= $profile['cell'];
				$email 		= $profile['email'];

				update_field('name', $name, 'user_'. $user_id);
				update_field('birth_date', $birth_date, 'user_'. $user_id);
				update_field('parent', $parent, 'user_'. $user_id);
				update_field('studio', $studio, 'user_'. $user_id);
				update_field('address', $address, 'user_'. $user_id);
				update_field('city', $city, 'user_'. $user_id);
				update_field('state', $state, 'user_'. $user_id);
				update_field('zipcode', $zipcode, 'user_'. $user_id);
				update_field('country', $country, 'user_'. $user_id);
				update_field('cell', $cell, 'user_'. $user_id);
				update_field('email', $email, 'user_'. $user_id);

				$args2 = array(
				    'ID'         => $user_id,
				    'user_email' => $email
				);
				wp_update_user($args2);

				$args = array(
					'meta_key' => 'my_profile',
					'meta_value' => true,
				);
				$my_profile = ts_get_user_posts('ts_sibling', 1, $user_id, $args);

				if($my_profile) {

					$profileArgs = array(
						'ID' => $my_profile[0]->ID,
						'post_title' => $name,
						'post_type' => 'ts_sibling',
						'post_status' => 'publish',
					);
					$newProfile = wp_update_post($profileArgs, true);
				}
				else {
					$profileArgs = array(
						'post_title' => $name,
						'post_type' => 'ts_sibling',
						'post_status' => 'publish',
						'author' => $user_id,
					);
					$newProfile = wp_insert_post($profileArgs, true);
				}
				if($newProfile && !is_wp_error($newProfile)) {
					update_post_meta($newProfile, 'my_profile', true);
					update_post_meta($newProfile, 'name', $name);
					update_post_meta($newProfile, 'birth_date', $birth_date);
					update_post_meta($newProfile, 'parent', $parent);
					update_post_meta($newProfile, 'studio', $studio);
					update_post_meta($newProfile, 'address', $address);
					update_post_meta($newProfile, 'city', $city);
					update_post_meta($newProfile, 'state', $state);
					update_post_meta($newProfile, 'zipcode', $zipcode);
					update_post_meta($newProfile, 'country', $country);
					update_post_meta($newProfile, 'cell', $cell);
					update_post_meta($newProfile, 'email', $email);

					$dancer = get_term_by('name', 'Dancer', 'ts_rostertype');
					wp_set_object_terms($newProfile, $dancer->term_id, 'ts_rostertype');

					$agediv = ts_set_age_division($newProfile, $birth_date);

					$participant = array(
						'age_division' => $age_division, 
						'discount' => $discount, 
						'duration' => $duration, 
						'fee' => $new_value, 
					);
					$participantsArray[$newProfile] = $participant;
					$response['agediv'] = $agediv;
				}
			}

			if($currsiblings) {
				foreach ($currsiblings as $key=>$s) {
					if($s['name'] !=''){

						$id 		= $key;
						$name 		= $s['name'];
						$birth_date = $s['birth_date'];
						$parent 	= $s['parent'];
						$studio 	= $s['studio'];
						$address 	= $s['address'];
						$city 		= $s['city'];
						$state 		= $s['state'];
						$zipcode 	= $s['zipcode'];
						$country 	= $s['country'];
						$cell 		= $s['cell'];
						$email 		= $s['email'];

						$siblingArgs = array(
							'ID' => $id,
							'post_title' => $name,
							'post_type' => 'ts_sibling',
							'post_status' => 'publish',
						);
						$newSibling = wp_update_post($siblingArgs, true);

						if($newSibling && !is_wp_error($newSibling)) {
							update_post_meta($newSibling, 'name', $name);
							update_post_meta($newSibling, 'birth_date', $birth_date);
							update_post_meta($newSibling, 'parent', $parent);
							update_post_meta($newSibling, 'studio', $studio);
							update_post_meta($newSibling, 'address', $address);
							update_post_meta($newSibling, 'city', $city);
							update_post_meta($newSibling, 'state', $state);
							update_post_meta($newSibling, 'zipcode', $zipcode);
							update_post_meta($newSibling, 'country', $country);
							update_post_meta($newSibling, 'cell', $cell);
							update_post_meta($newSibling, 'email', $email);

							$dancer = get_term_by('name', 'Dancer', 'ts_rostertype');
							wp_set_object_terms($newSibling, $dancer->term_id, 'ts_rostertype');

							ts_set_age_division($newSibling, $birth_date);

							$sibling = array(
								'name' => $name, 
								'birth_date' => $birth_date, 
								'parent' => $parent, 
								'studio' => $studio, 
								'address' => $address, 
								'cell' => $cell, 
								'email' => $email, 
							);
							$siblingsArray[$newSibling] = $sibling;

							$participant = array(
								'age_division' => $age_division, 
								'discount' => $discount, 
								'duration' => $duration, 
								'fee' => $new_value, 
							);
							$participantsArray[$newSibling] = $participant;
						}
					}	
				}
			}

			if($newsiblings) {
				foreach ($newsiblings as $s) {
					if($s['name'] !=''){

						$name 		= $s['name'];
						$birth_date = $s['birth_date'];
						$parent 	= $s['parent'];
						$studio 	= $s['studio'];
						$address 	= $s['address'];
						$city 		= $s['city'];
						$state 		= $s['state'];
						$zipcode 	= $s['zipcode'];
						$country 	= $s['country'];
						$cell 		= $s['cell'];
						$email 		= $s['email'];

						$siblingArgs = array(
							'post_title' => $name,
							'post_type' => 'ts_sibling',
							'post_status' => 'publish',
							'author' => $user_id,
						);
						$newSibling = wp_insert_post($siblingArgs, true);

						if($newSibling && !is_wp_error($newSibling)) {
							update_post_meta($newSibling, 'name', $name);
							update_post_meta($newSibling, 'birth_date', $birth_date);
							update_post_meta($newSibling, 'parent', $parent);
							update_post_meta($newSibling, 'studio', $studio);
							update_post_meta($newSibling, 'address', $address);
							update_post_meta($newSibling, 'city', $city);
							update_post_meta($newSibling, 'state', $state);
							update_post_meta($newSibling, 'zipcode', $zipcode);
							update_post_meta($newSibling, 'country', $country);
							update_post_meta($newSibling, 'cell', $cell);
							update_post_meta($newSibling, 'email', $email);

							$dancer = get_term_by('name', 'Dancer', 'ts_rostertype');
							wp_set_object_terms($newSibling, $dancer->term_id, 'ts_rostertype');

							ts_set_age_division($newSibling, $birth_date);

							$sibling = array(
								'name' => $name, 
								'birth_date' => $birth_date, 
								'parent' => $parent, 
								'studio' => $studio, 
								'address' => $address, 
								'cell' => $cell, 
								'email' => $email, 
							);
							$siblingsArray[$newSibling] = $sibling;

							$participant = array(
								'age_division' => $age_division, 
								'discount' => $discount, 
								'duration' => $duration, 
								'fee' => $new_value, 
							);
							$participantsArray[$newSibling] = $participant;
						}
					}	
				}
			}
			$temp_data['workshop']['participants'] = $participantsArray;
			$temp_data['profile']['siblings'] = $siblingsArray;
			$comfirmed = false;
		}
		if($tab=='workshop') {
			if($workshop){

				if((! isset($workshop['tour_city']) || $workshop['tour_city']=='') || ts_is_paid($entry_id) || (isset($workshop['tour_city']) && get_post_meta($workshop['tour_city'], 'status', true)==2) ) {
					$workshop['tour_city'] = $temp_data['workshop']['tour_city'];
				}
				
				$temp_data['workshop'] = $workshop;

				$participants 		= ts_check_value($workshop, 'participants');
				$observer 			= ts_check_value($workshop, 'observers');
				$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');

				$countObservers 			= count($observer);
				$countMunchkinObservers 	= count($munchkin_observer);

				$countMunchkin  = 0;
				$countMinis 	= 0;
				$countJuniors 	= 0;
				$countTeens 	= 0;
				$countSeniors 	= 0;
				$countPros 		= 0;
				$countTeachers 	= 0;

				$Munchkin 	= get_term_by('name', 'Munchkin', 'ts_agediv');
				$Mini 		= get_term_by('name', 'Mini', 'ts_agediv');
				$Junior 	= get_term_by('name', 'Junior', 'ts_agediv');
				$Teen 		= get_term_by('name', 'Teen', 'ts_agediv');
				$Senior 	= get_term_by('name', 'Senior', 'ts_agediv');
				$Pro 		= get_term_by('name', 'Pro', 'ts_agediv');
				$Teacher 	= get_term_by('name', 'Teacher', 'ts_agediv');

				if(! empty($participants)) {
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
				}

				$temp_data['workshop']['count_munchkins'] 		 = $countMunchkin;
				$temp_data['workshop']['count_minis'] 			 = $countMinis;
				$temp_data['workshop']['count_juniors'] 		 = $countJuniors;
				$temp_data['workshop']['count_teens'] 			 = $countTeens;
				$temp_data['workshop']['count_seniors'] 		 = $countSeniors;
				$temp_data['workshop']['count_pros'] 			 = $countPros;
				$temp_data['workshop']['count_teachers'] 		 = $countTeachers;
				$temp_data['workshop']['count_observer'] 		 = $countObservers;
				$temp_data['workshop']['count_munchkinobserver'] = $countMunchkinObservers;

				$workshop_fee 			= ts_get_total_workshop_fee($eid, $temp_data);
				$teacher_discount 		= ts_get_total_teacher_discount($eid, $temp_data);
				$scholarship_discount 	= ts_get_total_scholarship_discount($eid, $temp_data);
				$discounted_total 		= ts_get_discounted_total_workshop_fee($eid, $temp_data);

				$temp_data['workshop']['total_fee'] 			 = $workshop_fee;
				$temp_data['workshop']['teacher_discount'] 		 = $teacher_discount;
				$temp_data['workshop']['scholarship_discount'] 	 = $scholarship_discount;
				$temp_data['workshop']['discounted_total'] 		 = $discounted_total;
			}
		}
		if($tab=='competition') {

			if($competition){
				$temp_data['competition'] = $competition;
			}

			$routineArray = array();

			if($routinecurr){
				foreach ($routinecurr as $key=>$rn) {
					if($rn['name'] !=''){
						$id 	 = $key;
						$name 	 = $rn['name'];
						$dancers = $rn['dancers'];
						$agediv  = $rn['agediv'];
						$cat 	 = $rn['cat'];
						$genre 	 = $rn['genre'];
						$flows 	 = $rn['flows'];
						$props 	 = $rn['props'];
						$music 	 = $rn['music'];
						$fee 	 = $rn['fee'];

						$routineArgs = array(
							'ID' => (int)$key,
							'post_title' => $name,
							'post_type' => 'ts_routine',
						);

						$newRoutine = wp_update_post($routineArgs, true);

						if($newRoutine && !is_wp_error($newRoutine)) {
							update_post_meta($newRoutine, 'dancers', $dancers);
							update_post_meta($newRoutine, 'agediv', $agediv);
							update_post_meta($newRoutine, 'cat', $cat);
							update_post_meta($newRoutine, 'genre', $genre);
							update_post_meta($newRoutine, 'flows', $flows);
							update_post_meta($newRoutine, 'props', $props);
							update_post_meta($newRoutine, 'music', $music);
							update_post_meta($newRoutine, 'fee', $fee);

				            $term = get_term_by('name', $agediv, 'ts_agediv');
				            $agediv_order = get_term_meta($term->term_id, 'div_order', true);
							update_post_meta($newRoutine, 'agediv_order', $agediv_order);

							$newRoutineInfo = array(
								'name' => $name, 
								'dancers' => $dancers, 
								'agediv' => $agediv, 
								'cat' => $cat, 
								'genre' => $genre, 
								'flows' => $flows, 
								'props' => $props, 
								'music' => $music, 
								'fee' => $fee, 
							);
							$routineArray[$newRoutine] = $newRoutineInfo;
						}
					}	
				}
			}
			
			if($routinenew) {
				foreach ($routinenew as $rn) {
					if($rn['name'] !=''){

						$name 	 = $rn['name'];
						$dancers = $rn['dancers'];
						$agediv  = $rn['agediv'];
						$cat 	 = $rn['cat'];
						$genre 	 = $rn['genre'];
						$flows 	 = $rn['flows'];
						$props 	 = $rn['props'];
						$music 	 = $rn['music'];
						$fee 	 = $rn['fee'];

						$routineArgs = array(
							'post_title' => $name,
							'post_type' => 'ts_routine',
							'post_status' => 'publish',
							'author' => $user_id
						);
						$newRoutine = wp_insert_post($routineArgs, true);

						if($newRoutine && !is_wp_error($newRoutine)) {
							update_post_meta($newRoutine, 'dancers', $dancers);
							update_post_meta($newRoutine, 'agediv', $agediv);
							update_post_meta($newRoutine, 'cat', $cat);
							update_post_meta($newRoutine, 'genre', $genre);
							update_post_meta($newRoutine, 'flows', $flows);
							update_post_meta($newRoutine, 'props', $props);
							update_post_meta($newRoutine, 'music', $music);
							update_post_meta($newRoutine, 'fee', $fee);

				            $term = get_term_by('name', $agediv, 'ts_agediv');
				            $agediv_order = get_term_meta($term->term_id, 'div_order', true);
							update_post_meta($newRoutine, 'agediv_order', $agediv_order);

							$newRoutineInfo = array(
								'name' => $name, 
								'dancers' => $dancers, 
								'agediv' => $agediv, 
								'cat' => $cat, 
								'genre' => $genre, 
								'flows' => $flows, 
								'props' => $props, 
								'music' => $music, 
								'fee' => $fee, 
							);
							$routineArray[$newRoutine] = $newRoutineInfo;
						}
					}	
				}
			}

			$temp_data['competition']['routines'] = $routineArray;
		}
		if($tab=='confirmation') {

			$temp_data['discount_code'] = $discount_code;
			$comfirmed = true;

			$status = get_post_status($entry_id);
			if($status=='paid' || $status=='paidcheck'){
				$paid_amount = get_post_meta($entry_id, 'paid_amount', true);
				$grand_total = get_post_meta($entry_id, 'grand_total', true);
				if($paid_amount!=$grand_total) {
					do_action('registration_edited', $entry_id, $user_id, $remaining_amount);
				}
                if( $amount_credited > 0 ){
                    update_post_meta($entry_id,'amount_credited',$amount_credited);
                }
			}
		}
		if($tab=='payment') {
			
			do_action('registration_completed', $entry_id, $user_id, 'mail_in_check');
			ts_change_post_status($entry_id, 'unpaidcheck');
			$completed = '&completed=1';
		}
		if($tab!='payment'){

			$entry = array(
				'post_title' => 'Entry',
				'post_type' => 'ts_entry',
				'author' => $user_id
			);

			if($entry_id) {
				$entry_id = (int)$entry_id;
				if(current_user_can('edit_entry', $entry_id)){
					$entry['ID'] = $entry_id;
					$entry['post_title'] = 'Entry #'. $entry_id;
					$updated = wp_update_post($entry, true);
				}
			}
			else{
				$entry['post_status'] = 'unpaid';
				$entry['post_content'] = '';
				$updated = wp_insert_post($entry, true);
			}
			
			if($updated && !is_wp_error($updated)) {
				if(! $entry_id) {
					$updated = wp_update_post(array('ID' => $updated, 'post_title' => 'Entry #'. $updated), true);
				}

				$entry_type = get_term_by('name', 'Individual', 'ts_entry_type');
				wp_set_object_terms($updated, $entry_type->term_id, 'ts_entry_type');

				$grand_total = ts_grand_total($eid, $temp_data);
				if(isset($temp_data['discount_code']) && $temp_data['discount_code']!='') {
					$grand_total = ts_discounted_grand_total($grand_total, $temp_data['discount_code'], $entry_id);
					update_post_meta($updated, 'discount_code', $discount_code);
				}

				update_post_meta($updated, 'profile', $temp_data['profile']);
				update_post_meta($updated, 'workshop', $temp_data['workshop']);		
				update_post_meta($updated, 'competition', $temp_data['competition']);
				update_post_meta($updated, 'grand_total', $grand_total);
				update_post_meta($updated, 'comfirmed', $comfirmed);
				update_post_meta($updated, 'save_for_later', $curr_step);

	            if(isset($temp_data['workshop']['tour_city'])) {
	            	$date_from 	= get_post_meta($temp_data['workshop']['tour_city'], 'date_from', true);
                    $date_to =  get_post_meta($temp_data['workshop']['tour_city'], 'date_to', true);
                    update_post_meta($entry_id, 'tour_date', $date_from);
                    update_post_meta($entry_id, 'tour_end_date', $date_to);
					update_post_meta($updated, 'tour_city', $temp_data['workshop']['tour_city']);				
	            }

				$eid = $updated;
				$entry_id = $updated;
			}	
		}	

		ts_set_session_entry_data($temp_data, $eid, $user_id);
		
		$has_error = false;

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['success'] = true;
			$base_url = ts_get_base_url($entry_id, $eid);
			$next_step = $save ? $curr_step : $next_step;
			$response['redirect'] = $base_url .'&step='. $next_step . $completed;
		}
	
		echo json_encode($response);
	endif;

    die();		
}

function ajax_new_registration() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$id		= (int)$_POST['id'];
		$eid 	= ts_set_eid();

		$response = array(
			'success' => false, 
		);

		$temp_data = array();
		$temp_data['workshop']['tour_city'] = $id;
		ts_set_session_entry_data($temp_data, $eid);

		$response['success'] = true;
		$response['redirect'] = admin_url('admin.php?page=ts-post-entry&eid='. $eid);

		echo json_encode($response);

	endif;

    die();		
}

function ajax_edit_entry() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$eid	= (int)$_POST['eid'];
		$url	= $_POST['url'];

		$response = array(
			'success' => false, 
		);

		if(current_user_can('edit_entry', $eid)) {
			ts_load_entry_data_from_post($eid);
			update_post_meta($eid, 'save_for_later', 1);
			update_post_meta($eid, 'comfirmed', false);
			update_post_meta($eid, 'completed', false);
			$response['success'] = true;
			$response['redirect'] = $url;
		}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_save_roster() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$eid			= $_POST['eid'];
		$id				= $_POST['id'];
		$first_name		= $_POST['first_name'];
		$last_name		= $_POST['last_name'];
		$birth_date		= $_POST['birth_date'];
		$roster_type 	= $_POST['roster_type'];
		$type_name 		= $_POST['type_name'];
		$selected		= $_POST['selected'];

		$response = array(
			'success' => false, 
			'id' => $id, 
			'first_name' => $first_name, 
			'last_name' => $last_name, 
			'birth_date' => $birth_date, 
			'roster_type' => $roster_type, 
			'type_name' => $type_name, 
		);

		if(current_user_can('edit_studio_roster', $id)){

			$rosterArgs = array(
				'ID' => $id,
				'post_title' => $first_name .' '. $last_name,
				'post_type' => 'ts_studio_roster',
			);
			$newRoster = wp_update_post($rosterArgs, true);

			if($newRoster && !is_wp_error($newRoster)) {
				update_post_meta($newRoster, 'first_name', $first_name);
				update_post_meta($newRoster, 'last_name', $last_name);
				update_post_meta($newRoster, 'birth_date', $birth_date);

				wp_set_object_terms($newRoster, (int)$roster_type, 'ts_rostertype');
				ts_set_age_division($newRoster, $birth_date);

				$entry_data = ts_get_session_entry_data($eid);
				$temp_data = $entry_data;

				if($selected){
					$age_div = wp_get_object_terms($newRoster, 'ts_agediv');
					$fee = ts_get_workshop_fee($newRoster, 2, $eid);

					$newParticipant = array(
		            	'age_division' => $age_div[0]->term_id, 
		                'discount' => '',
		                'duration' => 1,
		                'fee' => $fee
					);
					$temp_data['workshop']['participants'][$id] = $newParticipant;
				}
				else {
					unset($temp_data['workshop']['participants'][$id]);
				}
				ts_set_session_entry_data($temp_data, $eid);

				$response['success'] = true;
			}
		}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_adjust_fee() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$eid			= $_POST['eid'];
		$id				= (int)$_POST['id'];
		$age_division 	= (int)$_POST['age_division'];
		$discount		= (int)$_POST['discount'];
		$duration		= (int)$_POST['duration'];

		$response = array(
			'success' => false, 
			'id' => $id, 
		);

		$entry_data = ts_get_session_entry_data($eid);
		$temp_data = $entry_data;

		ts_update_age_division($id, $age_division);

		$base_fee = ts_get_workshop_fee($id, $duration, $eid);
		$new_value = ts_get_discounted_workshop_fee($base_fee, $discount);

		$participant = array(
			'age_division' => $age_division, 
			'discount' => $discount, 
			'duration' => $duration, 
			'fee' => $new_value, 
		);

		$temp_data['workshop']['participants'][$id] = $participant;

		$total_fee 				= ts_get_total_workshop_fee($eid, $temp_data);
		$discounted_total 		= ts_get_discounted_total_workshop_fee($eid, $temp_data);
		$scholarship_discount 	= ts_get_total_scholarship_discount($eid, $temp_data);
		$teacher_discount 		= ts_get_total_teacher_discount($eid, $temp_data);
		$grand_total 			= ts_grand_total($eid, $temp_data);

		$temp_data['workshop']['total_fee'] = $total_fee;
		$temp_data['workshop']['discounted_total'] = $discounted_total;
		$temp_data['workshop']['scholarship_discount'] = $scholarship_discount;
		$temp_data['workshop']['teacher_discount'] = $teacher_discount;
		$temp_data['grand_total'] = $grand_total;

		ts_set_session_entry_data($temp_data, $eid);

		$munchkins = get_term_by('name', 'Munchkin', 'ts_agediv');
		$munchkins_id = $munchkins->term_id;
		$disabled = $age_division == $munchkins_id ? true : false;
		$response['onedaydisabled'] = $disabled;

		$free_teacher_ids = ts_get_free_teacher_ids($eid);
		$new_value_preview = in_array($id, $free_teacher_ids) ? 'Free' : '$'. number_format($new_value, 2);
		$response['new_value'] = $new_value;
		$response['new_value_preview'] = $new_value_preview;

		$new_total = ts_get_discounted_total_workshop_fee($eid);
		$new_total_preview = number_format($new_total, 2);
		$response['new_total'] = $new_total;
		$response['new_total_preview'] = $new_total_preview;
		$response['success'] = true;

		echo json_encode($response);

	endif;

    die();		
}

function ajax_set_tour_city() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$eid			= $_POST['eid'];
		$tour_city		= (int)$_POST['tour_city'];

		$response = array(
			'success' => false, 
		);

		$entry_data = ts_get_session_entry_data($eid);

		$temp_data = $entry_data;
				
		if(! ts_is_paid($eid) && get_post_meta($tour_city, 'status', true) != 2 ) {
			$temp_data['workshop']['tour_city'] = $tour_city;
		}

		ts_set_session_entry_data($temp_data, $eid);

		$response['success'] = true;

		echo json_encode($response);

	endif;

    die();		
}

function ajax_add_routine_dancers() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$eid		= $_POST['eid'];
		$id			= absint($_POST['routine-id']);
		$name 		= $_POST['routine-name'];
		$dancers 	= $_POST['dancers'];

		$id 		= (int)$id;

		$response = array(
			'success' => false, 
			'id' => $id,
		);

		if(! empty($dancers)) {

			$entry_data = ts_get_session_entry_data($eid);
			$temp_data = $entry_data;

			$dancer_names = '';
			$dancer_ids = implode(',', $dancers);
			$age_div_name = '';
			$routine_cat_id = '';
			$routine_cat_name = '';
			$count = 0;
			$fee = 0;

			if(get_post_status($eid)=='paid' || get_post_status($eid)=='paidcheck') {
				$dancers_count = get_post_meta($id, 'dancers_count', true);
				if($dancers_count) {
					if(count($dancers) < $dancers_count) {
						$count = $dancers_count;
					}
					else {
						$count = count($dancers);
					}
					update_post_meta($id, 'dancers_count_edited', $count);
				}
			}
			else {
				$count = count($dancers);
			}

			$cat = ts_get_competition_categories();

			if( 1 == $count ) {
				$routine_cat_id = $cat[1]['id'];
				$routine_cat_name = $cat[1]['title'];
			}
			else if( (2 <= $count) && ($count <= 3) ) {
				$routine_cat_id = $cat[2]['id'];
				$routine_cat_name = $cat[2]['title'];
			}
			else if( (4 <= $count) && ($count <= 9) ) {
				$routine_cat_id = $cat[3]['id'];
				$routine_cat_name = $cat[3]['title'];
			}
			else if( (10 <= $count) && ($count <= 16) ) {
				$routine_cat_id = $cat[4]['id'];
				$routine_cat_name = $cat[4]['title'];
			}
			else if( (17 <= $count) && ($count <= 24) ) {
				$routine_cat_id = $cat[5]['id'];
				$routine_cat_name = $cat[5]['title'];
			}
			else if( $count >= 25 ) {
				$routine_cat_id = $cat[6]['id'];
				$routine_cat_name = $cat[6]['title'];
			}

			$fee = ts_get_routine_fee($count);

			$count_d = 0;
			$age_total = 0;
			foreach ($dancers as $d) {
				$count_d++;
				$comma = $count_d===1 ? '' : ', ';
				$dancer_names.= $comma . get_the_title($d);

				$birth_date = get_post_meta($d, 'birth_date', true);
				$age = ts_get_the_age($birth_date);
				$age_total = $age_total + $age;
			}

			$age_ave = round($age_total / $count_d);
			$age_div_name = ts_get_routine_agediv_name($age_ave);

			$routineArray = isset($temp_data['competition']['routines']) ? $temp_data['competition']['routines'] : array();

			if(ts_post_exists_by_type($id, 'ts_routine') && current_user_can('edit_routine', $id)) {
				$routine_id = $id;
			}
			else{
				$user_id = get_current_user_id();
				$name = $name ? $name : 'Routine '. $id;

				$routineArgs = array(
					'post_title' => $name,
					'post_type' => 'ts_routine',
					'post_status' => 'publish',
					'author' => $user_id
				);
				$routine_id = wp_insert_post($routineArgs, true);
			}
				
			if($routine_id && !is_wp_error($routine_id)) {
				if(get_post_status($eid)!='paid' && get_post_status($eid)!='paidcheck') {
					update_post_meta($routine_id, 'dancers_count', $count);
				}
				update_post_meta($routine_id, 'dancers', $dancer_ids);
				update_post_meta($routine_id, 'agediv', $age_div_name);
				update_post_meta($routine_id, 'cat', $routine_cat_id);
				update_post_meta($routine_id, 'fee', $fee);

	            $term = get_term_by('name', $age_div_name, 'ts_agediv');
	            $agediv_order = get_term_meta($term->term_id, 'div_order', true);
				update_post_meta($routine_id, 'agediv_order', $agediv_order);

				$routineArray[$routine_id]['dancers'] = $dancer_ids;
				$routineArray[$routine_id]['agediv'] = $age_div_name;
				$routineArray[$routine_id]['cat'] = $routine_cat_id;
				$routineArray[$routine_id]['fee'] = $fee;
			}
			$temp_data['competition']['routines'] = $routineArray;

			$total_fee = ts_get_total_competition_fee($eid, $temp_data);
			$grand_total = ts_grand_total($eid, $temp_data);

			$temp_data['competition']['total_fee'] = $total_fee;
			$temp_data['grand_total'] = $grand_total;

			ts_set_session_entry_data($temp_data, $eid);

			$response['dancer_names'] = $dancer_names;
			$response['dancer_ids'] = $dancer_ids;
			$response['age_ave'] = $age_ave;
			$response['age_div_name'] = $age_div_name;
			$response['routine_cat_id'] = $routine_cat_id;
			$response['routine_cat_name'] = $routine_cat_name;
			$response['count'] = $count;
			$response['fee'] = $fee;
			$response['fee_preview'] = number_format($fee, 2);
			$response['total_fee'] = $total_fee;
			$response['total_fee_preview'] = number_format($total_fee, 2);
			$response['routine_id'] = $routine_id;
			$response['success'] = true;
		}	

		echo json_encode($response);

	endif;

    die();		
}

function ajax_add_participants() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$participants = $_POST['participants'];
		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
		);

		$user_id = get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid, $user_id);
		$temp_data = $entry_data;
		$participantsArray = ts_check_value($temp_data, 'workshop', 'participants');

		$newParticipantsArray = array();

		foreach ($participants as $p) {

			if(! array_key_exists($p, $participantsArray)) {

				$age_div = wp_get_object_terms($p, 'ts_agediv');

                $first_name = get_post_meta($p, 'first_name', true);
                $last_name = get_post_meta($p, 'last_name', true);
                $name = $first_name .' '.$last_name;
                $fee = ts_get_workshop_fee($p, 2, $eid);

                $new = array(
                    'name' => $name,
                	'age_division' => $age_div[0]->term_id, 
                    'discount' => '',
                    'duration' => 1,
                    'fee' => $fee
                ); 

                $participantsArray[$p] = $new;
                $newParticipantsArray[$p] = $new;
			}	
		}

		$temp_data['workshop']['participants'] = $participantsArray;

		$total_fee = ts_get_total_workshop_fee($eid, $temp_data);
		$discounted_total = ts_get_discounted_total_workshop_fee($eid, $temp_data);
		$scholarship_discount = ts_get_total_scholarship_discount($eid, $temp_data);
		$teacher_discount = ts_get_total_teacher_discount($eid, $temp_data);
		$grand_total = ts_grand_total($eid, $temp_data);

		$temp_data['workshop']['total_fee'] = $total_fee;
		$temp_data['workshop']['discounted_total'] = $discounted_total;
		$temp_data['workshop']['scholarship_discount'] = $scholarship_discount;
		$temp_data['workshop']['teacher_discount'] = $teacher_discount;
		$temp_data['grand_total'] = $grand_total;

		ts_set_session_entry_data($temp_data, $eid, $user_id);

		$response['newparticipants'] = $newParticipantsArray;
		$response['success'] = true;

		echo json_encode($response);

	endif;

    die();		
}

function ajax_add_observer() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$name = $_POST['name'];
		$id = $_POST['id'];
		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
		);

		$user_id = get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid, $user_id);
		$temp_data = $entry_data;
		$observerArray = ts_check_value($entry_data, 'workshop', 'observers');

		if(is_array($observerArray) && ! array_key_exists($id, $observerArray)) {
	        $new = array(
	        	'name' => $name, 
	        	'age_division' => 'N/A', 
	            'discount' => 'N/A',
	            'duration' => 'N/A',
	            'fee' => 35
	        ); 
			$temp_data['workshop']['observers'][$id] = $new;

			$total_fee 				= ts_get_total_workshop_fee($eid, $temp_data);
			$discounted_total 		= ts_get_discounted_total_workshop_fee($eid, $temp_data);
			$scholarship_discount 	= ts_get_total_scholarship_discount($eid, $temp_data);
			$teacher_discount 		= ts_get_total_teacher_discount($eid, $temp_data);
			$grand_total 			= ts_grand_total($eid, $temp_data);

			$temp_data['workshop']['total_fee'] = $total_fee;
			$temp_data['workshop']['discounted_total'] = $discounted_total;
			$temp_data['workshop']['scholarship_discount'] = $scholarship_discount;
			$temp_data['workshop']['teacher_discount'] = $teacher_discount;
			$temp_data['grand_total'] = $grand_total;

			ts_set_session_entry_data($temp_data, $eid, $user_id);

			$new['id'] = $id;
			$response['eid'] = $eid;
			$response['newpobserver'] = $new;
			$response['success'] = true;
    	}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_add_munchkin_observer() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$name = $_POST['name'];
		$id = $_POST['id'];
		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
		);

		$user_id = get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid, $user_id);
		$temp_data = $entry_data;
		$observerArray = ts_check_value($entry_data, 'workshop', 'munchkin_observers');

		if(is_array($observerArray) && ! array_key_exists($id, $observerArray)) {
	        $new = array(
	        	'name' => $name, 
	        	'age_division' => 'N/A', 
	            'discount' => 'N/A',
	            'duration' => 'N/A',
	            'fee' => 15
	        ); 
			$temp_data['workshop']['munchkin_observers'][$id] = $new;

			$total_fee 				= ts_get_total_workshop_fee($eid, $temp_data);
			$discounted_total 		= ts_get_discounted_total_workshop_fee($eid, $temp_data);
			$scholarship_discount 	= ts_get_total_scholarship_discount($eid, $temp_data);
			$teacher_discount 		= ts_get_total_teacher_discount($eid, $temp_data);
			$grand_total 			= ts_grand_total($eid, $temp_data);

			$temp_data['workshop']['total_fee'] = $total_fee;
			$temp_data['workshop']['discounted_total'] = $discounted_total;
			$temp_data['workshop']['scholarship_discount'] = $scholarship_discount;
			$temp_data['workshop']['teacher_discount'] = $teacher_discount;
			$temp_data['grand_total'] = $grand_total;

			ts_set_session_entry_data($temp_data, $eid, $user_id);

			$new['id'] = $id;
			$response['eid'] = $eid;
			$response['newpobserver'] = $new;
			$response['success'] = true;
    	}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_apply_coupon() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$eid			= $_POST['eid'];
		$coupon 		= $_POST['coupon'];

		$response = array(
			'success' => false, 
		);

		$entry_data = ts_get_session_entry_data($eid);
		$temp_data = $entry_data;

		$grand_total = ts_grand_total($eid, $temp_data);
		$discounted_grand_total = ts_discounted_grand_total($grand_total, $coupon, $eid);

		if($discounted_grand_total && $discounted_grand_total != $grand_total) {

			$button_html = '
				<input type="hidden" name="discount_code" value="'. $coupon .'" >
				Discount Code: <strong>'. $coupon .'</strong>
				<button type="button" data-eid="'. $eid .'" class="btn btn-blue btn-removecoupon">Remove</button>
			';

			$temp_data['discount_code'] = $coupon;
			$temp_data['discounted_grand_total'] = $discounted_grand_total;

			ts_set_session_entry_data($temp_data, $eid);

			$response['new_grand_total'] = $discounted_grand_total;
			$response['button_html'] = $button_html;
			$response['success'] = true;
		}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_save_voucher() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$id				= $_POST['voucher-id'];
		$code 			= $_POST['voucher-code'];
		$discount 		= $_POST['voucher-discount'];
		$workshop 		= $_POST['voucher-workshop'];
		$competition 	= $_POST['voucher-competition'];

		$id 			= absint($id);
		$has_error 		= true;

		$response = array(
			'success' => false, 
		);

		$args = array(
			'post_title' => $code,
			'post_type' => 'ts_coupon',
			'post_status' => 'publish',
		);

		if($id && current_user_can('edit_coupon', $id)){
			$args['ID'] = $id;
			$voucher_id = wp_update_post($args, true);
		}
		else{
			$args['post_content'] = '';
			$voucher_id = wp_insert_post($args, true);
		}
			
		if($voucher_id && !is_wp_error($voucher_id)) {
			update_post_meta($voucher_id, 'discount', $discount);
			update_post_meta($voucher_id, 'workshop', $workshop);
			update_post_meta($voucher_id, 'competition', $competition);
			$response['id'] = $voucher_id;
			$has_error = false;
		}

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['code'] = $code;
			$response['discount'] = $discount;
			$response['workshop'] = $workshop==1 ? 'Enabled' : 'Disabled';
			$response['competition'] = $competition==1 ? 'Enabled' : 'Disabled';
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();		
}

function ajax_save_tour() {

	if($_POST) :
	
		check_ajax_referer('ts-save-item', 'token');

		$id				= $_POST['tour-id'];
		$status 		= $_POST['tour-status'];
		$workshop 		= $_POST['tour-workshop'];
        $competition 	= $_POST['tour-competition'];
		$title 			= $_POST['tour-title'];
		$city 			= $_POST['tour-city'];
		$venue 			= $_POST['tour-venue'];
		$datefrom 		= $_POST['tour-datefrom'];
		$dateto 		= $_POST['tour-dateto'];
		$listid 		= $_POST['tour-listid'];

		$id 			= absint($id);
		$has_error 		= true;

		$response = array(
			'success' => false, 
		);

		$args = array(
			'post_title' => $title,
			'post_type' => 'ts_tour',
			'post_status' => 'publish',
		);

		if($id && current_user_can('edit_tour', $id)){
			$args['ID'] = $id;
			$tour_id = wp_update_post($args, true);
		}
		else{
			$args['post_content'] = '';
			$tour_id = wp_insert_post($args, true);
		}
			
		if($tour_id && !is_wp_error($tour_id)) {

			$workshop = isset($workshop) && isset($status) ? 1 : 2;
            $competition = isset($competition) && isset($status) ? 1 : 2;
			$status = isset($status) ? 1 : 2;

			update_post_meta($tour_id, 'status', $status);
			update_post_meta($tour_id, 'workshop', $workshop);
            update_post_meta($tour_id, 'competition', $competition);
			update_post_meta($tour_id, 'city', $city);
			update_post_meta($tour_id, 'venue', $venue);
			update_post_meta($tour_id, 'date_from', date_format(date_create($datefrom),'Y/m/d'));
			update_post_meta($tour_id, 'date_to', date_format(date_create($dateto),'Y/m/d'));
			update_post_meta($tour_id, 'list_id', $listid);
			$response['id'] = $tour_id;
			$has_error = false;
		}

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['tour'] = $status;
			$response['workshop'] = $workshop;
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();		
}

function ajax_close_tour() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$id				= $_POST['id'];
		$tour_id 		= absint($id);
		$has_error 		= true;

		$response = array(
			'success' => false, 
			'id' => $tour_id, 
		);

		if($tour_id && current_user_can('edit_tour', $tour_id)){

			$status = get_post_meta($tour_id, 'status', true);

			if($status == 2) {
				update_post_meta($tour_id, 'workshop', 1);
                update_post_meta($tour_id, 'competition', 1);
				update_post_meta($tour_id, 'status', 1);
				$newstat = 1;
			}
			else {
				update_post_meta($tour_id, 'status', 2);
                update_post_meta($tour_id, 'competition', 2);
				update_post_meta($tour_id, 'workshop', 2);
				$newstat = 2;
			}

			$has_error = false;
		}

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['status'] = $newstat;
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();		
}

function ajax_sched_status() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$id				= $_POST['id'];
		$sched_id 		= absint($id);
		$has_error 		= true;

		$response = array(
			'success' => false, 
			'id' => $sched_id, 
		);

		if($sched_id && current_user_can('edit_event', $sched_id)){

			$status = get_post_status($sched_id);
			$post_status = $status === 'publish' ? 'draft' : 'publish';

		    $schedule = array(
		    	'ID' => $sched_id,
		        'post_status'  => $post_status,
		        'post_type'  => 'ts_event',
		    );  
		    $updated = wp_update_post($schedule);

			if($updated && !is_wp_error($updated)) {
				$has_error = false;
			}
		}

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['status'] = $post_status;
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();		
}

function ajax_remove_coupon() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
		);

		$entry_data = ts_get_session_entry_data($eid);
		$temp_data = $entry_data;

		if(isset($temp_data['discount_code'])) {

			unset($temp_data['discount_code']);
			ts_set_session_entry_data($temp_data, $eid);

			$button_html = '
				<label><input type="text" value="" id="discount-coupon" name="discount-coupon" /></label>
				<button type="button" data-eid="'. $eid .'" class="btn btn-blue btn-applycoupon">Apply Voucher</button>		
			';

			$grand_total = ts_grand_total($eid, $temp_data);
			
			$response['new_grand_total'] = $grand_total;
			$response['button_html'] = $button_html;
			$response['success'] = true;
		}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_remove_participant() {

	if($_POST) :
	
		check_ajax_referer('ts-delete-item', 'token');

		$id = (int)$_POST['id'];
		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
			'id' => $id, 
		);

		$user_id = get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid, $user_id);
		$temp_data = $entry_data;
		$roster = ts_check_value($entry_data, 'roster');
		$participants = ts_check_value($entry_data, 'workshop', 'participants');

		if( is_array($roster) && ! empty($roster) ) {
			$rosterArray = $roster;
			if (($key = array_search($id, $rosterArray)) !== false) {
			    unset($rosterArray[$key]);
			}
			$temp_data['roster'] = $rosterArray;
		}

		if( is_array($participants) && ! empty($participants) ) {

			$participantsArray = $participants;
			if(array_key_exists($id, $participantsArray)) {
				$thisfee = $participantsArray[$id]['fee'];
				$workshop_fee = $entry_data['workshop']['total_fee'];
				$temp_data['workshop']['total_fee'] = $workshop_fee - $thisfee;
				unset($participantsArray[$id]);
			}
			$temp_data['workshop']['participants'] = $participantsArray;
		}

		$total_fee 				= ts_get_total_workshop_fee($eid, $temp_data);
		$discounted_total 		= ts_get_discounted_total_workshop_fee($eid, $temp_data);
		$scholarship_discount 	= ts_get_total_scholarship_discount($eid, $temp_data);
		$teacher_discount 		= ts_get_total_teacher_discount($eid, $temp_data);
		$grand_total 			= ts_grand_total($eid, $temp_data);

		$temp_data['workshop']['total_fee'] = $total_fee;
		$temp_data['workshop']['discounted_total'] = $discounted_total;
		$temp_data['workshop']['scholarship_discount'] = $scholarship_discount;
		$temp_data['workshop']['teacher_discount'] = $teacher_discount;
		$temp_data['grand_total'] = $grand_total;

		ts_set_session_entry_data($temp_data, $eid, $user_id);

		$response['success'] = true;

		echo json_encode($response);

	endif;

    die();		
}

function ajax_remove_observer() {

	if($_POST) :
	
		check_ajax_referer('ts-delete-item', 'token');

		$id = (int)$_POST['id'];
		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
			'id' => $id, 
		);

		$user_id = get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid, $user_id);
		$temp_data = $entry_data;
		$observers = ts_check_value($entry_data, 'workshop', 'observers');

		if( is_array($observers) && ! empty($observers) ) {

			if(array_key_exists($id, $observers)) {
				unset($observers[$id]);
			}
			$temp_data['workshop']['observers'] = $observers;
		}

		$total_fee 				= ts_get_total_workshop_fee($eid, $temp_data);
		$discounted_total 		= ts_get_discounted_total_workshop_fee($eid, $temp_data);
		$scholarship_discount 	= ts_get_total_scholarship_discount($eid, $temp_data);
		$teacher_discount 		= ts_get_total_teacher_discount($eid, $temp_data);
		$grand_total 			= ts_grand_total($eid, $temp_data);

		$temp_data['workshop']['total_fee'] = $total_fee;
		$temp_data['workshop']['discounted_total'] = $discounted_total;
		$temp_data['workshop']['scholarship_discount'] = $scholarship_discount;
		$temp_data['workshop']['teacher_discount'] = $teacher_discount;
		$temp_data['grand_total'] = $grand_total;

		ts_set_session_entry_data($temp_data, $eid, $user_id);

		$response['new_total'] = ts_get_discounted_total_workshop_fee($eid);
		$response['success'] = true;

		echo json_encode($response);

	endif;

    die();		
}

function ajax_remove_munchkin_observer() {

	if($_POST) :
	
		check_ajax_referer('ts-delete-item', 'token');

		$id = (int)$_POST['id'];
		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
			'id' => $id, 
		);

		$user_id = get_current_user_id();
		$entry_data = ts_get_session_entry_data($eid, $user_id);
		$temp_data = $entry_data;
		$munchkin_observers = ts_check_value($entry_data, 'workshop', 'munchkin_observers');

		if( is_array($munchkin_observers) && ! empty($munchkin_observers) ) {

			if(array_key_exists($id, $munchkin_observers)) {
				unset($munchkin_observers[$id]);
			}
			$temp_data['workshop']['munchkin_observers'] = $munchkin_observers;
		}

		$total_fee = ts_get_total_workshop_fee($eid, $temp_data);
		$discounted_total = ts_get_discounted_total_workshop_fee($eid, $temp_data);
		$scholarship_discount = ts_get_total_scholarship_discount($eid, $temp_data);
		$teacher_discount = ts_get_total_teacher_discount($eid, $temp_data);
		$grand_total = ts_grand_total($eid, $temp_data);

		$temp_data['workshop']['total_fee'] = $total_fee;
		$temp_data['workshop']['discounted_total'] = $discounted_total;
		$temp_data['workshop']['scholarship_discount'] = $scholarship_discount;
		$temp_data['workshop']['teacher_discount'] = $teacher_discount;
		$temp_data['grand_total'] = $grand_total;

		ts_set_session_entry_data($temp_data, $eid, $user_id);

		$response['new_total'] = ts_get_discounted_total_workshop_fee($eid);
		$response['success'] = true;

		echo json_encode($response);

	endif;

    die();		
}

function ajax_delete_routine() {

	if($_POST) :
	
		check_ajax_referer('ts-delete-item', 'token');

		$id = absint($_POST['id']);
		$eid = $_POST['eid'];

		$response = array(
			'success' => false, 
			'id' => $id, 
		);

		if(current_user_can('delete_post', $id)){
			$delete = wp_delete_post($id, true);

			$entry_data = ts_get_session_entry_data($eid);
			$temp_data = $entry_data;
			if(isset($temp_data['competition']['routines'][$id])) {
				unset($temp_data['competition']['routines'][$id]);
				$total_fee = ts_get_total_competition_fee($eid, $temp_data);
				$temp_data['competition']['total_fee'] = $total_fee;
				$temp_data['grand_total'] = ts_grand_total($eid, $temp_data);

				ts_set_session_entry_data($temp_data, $eid);
				
				$response['new_total_fee'] = $total_fee;
				$response['new_total_fee_preview'] = number_format($total_fee,2);
				$response['success'] = true;
			}
		}else{
			$response['message'][] = __('Access Denied');
		}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_delete_item() {

	if($_POST) :
	
		check_ajax_referer('ts-delete-item', 'token');

		$id				= (int)$_POST['id'];
		$type			= $_POST['type'];

		$response = array(
			'success' => false, 
			'id' => $id, 
			'type' => $type, 
		);

		$delete = false;

		if($type == 'post') {
			if(current_user_can('delete_post', $id)){
				$delete = wp_delete_post($id, true);
			}else{
				$response['message'][] = __('Access Denied');
			}
		}
		else if($type=='roster') {
			if(current_user_can('delete_post', $id)){
				$delete = wp_delete_post($id, true);
				if($delete && ! is_wp_error($delete)) {
					$entry_data = ts_get_session_entry_data($eid);
					$temp_data = $entry_data;
					$temp_data['workshop']['participants'][$id] = $participant;
					ts_set_session_entry_data($temp_data, $eid);
				}
			}else{
				$response['message'][] = __('Access Denied');
			}
		}
        else if( $type == 'invoice') {
            if(current_user_can('delete_post', $id)){
                $entry_id = (int)get_post_meta($id,'entry_id',true);
                $ts_entry_previous_status = get_post_meta( $entry_id, 'ts_entry_hidden_post_status', true);

                delete_post_meta($entry_id,'ts_entry_invoice_amount');
                delete_post_meta($entry_id,'invoice_due');
                delete_post_meta($entry_id,'invoice_id');
                delete_post_meta($entry_id,'ts_entry_invoice_note');
                delete_post_meta($entry_id,'ts_entry_hidden_post_status');
                $delete = wp_delete_post($id, true);

                ts_change_post_status($entry_id, $ts_entry_previous_status );

            }else{
                $response['message'][] = __('Access Denied');
            }
        }

		if($delete && ! is_wp_error($delete)) {
			$response['success'] = true;
			$response['message'][] = __('Successfully deleted.');
		}else{
			$response['message'][] = __('Unable to delete.');
			array_unshift($response['message'], 'Error');
		}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_delete_all() {

	if($_POST) :
	
		check_ajax_referer('ts-delete-item', 'token');

		$ids = $_POST['ids'];
		$type = $_POST['type'];

		$response = array(
			'success' => false, 
		);

		$has_error = true;

		$ids = explode(',', $ids);

		if($ids && is_array($ids) && $type) {
			$error_count = 0;
			$success_count = 0;
			foreach ($ids as $id) {
				$id = (int)$id;
				if(ts_is_author($id)){
					$deleted = wp_delete_post($id);
					if(is_wp_error($deleted)){
						$error_count++;
					}else{
						$success_count++;	
					}
				}
			}
			if($error_count===0 && $success_count > 0)
				$has_error = false;
		}
		
		if($has_error===true) {
			$response['message'][] = __('Some items were not deleted.');
			array_unshift($response['message'], 'Error');
		}else{
			$response['success'] = true;
			$response['message'][] = __('Items were successfully deleted.');
		}

		echo json_encode($response);

	endif;

    die();		
}

function ajax_pay_invoice() {
    if($_POST) :
        check_ajax_referer('ts-default', 'token');

        $eid	= (int)$_POST['eid'];
        $url	= $_POST['url'];

        $response = array(
            'success' => false,
        );

        if(current_user_can('edit_entry', $eid)) {
            $response['success'] = true;
            $response['redirect'] = $url;
        }

        echo json_encode($response);
    endif;
    die();
}

function ajax_create_invoice() {

    if($_POST) :

        check_ajax_referer('ts-save-item', 'token');

        $id				            = $_POST['entryid'];
        $ts_entry_invoice_amount 	= $_POST['ts-entry-invoice-amount'];
        $ts_entry_invoice_note 		= $_POST['ts-entry-invoice-note'];
        $ts_entry_hidden_post_status= $_POST['ts_entry_hidden_post_status'];

        $id 			= absint($id);
        $has_error 		= true;

        $response = array(
            'success' => false,
            'id' => $id,
            'redirect' => false,
        );

        $invoice_id = false;
        if(isset($ts_entry_invoice_amount)) {
            $ts_entry_invoice_amount = number_format($ts_entry_invoice_amount, 2, '.', '');
            update_post_meta($id, "ts_entry_invoice_amount", $ts_entry_invoice_amount);
            $invoice_id = wp_insert_post(array (
                'post_type' => 'ts_invoice',
                'post_title' => 'Invoice #' . $id,
                'post_status' => 'unpaid',
                'ping_status' => 'closed',
            ));
            if ($id) {
                update_post_meta($invoice_id, 'invoice_amount', $ts_entry_invoice_amount);
                $has_error = false;
            }
        }
        if(isset($ts_entry_invoice_note)) {
            $ts_entry_invoice_note = sanitize_textarea_field( $ts_entry_invoice_note );
            update_post_meta($id, "ts_entry_invoice_note", $ts_entry_invoice_note);
            if ( $invoice_id ) {
                update_post_meta($invoice_id, 'invoice_note', $ts_entry_invoice_note);
            }
        }
        if(isset($ts_entry_hidden_post_status)) {
            $ts_entry_hidden_post_status = sanitize_textarea_field( $ts_entry_hidden_post_status );
            update_post_meta($id, "ts_entry_hidden_post_status", $ts_entry_hidden_post_status);
        }

        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            do_action('ts_invoice_created', $id, $invoice_id);
            $response['success'] = true;
            $response['redirect'] = admin_url('admin.php?page=ts-invoices');
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_download_all_music(){
    if($_POST) :

        check_ajax_referer('ts-default', 'token');

        $id				= intval(sanitize_text_field($_POST['id']));
        $has_error 		= true;

        $response = array(
            'success' => false,
            'id' => $id,
            'redirect' => false,
        );

        $entry_id 		= $id;
        $entry = get_post($entry_id);
        $competition 	= get_post_meta($entry_id, 'competition', true);
        $routines 		= $competition['routines'];
        $author 		= $entry->post_author;
        $user_meta 		= get_userdata($author);
        $user_roles = $user_meta->roles;

        if(in_array('studio', $user_roles)) {
            $name = get_field('director', 'user_'. $author);
        }
        else if(in_array('individual', $user_roles)){
            $name = get_field('name', 'user_'. $author);
        }

        $pretty_filename = get_the_title($entry_id);
        $pretty_filename.=  '-'.$name;
        if(! empty($routines) ){
            // Prepare File
            if ( !is_dir(TS_MUSIC_ZIP_FOLDER) ) {
                mkdir(TS_MUSIC_ZIP_FOLDER, 0775, true);
            }

            $file = tempnam(TS_MUSIC_ZIP_FOLDER, "zip");
            $zip = new ZipArchive();
            $zip->open($file, ZipArchive::OVERWRITE);
            foreach( $routines as  $routine ) {
                // Get the file name
                $music_id = (int)$routine['music'];
                if($music_id) {
                    $name = explode('/', get_attached_file($music_id) );
                    $name = $name[sizeof($name) - 1];
                    $zip->addFile(get_attached_file($music_id), $name);
                }
            }
            // Store the filename before closing the file

            $filename_array = explode('/', $zip->filename);
            $filename = $filename_array[sizeof($filename_array) - 1];

            //Close the file
            $zip->close();
            $has_error = false;
        }

        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
            $response['redirect'] = TS_ZIP_ATTACHMENTS_URL."/ts-music-download.php?ts_pretty_filename=".sanitize_file_name($pretty_filename)."&ts_real_filename=".$filename."&ts_unlink=".true;
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_save_music_info() {
    if($_POST) :

        check_ajax_referer('ts-save-item', 'token');

        $music_id		= $_POST['music-id'];
        $music_title 	= sanitize_text_field($_POST['music-title']);

        $music_id		= absint($music_id);
        $has_error 		= true;

        $response = array(
            'success' => false,
            'id' => $music_id,
        );

        if( get_the_title($music_id) && $music_title ) {
            $file = get_attached_file($music_id);
            $path = pathinfo($file);

            $newfilename = $music_title;
            $newfile = $path['dirname']."/".$newfilename.".".$path['extension'];

            rename($file, $newfile);
            $check = update_attached_file( $music_id, $newfile );
            if( $check ) {
                $has_error = false;
                $music_meta = array(
                    'ID'		=> $music_id,
                    'post_title'	=> $music_title,
                );
                wp_update_post( $music_meta );
            }
        }

        if($has_error === true) {
            $response['message'][] = __('Unable to rename.');
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_save_mark_as_paid() {
    if($_POST) :
        check_ajax_referer('ts-default', 'token');
        $entry_id = (int)$_POST['id'];
        $response = array(
            'success' => false,
            'id' => $entry_id,
        );
        $has_error = true;
        if(current_user_can('edit_post', $entry_id)){
			$user_id = get_post_field( 'post_author', $entry_id );
			$grand_total = get_post_meta( 'grand_total', $entry_id, true );
			do_action('registration_paid', $entry_id, $user_id, 'mail_in_check', $grand_total,	false, 0);
			do_action('registration_completed', $entry_id, $user_id, 'mail_in_check');
            $has_error = false;
        } 
        else{
            $response['message'][] = __('Access Denied');
        }
        if($has_error===true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
            $response['message'][] = __('Entry is now mark as paid.');
        }
        echo json_encode($response);
    endif;
    die();
}

function ajax_save_special_awards() {

    if($_POST) :

        check_ajax_referer('ts-save-item', 'token');

        $tour_city 		= $_POST['tour_city'];
        $special_awards = $_POST['special_awards'];

        $has_error 		= true;

        $response = array(
            'success' => false,
        );

        if($tour_city){
			$choreo12below_id 		= isset($special_awards['twelve_below']['choreography']['routine_id']) ? $special_awards['twelve_below']['choreography']['routine_id'] : '';
			$standnom12below_id 	= isset($special_awards['twelve_below']['standout_nominee']['routine_id']) ? $special_awards['twelve_below']['standout_nominee']['routine_id'] : '';
			$standwin12below_id 	= isset($special_awards['twelve_below']['standout_winner']['routine_id']) ? $special_awards['twelve_below']['standout_winner']['routine_id'] : '';
			$choreo13above_id 		= isset($special_awards['thirteen_above']['choreography']['routine_id']) ? $special_awards['thirteen_above']['choreography']['routine_id'] : '';
			$standnom13above_id 	= isset($special_awards['thirteen_above']['standout_nominee']['routine_id']) ? $special_awards['thirteen_above']['standout_nominee']['routine_id'] : '';
			$standwin13above_id 	= isset($special_awards['thirteen_above']['standout_winner']['routine_id']) ? $special_awards['thirteen_above']['standout_winner']['routine_id'] : '';
			$studio_innovator 		= isset($special_awards['studio_innovator']) ? $special_awards['studio_innovator'] : '';

	        update_post_meta($choreo12below_id, 'special_award', 'Choreography Award');
	        update_post_meta($standnom12below_id, 'special_award', 'Judges Standout Nominee');
	        update_post_meta($standwin12below_id, 'special_award', 'Judges Standout Winner');
	        update_post_meta($choreo13above_id, 'special_award', 'Choreography Award');
	        update_post_meta($standnom13above_id, 'special_award', 'Judges Standout Nominee');
	        update_post_meta($standwin13above_id, 'special_award', 'Judges Standout Winner');

	        if($studio_innovator!==''){
				$args = array(
					'role'         => 'studio',
					'meta_key'     => 'studio',
					'meta_value'   => $studio_innovator,
					'meta_compare' => 'LIKE',
					'number'       => 1,
				 ); 
				$users = get_users($args);
				print_r($users);
				$special_awards['studio_innovator'] = $users[0]->ID;
			}

	        update_post_meta($tour_city, 'special_awards', $special_awards);
	        $has_error = false;
        }

        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_save_scholarships() {

    if($_POST) :

        check_ajax_referer('ts-save-item', 'token');

        $tour_city 		= $_POST['tour_city'];
        $scholarships 	= $_POST['scholarships'];

        $has_error 		= true;

        $response = array(
            'success' => false,
        );

        if($tour_city){
	        update_post_meta($tour_city, 'scholarships', $scholarships);

	        foreach ($scholarships as $key => $value) {
	        	update_post_meta($key, 'number', $value['number']);
	        	update_post_meta($key, 'scholarship', $value['title']);
	        }
	        $has_error = false;
        }

        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_load_participant_info() {

    if($_POST) :

        check_ajax_referer('ts-default', 'token');

		$id				= absint($_POST['id']);
		$temp_id		= absint($_POST['tempid']);
		$has_error 		= true;

        $response = array(
            'success' => false,
            'id' => $id,
            'temp_id' => $temp_id,
        );

        if(ts_post_exists_by_id($id)){
        	$response['agediv'] = ts_participant_agediv($id);
        	$response['studio'] = ts_post_studio($id);
	        $has_error = false;
        }

        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_publish_results() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$id				= $_POST['id'];
		$tour_id 		= absint($id);
		$has_error 		= true;

		$response = array(
			'success' => false, 
			'id' => $tour_id, 
		);

		if($tour_id && current_user_can('edit_tour', $tour_id)){
			$status = get_post_meta($tour_id, 'results_status', true);
			$newval = ! $status || $status=='draft' ? 'publish' : 'draft';
	        update_post_meta($tour_id, 'results_status', $newval);
	        do_action('publish_results', $tour_id);
	        $has_error = false;
		}

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['status'] = $newval;
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();	
}

function ajax_add_critique() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$routine_id 	= absint($_POST['routine_id']);
		$attachment_id 	= absint($_POST['attachment_id']);
		$has_error 		= true;

		$response = array(
			'success' => false, 
		);

		if($routine_id && $attachment_id){
			update_post_meta($routine_id, 'critique', $attachment_id);
	        $has_error = false;
		}

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();	
}

function ajax_add_critiques() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$tour_id 		= absint($_POST['tour_id']);
		$attachments 	= json_decode(stripslashes($_POST['attachments']));
		$has_error 		= true;

		$response = array(
			'success' => false, 
		);

		foreach ($attachments as $att) {
			$routine_num = absint(substr($att->filename, 0, 3));
			$routine = ts_get_routine_by_num($routine_num, $tour_id);
			update_post_meta($routine->ID, 'critique', $att->id);
		}
		$has_error = false;

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();	
}

function ajax_remove_critique() {

	if($_POST) :
	
		check_ajax_referer('ts-default', 'token');

		$post_id 		= absint($_POST['post_id']);
		$has_error 		= true;

		$response = array(
			'success' => false, 
		);

		if($post_id){
			delete_post_meta($post_id, 'critique');
	        $has_error = false;
		}

		if($has_error === true) {
			array_unshift($response['message'], 'Error');
		}
		else{
			$response['success'] = true;
		}
		echo json_encode($response);

	endif;

    die();	
}

function ajax_load_routine_info() {

    if($_POST) :

        check_ajax_referer('ts-default', 'token');

		$routine_number	= absint($_POST['routine_number']);
		$tour_id		= absint($_POST['tour_id']);
		$row			= $_POST['row'];
		$has_error 		= true;

        $response = array(
            'success' => false,
            'row' => $row,
        );

        $tour_routines = ts_tour_routines_ids($tour_id);

        if(is_array($tour_routines) && ! empty($tour_routines)) {
			$args = array(
				'post__in' => $tour_routines,
				'posts_per_page' => 1,
				'meta_query' => array(
					array(
						'key'     => 'routine_number',
						'value'   => $routine_number,
						'compare' => '=',
					),
				),
			);
			$routine = ts_get_posts('ts_routine', -1, $args);
	        if(ts_post_exists_by_id($routine[0]->ID)){
	        	$routine_id = $routine[0]->ID;
	        	$response['routine_id'] = $routine_id;
	        	$response['name'] = get_the_title($routine_id);
	        	$response['studio'] = ts_post_studio($routine_id);
		        $has_error = false;
	        }
	    }
	        
        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_reset_competition_schedule() {

    if($_POST) :

        check_ajax_referer('ts-default', 'token');

		$id			= absint($_POST['id']);
		$return 	= $_POST['return'];
		$has_error 	= true;

        $response = array(
            'success' => false,
            'redirect' => $return,
        );

        if(ts_post_exists_by_id($id)){
        	delete_post_meta($id, 'schedule_saved');
	        $has_error = false;
        }

        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
        }
        echo json_encode($response);

    endif;

    die();
}

function ajax_save_routine_scores() {

    if($_POST) :

        check_ajax_referer('ts-default', 'token');

		$id			= absint($_POST['id']);
		$judge1		= ($_POST['judge1']);
		$judge2		= ($_POST['judge2']);
		$judge3		= ($_POST['judge3']);
		$has_error 	= true;

        $response = array(
            'success' => false,
            'id' => $id,
        );

        if(ts_post_exists_by_id($id)){
        	$total_score = $judge1+$judge2+$judge3;
			update_post_meta($id, 'judges_scores', array($judge1,$judge2,$judge3));
			update_post_meta($id, 'total_score', $total_score);
            $response['total_score'] = $total_score;
	        $has_error = false;
        }

        if($has_error === true) {
            array_unshift($response['message'], 'Error');
        }
        else{
            $response['success'] = true;
        }
        echo json_encode($response);

    endif;

    die();	
}