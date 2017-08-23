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


function ts_get_days_before_date($date) {

	$date1 = new DateTime($date);
	$date2 = new DateTime();
	$diff = $date2->diff($date1);
	return $diff->days;
}

function ts_get_the_age($birthdate) {

	$from = new DateTime($birthdate);
	$to   = new DateTime('today');
	$age = $from->diff($to)->y;	

	//$birthdate = explode('/', $birthdate);
	//$age = (date('md', date('U', mktime(0, 0, 0, $birthdate[0], $birthdate[1], $birthdate[2]))) > date('md') ? ((date('Y') - $birthdate[2]) - 1) : (date('Y') - $birthdate[2]));

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