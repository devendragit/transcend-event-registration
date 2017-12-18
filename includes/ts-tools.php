<?php
function ts_trim_duplicate($array) {
	foreach($array as $key => $value) {
		foreach($array as $key2 => $value2) {
			if($key != $key2 && $value === $value2) {
				unset($array[$key]);
			}
		}
	}
	return $array;
}

function ts_trim_duplicate_objects($objects) {
	$filtered = array();

	foreach ($objects as $current) {
	    if ( ! in_array($current, $filtered)) {
	        $filtered[] = $current;
	    }
	}    
	return $filtered;
}

function ts_session_start() {
    if(!session_id()) {
        session_start();
    }
}

function ts_session_end() {

    session_destroy ();
}

function ts_json_to_array($file=false) {

	if(! $file || empty($file))
		return false;

	$json = file_get_contents($file);
	$array = json_decode($json, true);

	return $array;
}

function ts_in_date_range($start, $end, $date){

	$start = strtotime($start);
	$end = strtotime($end);
	$date = strtotime($date);

	return (($date >= $start) && ($date <= $end));
}

function ts_get_days_before_date($end, $start=false) {

	$date1 = new DateTime($end);
	if($start) {
		$date2 = new DateTime($start);
	}
	else {
		$date2 = new DateTime();
	}
	$diff = $date2->diff($date1);
	return $diff->days;
}

function ts_get_the_age($birthdate) {

	$from = new DateTime($birthdate);
	$to   = new DateTime('today');
	$age = $from->diff($to)->y;	

	return $age;
}

function ts_check_value($array, $index1, $index2=false, $index3=false) {

	if($index1 && ! $index2 && ! $index3) {
		$data = isset($array[$index1]) && ! empty($array[$index1]) ? $array[$index1] : array();
	}
	else if($index1 && $index2 && ! $index3) {
		$data = isset($array[$index1][$index2]) && ! empty($array[$index1][$index2]) ? $array[$index1][$index2] : array();
	}
	else if($index1 && ! $index2 && $index3) {
		$data = isset($array[$index1][$index2][$index3]) && ! empty($array[$index1][$index2][$index3]) ? $array[$index1][$index2][$index3] : array();
	}
	return $data;
}

function ts_get_array_index($array, $value) {

	$found = current(array_filter($array, function($item) use($value) {
	    return isset($item['id']) && $value == $item['id'];
	}));	
	return $found;
}

function ts_random_password($length=9) {

	$chars = "abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	return substr(str_shuffle($chars),0,$length);
}

function ts_add_suffix($num) {
	if (!in_array(($num % 100),array(11,12,13))){
		switch ($num % 10) {
			case 1:  return $num.'st';
			case 2:  return $num.'nd';
			case 3:  return $num.'rd';
		}
	}
	return $num.'th';
}

function ts_get_param($param) {
	if(isset($_GET[$param]) && $_GET[$param] != '') {
		return $_GET[$param];
	}
	else {
		return false;
	}
}

/* Mailchimp */

function ts_add_mailchimp_subscribers($list_id, $email, $fname='', $lname='') {

	$fields = array(
		'email_address' => $email,
		'status'        => 'subscribed',
		'merge_fields'  => array(
		    'FNAME'     => $fname,
		    'LNAME'     => $lname,
		)
	);	

	$member_id = md5(strtolower($email));
	$endpoint = '/lists/'. $list_id .'/members/'. $member_id;
	$result = ts_mailchimp_curl_submit($endpoint, $fields, 'PUT');    
    
    return $result;
}

function ts_mailchimp_curl_submit($endpoint, $fields=array(), $method='GET') {

    $data_center = substr(MC_API_KEY,strpos(MC_API_KEY,'-')+1);
	$url = 'https://' . $data_center . '.api.mailchimp.com/3.0'. $endpoint;

	$fields = json_encode($fields);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . MC_API_KEY);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);                                                                                                                 

    $result = curl_exec($ch);
    $result = json_decode($result);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    
    $output = array(
    	'status'  => $http_code,
    	'result'  => $result,
    );

    return $output;
}

/** Wordpress Tools **/

function ts_post_exists_by_id($post_id) {

	$status = get_post_status($post_id);
	$check = is_string($status) && $status!='trash' ? $post_id : false;
	return $check;
}

function ts_post_exists_by_type($post_id, $post_type='post') {

	$args = array(
		'posts_per_page'   => 1,
		'include'          => $post_id,
		'post_type'        => $post_type,
		'post_status'      => 'publish',
	);

	$posts = get_posts($args);

	if($posts && !empty($posts)) {
		return true;
	}
	else {
		return false;
	}
}

function ts_post_exists($title, $content = '', $date = '', $type = '') {
    global $wpdb;
 
    $post_title = wp_unslash(sanitize_post_field('post_title', $title, 0, 'db'));
    $post_content = wp_unslash(sanitize_post_field('post_content', $content, 0, 'db'));
    $post_date = wp_unslash(sanitize_post_field('post_date', $date, 0, 'db'));
    $post_type = wp_unslash(sanitize_post_field('post_type', $type, 0, 'db'));
 
    $query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
    $args = array();

    if (!empty ($type)) {
        $query .= ' AND post_type = %s';
        $args[] = $post_type;
    }
 
    if (!empty ($date)) {
        $query .= ' AND post_date = %s';
        $args[] = $post_date;
    }
 
    if (!empty ($title)) {
        $query .= ' AND post_title = %s';
        $args[] = $post_title;
    }
 
    if (!empty ($content)) {
        $query .= ' AND post_content = %s';
        $args[] = $post_content;
    }
 
    if (!empty ($args))
        return (int) $wpdb->get_var($wpdb->prepare($query, $args));
 
    return 0;
}

function ts_insert_attachment($file_handler, $post_id, $set_thumb = false) {
	
    if(UPLOAD_ERR_OK !== $_FILES[ $file_handler ]['error'])
        return false; 

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attach_id = media_handle_upload($file_handler, $post_id);

    if($attach_id && $set_thumb)
        update_post_meta($post_id, '_thumbnail_id', $attach_id);
    if($attach_id)
        update_post_meta($post_id, $file_handler, $attach_id);

    return $attach_id;
}

/* This Plugin */

function ts_create_scores_array( $schedules ) {

    $scores_array = $schedules;

    for( $i=0 ;$i<count($scores_array); $i++ ){
        if(is_array($scores_array[$i]['lineup'])) {
            for( $y=0 ;$y<count($scores_array[$i]['lineup']); $y++ ) {
                if( 'Judges Break' == $scores_array[$i]['lineup'][$y]['action'] || 'Awards' == $scores_array[$i]['lineup'][$y]['action'] ) {
                    unset($scores_array[$i]['lineup'][$y]);
                    $scores_array[$i]['lineup'] = array_values($scores_array[$i]['lineup']);
                }
                unset($scores_array[$i]['lineup'][$y]['action']);
                $scores_array[$i]['lineup'][$y]['judge_1_score'] = 0;
                $scores_array[$i]['lineup'][$y]['judge_2_score'] = 0;
                $scores_array[$i]['lineup'][$y]['judge_3_score'] = 0;                
                $scores_array[$i]['lineup'][$y]['score'] = 0;
            }
        }
    }

    return $scores_array;
}

function ts_search_in_array($SearchArray, $query, $all = 0, $Return = 'direct') {
    $SearchArray = json_decode(json_encode($SearchArray), true);
    $ResultArray = array();
    if (is_array($SearchArray)) {
        $desen = "@[\s*]?[\'{1}]?([a-zA-Z\ç\Ç\ö\Ö\ş\Ş\ı\İ\ğ\Ğ\ü\Ü[:space:]0-9-_]*)[\'{1}]?[\s*]?(\<\=|\>\=|\=|\!\=|\<|\>)\s*\'([a-zA-Z\ç\Ç\ö\Ö\ş\Ş\ı\İ\ğ\Ğ\ü\Ü[:space:]0-9-_:]*)\'[\s*]?(and|or|\&\&|\|\|)?@si";
        $DonenSonuc = preg_match_all($desen, $query, $Result);
        if ($DonenSonuc) {
            foreach ($SearchArray as $i => $ArrayElement) {
                if (is_array($ArrayElement)) {
                    $SearchStatus = 0;
                    $EvalString = "";
                    for ($r = 0; $r < count($Result[1]); $r++):
                        if ($Result[2][$r] == '=') {
                            $Operator = "==";
                        } elseif ($Result[2][$r] == '!=') {
                            $Operator = "!=";
                        } elseif ($Result[2][$r] == '>=') {
                            $Operator = ">=";
                        } elseif ($Result[2][$r] == '<=') {
                            $Operator = "<=";
                        } elseif ($Result[2][$r] == '>') {
                            $Operator = ">";
                        } elseif ($Result[2][$r] == '<') {
                            $Operator = "<";
                        } else {
                            $Operator = "==";
                        }
                        $AndOperator = "";
                        if ($r != count($Result[1]) - 1) {
                            $AndOperator = $Result[4][$r] ?: 'and';
                        }
                        $EvalString .= '("' . $ArrayElement[$Result[1][$r]] . '"' . $Operator . '"' . $Result[3][$r] . '") ' . $AndOperator . ' ';
                    endfor;
                    eval('if( ' . $EvalString . ' ) $SearchStatus = 1;');
                    if ($SearchStatus === 1) {
                        if ($all === 1) {
                            if ($Return == 'direct') :
                                $ResultArray[$i] = is_array($ResultArray[$i]) ? $ResultArray[$i] : [];
                                $ResultArray[$i] = array_merge($ResultArray[$i], $ArrayElement);
                            elseif ($Return == 'array') :
                                $ResultArray['index'][] = $i;
                                $ResultArray['array'] = is_array($ResultArray['array']) ? $ResultArray['array'] : [];
                                $ResultArray['array'] = array_merge($ResultArray['array'], $ArrayElement);
                            endif;
                        } else {
                            if ($Return == 'direct') :
                                $ResultArray = $i;
                            elseif ($Return == 'array') :
                                $ResultArray['index'] = $i;
                            endif;
                            return $ResultArray;
                        }
                    }
                    if ($all === 1 && is_array($ArrayElement)) {
                        if ($Return == 'direct') :
                            $args = func_get_args();
                            $ChildResult = ts_search_in_array($ArrayElement, $args[1], $args[2], $args[3]);
                            if (count($ChildResult) > 0):
                                $ResultArray[$i] = is_array($ResultArray[$i]) ? $ResultArray[$i] : [];
                                $ResultArray[$i] = array_merge($ResultArray[$i], $ChildResult);
                            endif;
                        endif;
                    }
                }
            }
            if ($all === 1) {
                return $ResultArray;
            }
        }
    }
    return false;
}

function ts_sort_score($x, $y) {

    return $x['score'] < $y['score'];
}

function ts_multi_array_search($array, $search) {
    $result = array();
    foreach ($array as $key => $value) {
        foreach ($search as $k => $v) {
            if (!isset($value[$k]) || $value[$k] != $v) {
                continue 2;
            }
        }
        $result[] = $value;
    }
    return $result;
}

function ts_is_tour_close($tour_id) {

	$date_from 	= get_post_meta($tour_id, 'date_from', true);
	$date_to 	= get_post_meta($tour_id, 'date_to', true);
	$status 	= get_post_meta($tour_id, 'status', true);

	return ($date_from && ts_get_days_before_date($date_from) <= 0) || $status==2 ? true : false;
}

function ts_tour_routines_ids($tour_id) {

	$args = array(
		'post_status' => array('paid', 'paidcheck'),
		'meta_query' => array(
			array(
				'key'     => 'tour_city',
				'value'   => $tour_id,
				'compare' => '=',
			),
		),
	);

	$entries = ts_get_posts('ts_entry', -1, $args);

	if(! empty($entries)){
		$routines_array = array();
		foreach ($entries as $e) {
			$competition = get_post_meta($e->ID, 'competition', true);
			$routines = $competition['routines'];
            if(! empty($routines)) {
                $routine_ids = array_keys($routines);
                $routines_array = array_merge($routine_ids, $routines_array); 
            }
		}
	}

	return $routines_array;
}

function ts_tour_studio_ids($tour_id) {

	$args = array(
		'post_status' => array('paid', 'paidcheck'),
		'meta_query' => array(
			array(
				'key'     => 'tour_city',
				'value'   => $tour_id,
				'compare' => '=',
			),
		),
	);

	$entries = ts_get_posts('ts_entry', -1, $args);

	if(! empty($entries)){
		$studio_array = array();
		foreach ($entries as $e) {
			$studio_id = $e->post_author;
            if(! in_array($studio_id, $studio_array)) {
            	$studio_array[] = $studio_id;
            }
		}
	}

	return $studio_array;
}

function ts_tour_routines_by_number($tour_id) {

	$routines = array();
	$routine_ids = ts_tour_routines_ids($tour_id);

	if(! empty($routine_ids)){
		$args = array(
			'include' => $routine_ids,
	        'orderby' => 'meta_value_num',
			'meta_key' => 'routine_number',
	        'order' => 'ASC',
		);
		$routines = ts_get_posts('ts_routine', -1, $args);
	}	
	return $routines;
}

function ts_tour_participants($tour_id) {

	$args = array(
		'post_status' => array('paid', 'paidcheck'),
		'meta_query' => array(
			array(
				'key'     => 'tour_city',
				'value'   => $tour_id,
				'compare' => '=',
			),
		),
	);

	$entries = ts_get_posts('ts_entry', -1, $args);

	if(! empty($entries)){
		$participants_array = array();
		foreach ($entries as $e) {
			$workshop = get_post_meta($e->ID, 'workshop', true);
			$participants = $workshop['participants'];
            if(! empty($participants)) {
                $participants_ids = array_keys($participants);
                $participants_array = array_merge($participants_ids, $participants_array); 
            }
		}
	}

	return $participants_array;
}

function ts_post_studio($participant_id) {
	$post = get_post($participant_id);
	$author = $post->post_author;
	$studio = get_field('studio', 'user_'. $author);
	return $studio;
}

function ts_post_author_role($participant_id) {
	$post = get_post($participant_id);
	$author = $post->post_author;
	$authordata = get_userdata($author);
    $author_roles = $authordata->roles;
    $author_role = array_shift($author_roles);
    return $author_role;
}

function ts_participant_agediv($participant_id) {
	$agediv = wp_get_object_terms($participant_id, 'ts_agediv');
	$agediv_name = $agediv[0]->name;
	return $agediv_name;
}

function ts_participant_number($participant_id) {
	return get_post_meta($participant_id, 'participant_number', true);
}

function ts_get_routine_by_num($routine_number, $tour_id) {

	$args = array(
		'posts_per_page' => 1,
		'post_type' => 'ts_routine',
		'meta_key' => 'routine_number',
		'meta_value' => $routine_number,
	);

	if($tour_id) {
		$routine_ids = ts_tour_routines_ids($tour_id);
		if(! empty($routine_ids))
			$args['include'] = $routine_ids;
	}

	$posts = get_posts($args);

	if($posts && ! empty($posts)) {
		return $posts[0];
	}
	else {
		return false;
	}
}
function ts_import_studios(){

	$file = TS_LIBRARIES .'studios.csv'; 
	$handle = fopen($file,"r"); 
	$result_array = array();

	if ($handle !== FALSE) {
		$count=0;
		while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {

			$count++;

		    $Studio 			= $data[0];
            $Studio_Director 	= $data[1];
            $Invitees 			= $data[2];
            $Address 			= $data[3];
            $City 				= $data[4];
            $State 				= $data[5];
            $Zipcode 			= $data[6];
            $Country 			= $data[7];
            $Email 				= $data[8];
            $Phone 				= $data[9];
            $Username 			= $data[10];
            $Password 			= $data[11]; 		

			if($count===1) continue;
			if($Email==='') continue;

			$userdata = array(
			    'user_login'  =>  $Username,
			    'user_pass'   =>  $Password,
			    'user_email'   =>  $Email,
			    'role' => 'studio'
			);

			$user_id = wp_insert_user( $userdata ) ;

			if ( ! is_wp_error( $user_id ) ) {
			    update_field('studio', $Studio, 'user_'. $user_id);
			    update_field('studio_director', $Studio_Director, 'user_'. $user_id);
			    update_field('invitees', $Invitees, 'user_'. $user_id);
			    update_field('address', $Address, 'user_'. $user_id);
			    update_field('city', $City, 'user_'. $user_id);
			    update_field('state', $State, 'user_'. $user_id);
			    update_field('zipcode', $Zipcode, 'user_'. $user_id);
			    update_field('country', $Country, 'user_'. $user_id);
			    update_field('phone', $Phone, 'user_'. $user_id);
			}			

		}
		fclose($handle);
	}
}

function ts_import_individual(){

	$file = TS_LIBRARIES .'individual.csv'; 
	$handle = fopen($file,"r"); 
	$result_array = array();

	if ($handle !== FALSE) {
		$count=0;
		while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

			$count++;

		    $Name 		= $data[0];
            $BirthDate 	= $data[1];
            $Parent 	= $data[2];
            $Studio 	= $data[3];
            $Address 	= $data[4];
            $City 		= $data[5];
            $State 		= $data[6];
            $Zipcode 	= $data[7];
            $Country 	= $data[8];
            $Cell 		= $data[9];
            $Email 		= $data[10];
            $Username 	= $data[11];
            $Password 	= $data[12]; 		

			if($count===1) continue;
			if($Email==='') continue;

			$userdata = array(
			    'user_login'  =>  $Username,
			    'user_pass'   =>  $Password,
			    'user_email'   =>  $Email,
			    'role' => 'individual'
			);

			$user_id = wp_insert_user( $userdata ) ;

			if ( ! is_wp_error( $user_id ) ) {
			    update_field('name', $Name, 'user_'. $user_id);
			    update_field('birth_date', $BirthDate, 'user_'. $user_id);
			    update_field('parent', $Parent, 'user_'. $user_id);
			    update_field('studio', $Studio, 'user_'. $user_id);
			    update_field('address', $Address, 'user_'. $user_id);
			    update_field('city', $City, 'user_'. $user_id);
			    update_field('state', $State, 'user_'. $user_id);
			    update_field('zipcode', $Zipcode, 'user_'. $user_id);
			    update_field('country', $Country, 'user_'. $user_id);
			    update_field('cell', $Cell, 'user_'. $user_id);
			}	
		}
		fclose($handle);
	}
}

function ts_export_individual() {

	$args = array(
		'role' => 'individual',
	 ); 
	$users = get_users( $args );

	if($users) {
		$userArray = array();
	     foreach ($users as $key => $user) {
	     	$user_id = $user->ID;
	     	$email = $user->user_email;
	     	$username = $user->user_login;
	     	$password = $username. '123';
	        $userArray[$key]['name'] 		= get_field('name', 'user_'. $user_id);
	        $userArray[$key]['birth_date'] 	= get_field('birth_date', 'user_'. $user_id);
	        $userArray[$key]['parent'] 		= get_field('parent', 'user_'. $user_id);
	        $userArray[$key]['studio'] 		= get_field('studio', 'user_'. $user_id);
	        $userArray[$key]['address'] 	= get_field('address', 'user_'. $user_id);
	        $userArray[$key]['city'] 		= get_field('city', 'user_'. $user_id);
	        $userArray[$key]['state'] 		= get_field('state', 'user_'. $user_id);
	        $userArray[$key]['zipcode'] 	= get_field('zipcode', 'user_'. $user_id);
	        $userArray[$key]['country'] 	= get_field('country', 'user_'. $user_id);
	        $userArray[$key]['cell'] 		= get_field('cell', 'user_'. $user_id);
	        $userArray[$key]['email'] 		= $email;
	        $userArray[$key]['username'] 	= $username;
	        $userArray[$key]['password'] 	= $password;
	    }
	}
	$file = TS_LIBRARIES .'individual.csv';
	$output = fopen($file, 'w');
	fputcsv($output, array('Name','Date of Birth','Parent','Studio Name','Address','City','State','Zipcode','Country','Cell','Email','Username','Password'));

	foreach($userArray as $u) {
	    fputcsv($output, $u);
	}
	
	fclose($output);
}

function ts_create_terms() {

	$entry_types = array('Studio', 'Individual');

	foreach ($entry_types as $key=>$value) {
		$order = (int)$key+1;
		$type = term_exists($value, 'ts_entry_type');
		if ($type == 0 || $type == null) {
			$term = wp_insert_term($value, 'ts_entry_type');
			$term_id = $term->term_id;
			update_term_meta($term_id, 'type_order', $order);
		}
	}

	$roster_types = array('Dancer', 'Teacher');

	foreach ($roster_types as $key=>$value) {
		$order = (int)$key+1;
		$type = term_exists($value, 'ts_rostertype');
		if ($type == 0 || $type == null) {
			$term = wp_insert_term($value, 'ts_rostertype');
			$term_id = $term->term_id;
			update_term_meta($term_id, 'type_order', $order);
		}
	}

	$age_divisions = array('Munchkin', 'Mini', 'Junior', 'Teen', 'Senior', 'Pro', 'Teacher');

	foreach ($age_divisions as $key=>$value) {
		$order = (int)$key+1;
		$div = term_exists($value, 'ts_agediv');
		if ($div == 0 || $div == null) {
			$term = wp_insert_term($value, 'ts_agediv');
			if($term && !is_wp_error($term)) {
				$term_id 	= $term->term_id;
				$term 		= get_term($term_id, 'ts_agediv');
				$term_slug 	= $term->slug;

				$fee_meta 	= ts_get_fees_meta();

				update_term_meta($term_id, 'div_order', $order);
				update_term_meta($term_id, 'fee_early', $fee_meta[$term_slug]['fee_early']);
				update_term_meta($term_id, 'fee_standard', $fee_meta[$term_slug]['fee_standard']);
				update_term_meta($term_id, 'fee_early_oneday', $fee_meta[$term_slug]['fee_early_oneday']);
				update_term_meta($term_id, 'fee_standard_oneday', $fee_meta[$term_slug]['fee_standard_oneday']);
			}
		}
	}

	$schedule_types = array('Workshop', 'Competition');

	foreach ($schedule_types as $key=>$value) {
		$order = (int)$key+1;
		$type = term_exists($value, 'ts_schedules_type');
		if ($type == 0 || $type == null) {
			$term = wp_insert_term($value, 'ts_schedules_type');
			$term_id = $term->term_id;
			update_term_meta($term_id, 'type_order', $order);
		}
	}

	$adjudicated_awards = array('Platinum', 'High Silver', 'High Gold', 'Silver', 'Gold', 'Bronze');

	foreach ($adjudicated_awards as $key=>$value) {
		$order = (int)$key+1;
		$div = term_exists($value, 'ts_adjudicated_awards');
		if ($div == 0 || $div == null) {
			$term = wp_insert_term($value, 'ts_adjudicated_awards');
			if($term && !is_wp_error($term)) {
				$term_id 	= $term->term_id;
				$term 		= get_term($term_id, 'ts_adjudicated_awards');
				$term_slug 	= $term->slug;

				$awards_meta 	= ts_get_adjudicated_awards();

				update_term_meta($term_id, 'div_order', $order);
				update_term_meta($term_id, 'min_score', $awards_meta[$term_slug]['min_score']);
				update_term_meta($term_id, 'high_score', $awards_meta[$term_slug]['high_score']);
			}
		}
	}
}

function ts_create_tour_posts() {

	$tours = ts_get_tour_cities();

	foreach ($tours as $t) {
		if(! ts_post_exists($t['title'])) {

			$author = get_user_by('login', 'transcend_admin');

			$entry = array(
				'post_title' => $t['title'],
				'post_type' => 'ts_tour',
				'post_status' => 'publish',
				'author' => $author->ID
			);

			$updated = wp_insert_post($entry, true);

			if($updated && !is_wp_error($updated)) {

				$date_from 	= date_format(date_create($t['date_from']),'Y/m/d');
				$date_to 	= date_format(date_create($t['date_to']),'Y/m/d');
				$cron_timestamp = ts_get_local_timestamp(date('Y/m/d', strtotime($date_to . "+1 days")));

				update_post_meta($updated, 'date_from', $date_from);
				update_post_meta($updated, 'date_to', $date_to);
				update_post_meta($updated, 'venue', $t['venue']);
				update_post_meta($updated, 'city', $t['city']);
				wp_schedule_single_event($cron_timestamp, 'ts_cron_jobs', array( $date_to ) );
			}
		}
	}
}

function ts_update_agediv_fees() {

	$age_divisions = array('Munchkin', 'Mini', 'Junior', 'Teen', 'Senior', 'Pro', 'Teacher');

	foreach ($age_divisions as $ad) {

		$term = get_term_by('name', $ad, 'ts_agediv');

		if ($term) {
			$term_id 	= $term->term_id;
			$term_slug 	= $term->slug;

			$fee_meta 	= ts_get_fees_meta();

			update_term_meta($term_id, 'fee_early', $fee_meta[$term_slug]['fee_early']);
			update_term_meta($term_id, 'fee_standard', $fee_meta[$term_slug]['fee_standard']);
			update_term_meta($term_id, 'fee_early_oneday', $fee_meta[$term_slug]['fee_early_oneday']);
			update_term_meta($term_id, 'fee_standard_oneday', $fee_meta[$term_slug]['fee_standard_oneday']);
		}
	}
}

function ts_update_agediv_order() {

	$age_divisions = array('Munchkin', 'Mini', 'Junior', 'Teen', 'Senior', 'Pro', 'Teacher');

	foreach ($age_divisions as $key=>$value) {

		$order = (int)$key+1;
		$term  = get_term_by('name', $value, 'ts_agediv');

		if ($term) {
			$term_id = $term->term_id;
			update_term_meta($term_id, 'div_order', $order);
		}
	}
}

function ts_update_agedivs() {

	$term 	 = get_term_by('name', 'Munchkins', 'ts_agediv');
	$term_id = $term->term_id;
	wp_update_term($term_id, 'ts_agediv', array('name' => 'Munchkin'));
}

function ts_update_tour_posts() {

	$tours = ts_get_tour_cities();

	foreach ($tours as $t) {
		if($id = ts_post_exists($t['title'])) {

			$entry = array(
				'ID' => $id,
				'post_title' => $t['title'],
				'post_type' => 'ts_tour',
			);

			$updated = wp_update_post($entry, true);

			if($updated && !is_wp_error($updated)) {

				$date_from 	= date_format(date_create($t['date_from']),'Y/m/d');
				$date_to 	= date_format(date_create($t['date_to']),'Y/m/d');

				update_post_meta($updated, 'date_from', $date_from);
				update_post_meta($updated, 'date_to', $date_to);
				update_post_meta($updated, 'venue', $t['venue']);
				update_post_meta($updated, 'city', $t['city']);
			}
		}
	}
}

function ts_update_entries() {

	$args = array(
		'post_status' => array('any'),
	);
	$entries = ts_get_posts('ts_entry', -1, $args);

	if($entries) {
		foreach ($entries as $entry) {
			setup_postdata($entry);
			$entry_id 		= $entry->ID;
			$workshop 		= get_post_meta($entry_id, 'workshop', true);
			$tour_city 		= $workshop['tour_city'];
			if(isset($tour_city)) {
				$date_from 	= get_post_meta($tour_city, 'date_from', true);
				$date_to 	= get_post_meta($tour_city, 'date_to', true);
				update_post_meta($entry_id, 'tour_date', date_format(date_create($date_from),'Y/m/d'));
				update_post_meta($entry_id, 'tour_date_end', date_format(date_create($date_to),'Y/m/d'));
				update_post_meta($entry_id, 'tour_city', $tour_city);
			}
			$status = get_post_status($entry_id);
			if(ts_is_paid($entry_id)){
				$paid_amount 	= get_post_meta($entry_id, 'paid_amount', true);
				$grand_total 	= get_post_meta($entry_id, 'grand_total', true);
				$discount_code 	= get_post_meta($entry_id, 'discount_code', true);
				if(! $paid_amount) {
					update_post_meta($entry_id, 'paid_amount', $grand_total);
				}
				if($discount_code) {
					update_post_meta($entry_id, 'discount_code_applied', true);
				}
			}
		}
	}
}

function ts_update_roster_order() {

	$roster = ts_get_posts('ts_studio_roster');
	$siblings = ts_get_posts('ts_sibling');

	$items = array_merge($roster, $siblings);

	foreach ($items as $r) {
		$rid = $r->ID;
		$age_div = wp_get_object_terms($rid, 'ts_agediv');
		$div_id = $age_div[0]->term_id;
		$div_order = get_term_meta($div_id, 'div_order', true);
		update_post_meta($rid, 'age_cat_order', $div_order);
	}
}

function ts_update_entry() {
    $postid = 905;
    $updated = false;
    if(ts_post_exists_by_id($postid)) {
        $args = array(
            'ID' =>  $postid,
            'post_status' => 'unpaidcheck'
        );
        $updated = wp_update_post($args);
        if($updated && !is_wp_error($updated)) {
            delete_post_meta($updated, 'completed');
            delete_post_meta($updated, 'date_paid');
            delete_post_meta($updated, 'paid_amount');
            delete_post_meta($updated, 'paid_amount_competition');
            delete_post_meta($updated, 'discount_code_applied');
        }
    }
}

function ts_update_routines() {
    $args = array(
        'post_status' => array('any'),
    );
    $routines = ts_get_posts('ts_routine', -1, $args);

    if($routines) {
        foreach ($routines as $routine) {
            setup_postdata($routine);
            $id = $routine->ID;
            $agediv = get_post_meta($id, 'agediv', true);
            $term = get_term_by('name', $agediv, 'ts_agediv');
            $agediv_order = get_term_meta($term->term_id, 'div_order', true);
            update_post_meta($id, 'agediv_order', $agediv_order);
            
            $cat = get_post_meta($id, 'cat', true);
            update_post_meta($id, 'cat_order', $cat);

            $dancers = get_post_meta($id, 'dancers', true);
            update_post_meta($id, 'dancers_count', count(explode(',', $dancers)));
        }
    }
}

function ts_update_routine_meta($entry_id, $user_id) {
	$competition = get_post_meta($entry_id, 'competition', true);
	$routines = $competition['routines'];
	$routine_ids = ! empty($routines) ? array_keys($routines) : 0;
	$args = array(
		'order' => 'ASC',
		'include' => $routine_ids,
	);
	$routine_posts = ts_get_user_posts('ts_routine', -1, $user_id, $args);

	if($routine_ids!==0 && $routine_posts) {
		foreach ($routine_posts as $rp) {
			$rpid = $rp->ID;
			$dancers_count_edited = get_post_meta($rpid, 'dancers_count_edited', true);
            update_post_meta($id, 'dancers_count', $dancers_count_edited);
		}
	}
}

function ts_get_scheduleid_by_tourid($tour_id) {
	$args = array(
		'meta_query' => array(
			array(
				'key'     => 'event_city',
				'value'   => $tour_id,
				'compare' => '=',
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

	$competition_schedule = ts_get_posts('ts_event', 1, $args);	

	return $competition_schedule[0]->ID;
}

function ts_display_entry_details($entry_id, $user_id=false, $invoiceform=false, $center=false) {

	$entry_data 					= ts_get_entry_data_from_post($entry_id, $user_id);

	$workshop 						= ts_check_value($entry_data,'workshop');
	$competition 					= ts_check_value($entry_data,'competition');
	$discount_code      			= ts_check_value($entry_data,'discount_code');

	$countMunchkin 		 			= absint(ts_check_value($workshop,'count_munchkins'));
	$countMinis 			 		= absint(ts_check_value($workshop,'count_minis'));
	$countJuniors 			 		= absint(ts_check_value($workshop,'count_juniors'));
	$countTeens 			 		= absint(ts_check_value($workshop,'count_teens'));
	$countSeniors 			 		= absint(ts_check_value($workshop,'count_seniors'));
	$countPros 				 		= absint(ts_check_value($workshop,'count_pros'));
	$countTeachers 			 		= absint(ts_check_value($workshop,'count_teachers'));
	$countObservers 		 		= absint(ts_check_value($workshop,'count_observer'));
	$countMunchkinObservers 		= absint(ts_check_value($workshop,'count_munchkinobserver'));

	$routines 						= ts_check_value($competition,'routines');

	$tour_id 						= $workshop['tour_city'];
	$date_paid 						= get_post_meta($entry_id, 'date_paid', true);
	$tour_date 						= get_post_meta($tour_id, 'date_from', true);
	$force_early 					= $date_paid && ts_get_days_before_date($tour_date, $date_paid) > 30 ? true : false; 

	$workshop_fee 					= ts_get_total_workshop_fee($entry_id, $entry_data, $force_early);
	$workshop_teacher_discount 		= ts_get_total_teacher_discount($entry_id, $entry_data);
	$workshop_scholarship_discount 	= ts_get_total_scholarship_discount($entry_id, $entry_data);
	$workshop_fee_discounted 		= ts_get_discounted_total_workshop_fee($entry_id, $entry_data, $force_early);
	$competition_fee 				= ts_get_total_competition_fee($entry_id, $entry_data);
	$grand_total        			= ts_grand_total($entry_id, $entry_data, $force_early);
	$amount_credited                = absint(ts_check_value($entry_data,'amount_credited'));

	if(! empty($discount_code)){
		$discount_value	= absint(ts_get_discount_value($discount_code));
		$grand_total = ts_discounted_grand_total($grand_total, $discount_code, $entry_id);
	}
	ob_start();
	?>
	<table style="width: 100%; max-width: 800px; padding: 50px 0; <?php echo $center ? 'margin: 0 auto;' : ''; ?>" cellspacing="0" cellpadding="0" border="0">
		<tr class="tour-city">
			<td colspan="3" align="center"><strong><?php echo get_the_title($tour_id); ?></strong></td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top">
				<table style="width: 100%;" cellspacing="0" cellpadding="5" border="0">
					<tr>
						<td colspan="2"><strong style="text-decoration:underline;">Workshop</strong></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countMunchkin; ?></span> <span>Munchkin</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countMinis; ?></span> <span>Minis</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countJuniors; ?></span> <span>Juniors</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countTeens; ?></span> <span>Teens</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countSeniors; ?></span> <span>Seniors</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countPros; ?></span> <span>Pros</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countTeachers; ?></span> <span>Teachers</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countObservers; ?></span> <span>Observers</span></td>
					</tr>
					<tr>
						<td colspan="2"><span style="text-decoration:underline;"><?php echo $countMunchkinObservers; ?></span> <span>Additional Munchkin Observers</span></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td><strong style="text-decoration:underline;">Workshop Price Before Discounts</strong></td>
						<td align="right"><strong class="amount">$<?php echo number_format($workshop_fee, 2); ?></strong></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td>Teacher Discounts</td>
						<td align="right"><strong>$<?php echo number_format($workshop_teacher_discount, 2); ?></strong></td>
					</tr>
					<tr>
						<td>Scholarships/Discounts</td>
						<td align="right"><strong>$<?php echo number_format($workshop_scholarship_discount, 2); ?></strong></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td><strong style="text-decoration:underline;">Workshop Total</strong></td>
						<td align="right"><strong class="amount">$<?php echo number_format($workshop_fee_discounted, 2); ?></strong></td>
					</tr>
				</table>
			</td>
			<td valign="top" style="width: 100px;">&nbsp;</td>
			<td valign="top">
				<table style="width: 100%;" cellspacing="0" cellpadding="5" border="0">
					<tr>
						<td colspan="2"><strong style="text-decoration:underline;">Competition</strong></td>
					</tr>
					<?php
					if(is_array($routines)) {
						foreach ($routines as $key=>$r) {
							$name = $r['name'];
							$dancers_count = get_post_meta($key, 'dancers_count', true);
							$dancers_count_edited = get_post_meta($key, 'dancers_count_edited', true);
							$dancersCount = $dancers_count_edited ? $dancers_count_edited : $dancers_count;
							//$dancersCount = count(explode(",",$r['dancers']));
							?>
							<tr>
								<td><?php echo $name; ?></td>
								<td align="right"><strong>$<?php echo number_format(ts_get_routine_fee($dancersCount),2); ?></strong></td>
							</tr>
							<?php
						}
					}
					?>
					<tr>
						<td><strong style="text-decoration:underline;">Total Number of Routines</strong></td>
						<td align="right"><strong><span class="total-routines f-right"><?php echo count($routines); ?></span></strong></td>
					</tr>
					<tr>
						<td><strong style="text-decoration:underline;">Competition Total</strong>
						<td align="right"><strong><span class="total-competition-fee f-right">$<?php echo number_format($competition_fee, 2); ?></span></strong></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
		<?php
		if(! empty($discount_code)) { ?>
			<tr>
				<td align="right" colspan="3">
					Discount Code: <strong><?php echo $discount_code; ?></strong> <span style="color:red;"> (-$<?php echo number_format($discount_value, 2);?>)</span>
				</td>
			</tr>
			<?php
		} ?>
		<tr>
			<td align="right" colspan="3">
				<strong>Grand Total: $<span id="grand-total"><?php echo number_format($grand_total, 2); ?></span></strong>
			</td>
		</tr>
		<?php
		if(! empty($amount_credited)) {
			?>
			<tr>
				<td align="right" colspan="3">
					<strong>Amount Credited: $<span id="grand-total"><?php echo number_format($amount_credited, 2); ?></span></strong>
				</td>
			</tr>
			<?php
		}
		if($invoiceform) {
		?>
		<tr>
			<td align="right" colspan="3">
				<a class="btn btn-blue btn-addinvoice" href="javascript:void(0);">Create Invoice</a>
				<?php ts_create_invoice($entry_id); ?>
			</td>
		</tr>
		<?php
		} ?>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
	</table>
	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function ts_display_user_details($user_id) {

	ob_start();

	$user_meta 		= get_userdata($user_id);
	$user_roles 	= $user_meta->roles;
	$email 			= $user_meta->user_email;

	$studio 	= get_field('studio', 'user_'. $user_id);
	$address 	= get_field('address', 'user_'. $user_id);
	$city 		= get_field('city', 'user_'. $user_id);
	$state 		= get_field('state', 'user_'. $user_id);
	$zipcode 	= get_field('zipcode', 'user_'. $user_id);
	$country 	= get_field('country', 'user_'. $user_id);
	$cell 		= get_field('cell', 'user_'. $user_id);
	?>
	<?php 
	if(in_array('studio', $user_roles)) { 
		$director 	= get_field('director', 'user_'. $user_id);
		$phone 		= get_field('phone', 'user_'. $user_id);
		$contact 	= get_field('contact', 'user_'. $user_id);
		?>
		<table cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td><strong>Studio Name:</strong></td>
				<td><?php echo $studio; ?></td>
			</tr>
			<tr>
				<td><strong>Director's Name:</strong></td>
				<td><?php echo $director; ?></td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td><?php echo $address; ?></td>
			</tr>
			<tr>
				<td><strong>City:</strong></td>
				<td><?php echo $city; ?></td>
			</tr>
			<tr>
				<td><strong>State:</strong></td>
				<td><?php echo $state; ?></td>
			</tr>
			<tr>
				<td><strong>Zip Code:</strong></td>
				<td><?php echo $zipcode; ?></td>
			</tr>
			<tr>
				<td><strong>Country:</strong></td>
				<td><?php echo $country; ?></td>
			</tr>
			<tr>
				<td><strong>Studio Phone Number:</strong></td>
				<td><?php echo $phone; ?></td>
			</tr>
			<tr>
				<td><strong>Email:</strong></td>
				<td><?php echo $email; ?></td>
			</tr>
			<tr>
				<td><strong>Cell:</strong></td>
				<td><?php echo $cell; ?></td>
			</tr>
			<tr>
				<td><strong>Studio Contact Name:</strong></td>
				<td><?php echo $contact; ?></td>
			</tr>
		</table>
		<?php
	}
	else if(in_array('individual', $user_roles)) { 
		$name 		= get_field('name', 'user_'. $user_id);
		$birth_date = get_field('birth_date', 'user_'. $user_id);
		$parent 	= get_field('parent', 'user_'. $user_id);
		?>
		<table cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td><strong>Name:</strong></td>
				<td><?php echo $name; ?></td>
			</tr>
			<tr>
				<td><strong>Date of Birth:</strong></td>
				<td><?php echo $birth_date; ?></td>
			</tr>
			<tr>
				<td><strong>Parent's Name:</strong></td>
				<td><?php echo $parent; ?></td>
			</tr>
			<tr>
				<td><strong>Studio Name:</strong></td>
				<td><?php echo $studio; ?></td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td><?php echo $address; ?></td>
			</tr>
			<tr>
				<td><strong>City:</strong></td>
				<td><?php echo $city; ?></td>
			</tr>
			<tr>
				<td><strong>State:</strong></td>
				<td><?php echo $state; ?></td>
			</tr>
			<tr>
				<td><strong>Zip Code:</strong></td>
				<td><?php echo $zipcode; ?></td>
			</tr>
			<tr>
				<td><strong>Country:</strong></td>
				<td><?php echo $country; ?></td>
			</tr>
			<tr>
				<td><strong>Email:</strong></td>
				<td><?php echo $email; ?></td>
			</tr>
			<tr>
				<td><strong>Cell:</strong></td>
				<td><?php echo $cell; ?></td>
			</tr>
		</table>
		<?php
	}
	?>
	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

