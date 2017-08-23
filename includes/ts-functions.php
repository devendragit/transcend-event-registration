<?php
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
		'post_status' => array('pending', 'publish', 'unpaid', 'paid', 'unpaidcheck', 'paidcheck'),
	);
	$entries = ts_get_posts('ts_entry', -1, $args); 

	if($entries) {
		foreach ($entries as $entry) { 
			setup_postdata($entry);
            $entry_id = $entry->ID;
            $workshop = get_post_meta($entry_id, 'workshop', true);
            $tour_city 		= $workshop['tour_city'];
            if(isset($tour_city)) {
            	$date_from 	= get_post_meta($tour_city, 'date_from', true);
				$date_to 	= get_post_meta($tour_city, 'date_to', true);
            	update_post_meta($entry_id, 'tour_date', date_format(date_create($date_from),'Y/m/d'));
				update_post_meta($entry_id, 'tour_date', date_format(date_create($date_to),'Y/m/d'));
            }
            $status = get_post_status($entry_id);
            if($status=='paid' || $status=='paidcheck'){
				$paid_amount = get_post_meta($entry_id, 'paid_amount', true);
				$grand_total = get_post_meta($entry_id, 'grand_total', true);
				if(! $paid_amount) {
	            	update_post_meta($entry_id, 'paid_amount', $grand_total);
	        	}
	        }	
        }
    }
}

function ts_login_logo_url() {

    return home_url();
}

function ts_login_logo_url_title() {
    
    return get_bloginfo('name');
}

function ts_get_current_user_role() {

	global $wp_roles;
	$current_user = wp_get_current_user();
	$user_roles = $current_user->roles;
	return array_shift($user_roles);
}

function ts_is_author($postid) {
    $current_user = wp_get_current_user();
    if (empty($current_user))
        return false;
    $user_id = $current_user->ID;

    $post = get_post($postid);
    $post_author = $post->post_author;

    if($user_id==$post_author)
    	return true;
    else
    	return false;
}

function ts_get_posts($post_type='ts_entry', $count=-1, $moreargs=array()) {

	$args = array(
		'posts_per_page' => $count,
		'post_type' => $post_type,
	);

	$args = array_merge($args, $moreargs);

	$post = get_posts($args);	

	return $post;
}

function ts_get_user_posts($post_type='ts_entry', $count=-1, $user_id=false, $moreargs=array()) {

	if(! $user_id)
		$user_id = get_current_user_id();

	$args = array(
		'posts_per_page' => $count,
		'post_type' => $post_type,
		'author' => $user_id,
	);

	$args = array_merge($args, $moreargs);

	$post = get_posts($args);	

	return $post;
}


function ts_remove_personal_options($options) {

	if(current_user_can('is_custom_user')){
		$options = preg_replace('#<h2>'. __("Personal Options") .'</h2>.+?/table>#s', '', $options, 1);
		$options = preg_replace('#<h2>'. __("About Yourself") .'</h2>.+?/table>#s', '', $options, 1);
		$options = preg_replace('#<h2>'. __("About the user") .'</h2>.+?/table>#s', '', $options, 1);
		$options = preg_replace('#<h2>Account Management</h2>#s', '', $options, 1);
		$options = preg_replace('#<h2>Name</h2>#s', '<h2>Account Management</h2>', $options, 1);
		$options = preg_replace('#<h2>Contact Info</h2>#s', '', $options, 1);
		$options = preg_replace('#<h3>'. __("Custom Gravatar") .'</h3>#s', '<h2>Profile Image</h2>', $options, 1);
		$options = preg_replace('#'. __("Use Custom Gravatar") .'#s', 'Use Custom Profile Image', $options, 1);
		$options = preg_replace('#<h3>Studio Information</h3>#s', '<h2>Studio Information</h2>', $options, 1);
	}	
	return $options;
}

function ts_profile_subject_start() {

	ob_start('ts_remove_personal_options');
}

function ts_profile_subject_end() {
	ob_end_flush();
	if(current_user_can('is_custom_user')){
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		var acf = $('#acf-form-data');
		var title = acf.next();
		var table = title.next();
		$(table).prependTo('#your-profile');
		$(title).prependTo('#your-profile');
		$(acf).prependTo('#your-profile');
	});	
	</script>
	<?php
	}
}

function ts_filter_editable_roles($roles) {
    if(isset($roles['administrator']) && ! current_user_can('administrator')){
    	unset($roles['administrator']);
    }
    return $roles;
}

function ts_map_meta_cap($caps, $cap, $user_id, $args){
	switch($cap){
	    case 'edit_user':
	    case 'remove_user':
	    case 'promote_user':
	        if(isset($args[0]) && $args[0] == $user_id)
	            break;
	        elseif(!isset($args[0]))
	            $caps[] = 'do_not_allow';
	        $other = new WP_User(absint($args[0]));
	        if($other->has_cap('administrator')){
	            if(!current_user_can('administrator')){
	                $caps[] = 'do_not_allow';
	            }
	        }
	        break;
	    case 'delete_user':
	    case 'delete_users':
	        if(!isset($args[0]))
	            break;
	        $other = new WP_User(absint($args[0]));
	        if($other->has_cap('administrator')){
	            if(!current_user_can('administrator')){
	                $caps[] = 'do_not_allow';
	            }
	        }
	        break;
	    default:
	        break;
	}
	return $caps;
}

function ts_pre_user_query($user_search) {
    $user = wp_get_current_user();
    if (! current_user_can('manage_options')) {
        global $wpdb;
        $user_search->query_where = 
            str_replace('WHERE 1=1', 
            "WHERE 1=1 AND {$wpdb->users}.ID IN (
                 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')", 
            $user_search->query_where
       );
    }
}

function ts_remove_admin_footer() {
    if (current_user_can('is_customer')) {
		add_filter('admin_footer_text', '__return_empty_string', 11); 
		add_filter('update_footer', '__return_empty_string', 11);
    }
}

function ts_remove_help_tabs() {

	if (current_user_can('is_customer')) {
	    $screen = get_current_screen();
	    $screen->remove_help_tabs();
	}    
}

function ts_remove_core_updates(){
	
	if(current_user_can('is_custom_user')) {
		global $wp_version;
		return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
	}
}

function ts_remove_admin_notices() {

	if(current_user_can('is_custom_user')) {

	    global $wp_filter;

        if (isset($wp_filter['user_admin_notices'])) {
            unset($wp_filter['user_admin_notices']);
        }
	    if (isset($wp_filter['admin_notices'])) {
	        unset($wp_filter['admin_notices']);
	    }
	    if (isset($wp_filter['all_admin_notices'])) {
	        unset($wp_filter['all_admin_notices']);
	    }
	}
}

function ts_admin_body_class($classes) {

	if(ts_get_current_user_role() == 'event_organizer'){
		$classes.= ' event-organizer custom-user';
	}else if(ts_get_current_user_role() == 'studio') {
		$classes.= ' studio custom-user customer';
	}else if(ts_get_current_user_role() == 'individual') {
		$classes.= ' individual custom-user customer';
	}

	if($_GET['page']=='ts-post-entry' || $_GET['page']=='ts-edit-entry') {
		$classes.= ' folded';
	}

    return $classes;
}

function ts_frontend_body_class($classes) {

	$classes[] = 'ts-page';

	if(is_page(ts_get_register_page_id()))
		$classes[] = 'ts-event-registration';

    return $classes;
}

function ts_count_user_login($user_login, $user) {

    if(get_user_meta($user->ID, 'ts_login_count', true)) {
        $login_count = get_user_meta($user->ID, 'ts_login_count', true);
        update_user_meta($user->ID, 'ts_login_count', ((int) $login_count + 1));
    } 
    else {
        update_user_meta($user->ID, 'ts_login_count', 1);
    }	
}

function ts_login_redirect($redirect_to, $request, $user) {

	if (isset($user->roles) && is_array($user->roles)) {
		if (in_array('event_organizer', $user->roles)) {
			return TS_ORGANIZER_DASHBOARD;
		} 
		else if (in_array('studio', $user->roles) ||  in_array('individual', $user->roles)) {
			if(absint(get_user_meta($user->ID, 'ts_login_count', true)) > 1){
				return TS_STUDIO_DASHBOARD;
			}
			else{
				return $redirect_to;
			}
		} 
		else {
			if($redirect_to == get_permalink(ts_get_register_page_id())) {
				return TS_ADMIN_DASHBOARD;
			}
			else {
				return $redirect_to;
			}
		}
	} 
	else {
		return $redirect_to;
	}
}

function ts_redirect_dashboard(){
	
	global $pagenow;

	if(is_admin() && $pagenow == 'index.php'){
		if(current_user_can('is_organizer')) {
			wp_redirect(TS_ORGANIZER_DASHBOARD);
			exit;
		}
		else if(current_user_can('is_studio') || current_user_can('is_individual')) {
			wp_redirect(TS_STUDIO_DASHBOARD);
			exit;
		}
	}
}

function ts_get_register_page_id() {

	$register_page = get_page_by_path('event-registration');
	return $register_page->ID;
}

function ts_restrict_media_to_user($wp_query) {

	$query = $wp_query->query;
	
    if ($query['post_type'] == 'attachment') {
        if (! current_user_can('manage_options')) {
            global $current_user;
            $wp_query->set('author', $current_user->ID);
            if(current_user_can('is_customer')) {
	            $mime_types = array('audio/mpeg','audio/x-realaudio','audio/wav','audio/ogg','audio/midi','audio/x-ms-wma','audio/x-ms-wax','audio/x-matroska');
	            $wp_query->set('post_mime_type', $mime_types);
        	}
        }
    }
}

function ts_modify_nodes($wp_admin_bar) {

	if(current_user_can('is_custom_user')) {
	    $wp_admin_bar->remove_node('wp-logo');
	    $wp_admin_bar->remove_node('new-content');
	    $wp_admin_bar->remove_node('gform-forms');
	    
	}
}

function ts_admin_header_nav() {

	if(current_user_can('is_custom_user')) {

		global $typenow, $pagenow;
		
		$user_id      = get_current_user_id();
		$current_user = wp_get_current_user();
		$profile_url  = get_edit_profile_url($user_id);

		if (! $user_id)
			return;
	}	
}

function ts_update_term_order($term_id, $term_name, $tax='ts_agediv') {
	if($tax=='ts_agediv'){
		if($term_name=='Munchkin') {
			$order = 1;
		}
		else if($term_name=='Mini') {
			$order = 2;
		}
		else if($term_name=='Junior') {
			$order = 3;
		}
		else if($term_name=='Teen') {
			$order = 4;
		}
		else if($term_name=='Senior') {
			$order = 5;
		}
		else if($term_name=='Pro') {
			$order = 6;
		}
		else if($term_name=='Teacher') {
			$order = 7;
		}
		update_term_meta($term_id, 'div_order', $order);
	}
	else if($tax='ts_rostertype'){
		if($term_name=='Dancer') {
			$order = 1;
		}
		else if($term_name=='Teacher') {
			$order = 2;
		}
		update_term_meta($term_id, 'type_order', $order);
	}	
}

function ts_edit_admin_menus() {

    /*global $menu;

    $menu[6][6] = 'dashicons-groups';
    $menu[7][6] = 'dashicons-calendar-alt';*/
    
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

function ts_update_roster_agedivs() {
	
	$roster = ts_get_posts('ts_studio_roster');
	$siblings = ts_get_posts('ts_sibling');

	$items = array_merge($roster, $siblings);

	foreach ($items as $r) {
		$rid = $r->ID;
		$birth_date = get_post_meta($rid, 'birth_date', true);
		ts_set_age_division($rid, $birth_date);
	}	
}

function ts_update_age_division($id, $age_division) {

	$div_curr = wp_get_object_terms($id, 'ts_agediv');
	$div_new = get_term_by('id', $age_division, 'ts_agediv');

	if($div_curr[0]->term_id !== $div_new->term_id) {

		$div_id = $div_new->term_id;
		$div_slug = $div_new->slug;
		$div_order = get_term_meta($div_id, 'div_order', true);

		wp_set_object_terms($id, $div_id, 'ts_agediv');

		update_post_meta($id, 'age_cat_order', $div_order);

		$divTeacher = get_term_by('name', 'Teacher', 'ts_agediv');

		if($div_id==$divTeacher->term_id) {
			$typeTeahcer = get_term_by('name', 'Teacher', 'ts_rostertype');
			wp_set_object_terms($id, $typeTeahcer->term_id, 'ts_rostertype');
		}
		else{
			$typeDancer = get_term_by('name', 'Dancer', 'ts_rostertype');
			wp_set_object_terms($id, $typeDancer->term_id, 'ts_rostertype');
		}
	}
}

function ts_set_age_division($id, $birthdate) {

	$age 	= absint(ts_get_the_age($birthdate));
	$type 	= wp_get_object_terms($id, 'ts_rostertype');

	if($type && $type[0]->name == 'Teacher'){
		$div = get_term_by('name', 'Teacher', 'ts_agediv');
		$test = 1;
	}
	else{
		$test = 2;
		if((0 <= $age) && ($age <= 7)) {
			$div = get_term_by('name', 'Munchkin', 'ts_agediv');
		}
		else if((8 <= $age) && ($age <= 10)) {
			$div = get_term_by('name', 'Mini', 'ts_agediv');
		}
		else if((11 <= $age) && ($age <= 12)) {
			$div = get_term_by('name', 'Junior', 'ts_agediv');
		}
		else if((13 <= $age) && ($age <= 15)) {
			$div = get_term_by('name', 'Teen', 'ts_agediv');
		}
		else if((16 <= $age) && ($age <= 19)) {
			$div = get_term_by('name', 'Senior', 'ts_agediv');
		}
		else if($age >= 20) {
			$test = 3;
			$div = get_term_by('name', 'Pro', 'ts_agediv');
		}
	}

	$div_id = $div->term_id;
	$div_order = get_term_meta($div_id, 'div_order', true);

	update_post_meta($id, 'age_cat_order', $div_order);
	$agediv = wp_set_object_terms($id, $div_id, 'ts_agediv');

	return $div;
}

function ts_get_routine_agediv_name($age_ave) {

	if((0 <= $age_ave) && ($age_ave <= 7)) {
		$age_div_name = 'Munchkin';
	}
	else if((8 <= $age_ave) && ($age_ave <= 10)) {
		$age_div_name = 'Mini';
	}
	else if((11 <= $age_ave) && ($age_ave <= 12)) {
		$age_div_name = 'Junior';
	}
	else if((13 <= $age_ave) && ($age_ave <= 15)) {
		$age_div_name = 'Teen';
	}
	else if((16 <= $age_ave) && ($age_ave <= 19)) {
		$age_div_name = 'Senior';
	}
	else if($age_ave >= 20) {
		$age_div_name = 'Pro';
	}

	return $age_div_name;
}

function ts_change_post_status($post_id, $status) {
    $post = array(
    	'ID' => $post_id, 
    	'post_status' => $status, 
   );
    remove_action('save_post', 'ts_save_custom_meta_box');
    wp_update_post($post);
    add_action('save_post', 'ts_save_custom_meta_box');
}

function ts_set_session_entry_data($entry_data, $eid, $user_id=false) {

	if(! $user_id)
		$user_id = get_current_user_id();

	$_SESSION['user_temp'][$user_id]['entry'][$eid] = $entry_data;	
}

function ts_get_session_entry_data($eid, $user_id=false) {

	if(! $user_id)
		$user_id = get_current_user_id();

	return isset($_SESSION['user_temp'][$user_id]['entry'][$eid]) && ! empty($_SESSION['user_temp'][$user_id]['entry'][$eid]) ? $_SESSION['user_temp'][$user_id]['entry'][$eid] : array();
}

function ts_load_entry_data_from_post($eid, $user_id=false) {

	if(! $user_id)
		$user_id = get_current_user_id();

	$entry_data = ts_get_entry_data_from_post($eid, $user_id);
	$_SESSION['user_temp'][$user_id]['entry'][$eid] = $entry_data;
}

function ts_get_entry_data_from_post($entry_id, $user_id=false) {

	if(! $user_id)
		$user_id = get_current_user_id();

	$entry_data = array();

	if(get_post_status($entry_id) !== false) {
		$entry_data['profile'] = get_post_meta($entry_id, 'profile', true);
		$entry_data['workshop'] = get_post_meta($entry_id, 'workshop', true);		
		$entry_data['competition'] = get_post_meta($entry_id, 'competition', true);
		$entry_data['grand_total'] = get_post_meta($entry_id, 'grand_total', true);
		$entry_data['discount_code'] = get_post_meta($entry_id, 'discount_code', true);
	}
	return $entry_data;
}

function ts_set_eid($user_id=false) {

	$eid = ts_random_password(7);

	if(! $user_id)
		$user_id = get_current_user_id();

	if(isset($_SESSION['user_temp'][$user_id]['entry'][$eid])) {
		ts_set_eid();
	}else{
		return $eid;
	}
}

function ts_get_entry_id() {
	return isset($_GET['id']) && current_user_can('edit_entry', (int)$_GET['id']) ? (int)$_GET['id'] : null;
}

function ts_get_current_eid() {
	return isset($_GET['id']) && current_user_can('edit_entry', (int)$_GET['id']) ? (int)$_GET['id'] : (isset($_GET['eid']) && $_GET['eid']!='' ? $_GET['eid'] : ts_set_eid());
}

function ts_get_current_evid() {
	return isset($_GET['evid']) && current_user_can('edit_entry', (int)$_GET['id']) ? (int)$_GET['evid'] : null;
}

function ts_get_base_url($entry_id, $eid) {

	$url = is_admin() ? admin_url('admin.php') : get_permalink();

	$urleid = '&eid='. $eid;
	$urlid = $entry_id ? '&id='. $entry_id : '';
	$urlaction = $entry_id ? '&action=edit' : '';
	$base_url = $url .'?page=ts-post-entry'. $urlaction . $urlid . $urleid;

	return $base_url;
}

function ts_remove_participant($id, $eid) {

	$entry_data 	= ts_get_session_entry_data($eid);
	$temp_data 		= $entry_data;
	$participants 	= ts_check_value($entry_data, 'workshop', 'participants');

	if(is_array($participants) && ! empty($participants)) {
		if(array_key_exists($id, $participants)) {
			unset($participantsArray[$id]);
		}
		$temp_data['workshop']['participants'] = $participantsArray;
	}
	ts_set_session_entry_data($temp_data, $eid);
}

function ts_get_observer_fee() {
	return 35;
}

function ts_get_munchkin_observer_fee() {
	return 15;
}

function ts_get_workshop_fee($id, $duration_id=1, $eid, $tour_city=false) {

	if(! $tour_city){
		$entry_data = ts_get_session_entry_data($eid);
		$tour_city 	= ts_check_value($entry_data, 'workshop', 'tour_city');
	}

	$tour_date 	= get_post_meta($tour_city, 'date_from', true);
	$age_div 	= wp_get_object_terms($id, 'ts_agediv');

	if($tour_date && ts_get_days_before_date($tour_date) > 30) {
		$fee_standard 			= get_term_meta($age_div[0]->term_id, 'fee_early', true);
		$fee_standard_oneday 	= get_term_meta($age_div[0]->term_id, 'fee_early_oneday', true);
	}
	else{
		$fee_standard 			= get_term_meta($age_div[0]->term_id, 'fee_standard', true);
		$fee_standard_oneday 	= get_term_meta($age_div[0]->term_id, 'fee_standard_oneday', true);
	}
		
	return $duration_id==2 ? $fee_standard_oneday : $fee_standard;	
}

function ts_get_discounted_workshop_fee($base_fee, $discount_id) {

	if($discount_id==1) {
		$discount = $base_fee * 0.50;
	}
	else if($discount_id==2){
		$discount = $base_fee;
	}
	else if($discount_id==3){
		$discount = $base_fee;
	}
	else if($discount_id==4){
		$discount = $base_fee * 0.15;
	}
	else if($discount_id==5){
		$discount = $base_fee * 0.50;
	}
	else if($discount_id==6){
		$discount = $base_fee * 0.20;
	}
	else if($discount_id==7){
		$discount = $base_fee * 0.10;
	}
	else {
		$discount = 0;
	}

	$discounted_fee = $base_fee-$discount;

	return $discounted_fee;
}

function ts_get_total_workshop_fee($eid, $data=false) {

	$entry_data 		= $data===false ? ts_get_session_entry_data($eid) : $data;
	$workshop 			= ts_check_value($entry_data, 'workshop');
	$participants 		= ts_check_value($workshop, 'participants');
	$observer 			= ts_check_value($workshop, 'observers');
	$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');


	$workshop_fee = 0;

	if(is_array($participants) && ! empty($participants)){
		foreach ($participants as $key => $value) {
			$duration = (int)$value['duration'];
			$base_fee = ts_get_workshop_fee($key, $duration, $eid);
			$workshop_fee = $workshop_fee+$base_fee;
		}
	}

	if(is_array($observer) && ! empty($observer)){
		foreach ($observer as $key => $value) {
			$observer_fee = ts_get_observer_fee();
			$workshop_fee = $workshop_fee+$observer_fee;
		}
	}
		
	if(is_array($munchkin_observer) && ! empty($munchkin_observer)){
		foreach ($munchkin_observer as $key => $value) {
			$munchkin_observer_fee = ts_get_munchkin_observer_fee();
			$workshop_fee = $workshop_fee+$munchkin_observer_fee;
		}
	}

	return $workshop_fee;	
}

function ts_get_discounted_total_workshop_fee($eid, $data=false) {

	$total_workshop_fee = ts_get_total_workshop_fee($eid, $data);
	$total_scholarship_discount = ts_get_total_scholarship_discount($eid, $data);
	$total_teacher_discount = ts_get_total_teacher_discount($eid, $data);

	$workshop_fee = $total_workshop_fee - $total_scholarship_discount - $total_teacher_discount;

	return $workshop_fee;	
}

function ts_get_total_scholarship_discount($eid, $data=false) {

	$entry_data 	= $data===false ? ts_get_session_entry_data($eid) : $data;
	$participants 	= ts_check_value($entry_data, 'workshop', 'participants');

	$total_discount = 0;

	if(is_array($participants) && ! empty($participants)){
		foreach ($participants as $key => $value) {
			$discount = (int)$value['discount'];
			$duration = (int)$value['duration'];
			$base_fee = ts_get_workshop_fee($key, $duration, $eid);
			$discounted = ts_get_discounted_workshop_fee($base_fee, $discount);
			$discount = $base_fee-$discounted;
			$total_discount = $total_discount+$discount;
		}
	}
		
	return $total_discount;	
}

function ts_count_teachers($eid, $data=false) {

	$countTeachers = 0;

	$entry_data 	= $data===false ? ts_get_session_entry_data($eid) : $data;
	$participants 	= ts_check_value($entry_data, 'workshop', 'participants');

	if(is_array($participants) && ! empty($participants)){
		
		$teacher = get_term_by('name', 'Teacher', 'ts_agediv');
		$teacher_id = $teacher->term_id;

		foreach ($participants as $key => $value) {
			$agediv = $value['age_division'];
			if($agediv==$teacher_id) {
				$countTeachers++;
			}			
		}
	}
	return $countTeachers;
}

function ts_count_free_teacher($count) {

	$free_count = 0;

	if((5 <= $count) && ($count <= 18)) {
		$free_count = 1;
	}
	else if((19 <= $count) && ($count <= 36)) {
		$free_count = 2;
	}
	else if((37 <= $count) && ($count <= 54)) {
		$free_count = 3;
	}
	else if((55 <= $count) && ($count <= 69)) {
		$free_count = 4;
	}
	else if($count >= 70) {
		$free_count = 4;
	}

	return $free_count;
}

function ts_get_free_teacher_ids($eid, $data=false) {

	$teacher = get_term_by('name', 'Teacher', 'ts_agediv');
	$teacher_id = $teacher->term_id;

	$entry_data 	= $data===false ? ts_get_session_entry_data($eid) : $data;
	$participants 	= ts_check_value($entry_data, 'workshop', 'participants');
	$count_dancers 	= ts_count_dancers($participants, $teacher_id);
	$free_count 	= ts_count_free_teacher($count_dancers);

	$count_teacher = 0;
	$teacher_ids = array();

	if(is_array($participants) && ! empty($participants)){
		foreach ($participants as $key => $value) {
			$agediv = (int)$value['age_division'];
			if($agediv==$teacher_id && $count_teacher < $free_count) {
				$count_teacher++;
				$teacher_ids[] = $key;
			}			
		}
	}

	return $teacher_ids;	
}

function ts_count_dancers($participants, $teacher_id) {

	$count_dancers = 0;

	if(is_array($participants) && ! empty($participants)){
		foreach ($participants as $value) {
			$agediv = (int)$value['age_division'];
			if($agediv!=$teacher_id) {
				$count_dancers++;
			}			
		}
	}

	return $count_dancers;	
}

function ts_get_total_teacher_discount($eid, $data=false) {

	$teacher = get_term_by('name', 'Teacher', 'ts_agediv');
	$teacher_id = $teacher->term_id;

	$entry_data 	= $data===false ? ts_get_session_entry_data($eid) : $data;
	$participants 	= ts_check_value($entry_data, 'workshop', 'participants');
	$count_dancers 	= ts_count_dancers($participants, $teacher_id);
	$free_count 	= ts_count_free_teacher($count_dancers);

	$total_discount = 0;
	$count_teacher = 0;

	if(is_array($participants) && ! empty($participants)){

		$teacher = get_term_by('name', 'Teacher', 'ts_agediv');
		$teacher_id = $teacher->term_id;

		foreach ($participants as $key => $value) {
			$agediv = (int)$value['age_division'];
			$discount = (int)$value['discount'];
			$duration = (int)$value['duration'];

			if($agediv == $teacher_id && $count_teacher < $free_count) {
				$count_teacher++;
				$base_fee = ts_get_workshop_fee($key, $duration, $eid);
				$discounted = ts_get_discounted_workshop_fee($base_fee, $discount);
				$total_discount = $total_discount+$discounted;
			}			
		}
	}

	return $total_discount;	
}

function ts_get_routine_fee($count) {

	$cat = ts_get_competition_categories();

	if(1 == $count) {
		$fee = $cat[1]['fee'] * $count;
	}
	else if((2 <= $count) && ($count <= 3)) {
		$fee = $cat[2]['fee'] * $count;
	}
	else if((4 <= $count) && ($count <= 9)) {
		$fee = $cat[3]['fee'] * $count;
	}
	else if((10 <= $count) && ($count <= 16)) {
		$fee = $cat[4]['fee'] * $count;
	}
	else if((17 <= $count) && ($count <= 24)) {
		$fee = $cat[5]['fee'] * $count;
	}
	else if($count >= 25) {
		$fee = $cat[6]['fee'] * $count;
	}
	return $fee;
}

function ts_get_total_competition_fee($eid, $data=false) {

	$entry_data = $data===false ? ts_get_session_entry_data($eid) : $data;
	$routines 	= ts_check_value($entry_data, 'competition', 'routines');

	$competition_fee = 0;

	if(is_array($routines) && ! empty($routines)){
		foreach ($routines as $routine) {
			$dancers = is_array($routine['dancers']) ? $routine['dancers'] : explode(",", $routine['dancers']);
			$count = count($dancers);
			$fee = ts_get_routine_fee($count);
			$competition_fee = $competition_fee+$fee;
		}
	}
	return $competition_fee;
}

function ts_grand_total($eid, $data=false) {

	$workshop_fee_discounted = ts_get_discounted_total_workshop_fee($eid, $data);
	$competition_fee = ts_get_total_competition_fee($eid, $data);
	$grand_total = $workshop_fee_discounted+$competition_fee;

	return $grand_total;
}

function ts_discounted_grand_total($total, $discount_code, $entry_id) {

	$voucher_id  = ts_post_exists($discount_code, '', '', 'ts_coupon');

	if($voucher_id) {
		$discount 	 = (get_post_meta($voucher_id, 'discount', true));
		$workshop 	 = get_post_meta($voucher_id, 'workshop', true);
		$competition = get_post_meta($voucher_id, 'competition', true);

		$data_workshop = get_post_meta($entry_id, 'workshop', true);
		$data_competition = get_post_meta($entry_id, 'competition', true);

		if( ($workshop && ! empty($data_workshop['participants'])) || ($competition && ! empty($data_competition['routines'])) ) {
			$total = $total-$discount;
		}
	}
	return $total;
}

function ts_mark_as_paid($entry_id, $user_id, $payment_method='stripe_payment') {

	if($payment_method=='stripe_payment'){
		ts_change_post_status($entry_id, 'paid');
	}
	else {
		ts_change_post_status($entry_id, 'paidcheck');
	}	
}

function ts_save_paid_amount($entry_id, $user_id, $payment_method='stripe_payment', $grand_total) {

	$entry_data = ts_get_entry_data_from_post($entry_id, $user_id);
	$competition_fee = ts_get_total_competition_fee($entry_id, $entry_data);

	update_post_meta($entry_id, 'paid_amount_competition', $competition_fee);
	update_post_meta($entry_id, 'paid_amount', $grand_total);
}

function ts_set_entry_meta($entry_id) {
	update_post_meta($entry_id, 'completed', true);
}

function ts_addto_mailchimp_list($entry_id, $user_id) {

	$workshop 	= get_post_meta($entry_id, 'workshop', true);
	$tour_city 	= $workshop['tour_city'];
	$list_id 	= get_post_meta($tour_city, 'list_id', true);

	$user_meta 	= get_userdata($user_id);
	$email 		= $user_meta->user_email;
	$user_roles = $user_meta->roles;

	if(in_array('studio', $user_roles)) {
		$name = get_field('studio', 'user_'. $user_id);
	}
	else if(in_array('individual', $user_roles)){
		$name = get_field('name', 'user_'. $user_id);
	}

	$result = ts_add_mailchimp_subscribers($list_id, $email, $name);
}

function ts_confirm_registration($entry_id) {
	update_post_meta($entry_id, 'comfirmed', true);
}

function ts_forgot_username_text($translated_text, $text, $domain) {

	if (isset($_GET['forgotusername'])){
		$msg_txt = 'Please enter your username or email address. You will receive a link to create a new password via email.';
	    if (false !== strpos($translated_text, $msg_txt)){
	        $translated_text = str_replace($msg_txt, 'Enter the email associated with your account and you will receive your username via email.', $translated_text);	
	    }

		$msg_txt2 = 'Enter your provided username or email and you will receive a link to create a new password via email.';
	    if (false !== strpos($translated_text, $msg_txt2)){
	        $translated_text = str_replace($msg_txt2, 'Enter the email associated with your account and you will receive your username via email.', $translated_text);	
	    }

		$username_txt = 'Username or Email';
	    if (false !== strpos($translated_text, $username_txt)){
	        $translated_text = str_replace($username_txt, 'Email', $translated_text);	
	    }
	}

    if (false !== strpos($translated_text, 'Howdy,')){
        $translated_text = str_replace('Howdy,', '', $translated_text);	
    }

	return $translated_text;
}

function ts_footer_scripts() {
	if (isset($_GET['forgotusername'])) {
		?>
		<script type="text/javascript">
		jQuery('.fl-post-title span').text('Forgot Username');
		jQuery('#lostpasswordform .tml-lostpassword .message').text('Enter the email associated with your account and you will receive your username via email.');
		jQuery('#lostpasswordform .tml-user-login-wrap label').text('Email');
		jQuery('#lostpasswordform .tml-submit-wrap #wp-submit').val('Get Username');
		</script>
		<?php 
	}
} 

function ts_media_library_default_tab($tab) {
    return 'type_url';
}

function ts_display_entry_details($entry_id, $user_id=false) {

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

	$workshop_fee 					= ts_get_total_workshop_fee($entry_id, $entry_data);
	$workshop_teacher_discount 		= ts_get_total_teacher_discount($entry_id, $entry_data);
	$workshop_scholarship_discount 	= ts_get_total_scholarship_discount($entry_id, $entry_data);
	$workshop_fee_discounted 		= ts_get_discounted_total_workshop_fee($entry_id, $entry_data);
	$competition_fee 				= ts_get_total_competition_fee($entry_id, $entry_data);
	$grand_total        			= ts_grand_total($entry_id, $entry_data);

	if(! empty($discount_code)){
		$grand_total = ts_discounted_grand_total($grand_total, $discount_code, $entry_id);
	}
	ob_start();
	?>
	<table style="width: 100%; max-width: 800px; margin: 0 auto; padding: 50px 0;" cellspacing="0" cellpadding="0" border="0">
		<tr class="tour-city">
			<td colspan="3" align="center"><strong><?php echo get_the_title($workshop['tour_city']); ?></strong></td>
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
						foreach ($routines as $r) { 
							$name = $r['name'];
							$dancersCount = count(explode(",",$r['dancers'])); 
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
				Discount Code: <strong><?php echo $discount_code; ?></strong> <span style="color:red;"> (-$200)</span>
			</td>
		</tr>
		<?php
		} ?>
		<tr>
			<td align="right" colspan="3">
				<strong>Grand Total: $<span id="grand-total"><?php echo number_format($grand_total, 2); ?></span></strong>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
	</table>	
	<?php 
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function ts_modify_admin_bar($wp_admin_bar) {

	if(current_user_can('is_custom_user')) {

	    $wp_admin_bar->remove_node('wp-logo');
	    $wp_admin_bar->remove_node('new-content');
	    $wp_admin_bar->remove_node('site-name');

		$site_logo = array(
		    'id' => 'site-logo',
		    'title' => '<img src="'. TS_URI .'assets/images/logo.png" />',
			'href' => get_home_url(),
            'meta' => array(
                'class' => 'site-logo',
                'target' => '_blank',
                'title' => get_option('blogname')
            )		    
		);
		$wp_admin_bar->add_node($site_logo);	
	}
}

function ts_remove_medialibrary_tab($strings) {
    if ( !current_user_can( 'administrator' ) ) {
        unset($strings["mediaLibraryTitle"]);
    return $strings;
    }
    else {
        return $strings;
    }
}

function ts_restrict_non_Admins(){

    if(!current_user_can('administrator')){
        exit;
    }
}

function ts_redirect_after_logout() {
     
    if (current_user_can('is_custom_user')) { 
     	$redirect_url = get_permalink(ts_get_register_page_id()); 
    } 
    else { 
     	$redirect_url = home_url();
    }
    wp_safe_redirect($redirect_url);
    exit;
}

function ts_get_local_timestamp($date) {
	$timestamp = mysql2date( 'U', $date );
	return $timestamp;
}

function ts_delete_attachment($id,$force_delete=false) {
	wp_delete_attachment( $id, $force_delete );
}

function ts_custom_meta_boxes() {
	add_meta_box("ts-entry-invoice-meta-box", "Create Invoice", "ts_entry_invoice_box_markup", "ts_entry", "side", "high", null);
}

function ts_save_custom_meta_box($post_id, $post, $update) {

	if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
		return $post_id;

	if ( !current_user_can('edit_post', $post_id) )
		return $post_id;

	if ( ! isset( $_POST['ts-entry-invoice-meta-box-nonce'] ) || ! wp_verify_nonce( $_POST['ts-entry-invoice-meta-box-nonce'], 'ts-entry-meta-box-security' ))
		return $post_id;

	if( 'ts_entry' === $post->post_type) {

		$ts_entry_invoice_amount = "";
		$ts_entry_invoice_note = "";
		$invoice_id = false;
		if(isset($_POST["ts-entry-invoice-amount"])) {
			$ts_entry_invoice_amount = intval( $_POST["ts-entry-invoice-amount"] );
			update_post_meta($post_id, "ts_entry_invoice_amount", $ts_entry_invoice_amount);
			$invoice_id = wp_insert_post(array (
				'post_type' => 'ts_invoice',
				'post_title' => 'Invoice #' . $post_id,
				'post_status' => 'unpaid',
				'ping_status' => 'closed',
			));
			if ($invoice_id) {
				update_post_meta($invoice_id, 'invoice_amount', $ts_entry_invoice_amount);
				do_action('ts_invoice_created', $post_id, $invoice_id);
			}
		}
		if(isset($_POST["ts-entry-invoice-note"])) {
			$ts_entry_invoice_note = sanitize_textarea_field( $_POST["ts-entry-invoice-note"] );
			update_post_meta($post_id, "ts_entry_invoice_note", $ts_entry_invoice_note);
			if ( $invoice_id ) {
				update_post_meta($invoice_id, 'invoice_note', $ts_entry_invoice_note);
			}
		}
		if(isset($_POST["ts_entry_hidden_post_status"])) {
			$ts_entry_hidden_post_status = sanitize_textarea_field( $_POST["ts_entry_hidden_post_status"] );
			update_post_meta($post_id, "ts_entry_hidden_post_status", $ts_entry_hidden_post_status);
		}
	}

}

function ts_update_meta_after_invoice_creation($entry_id, $invoice_id) {

	remove_action('save_post', 'ts_save_custom_meta_box');
	$entry_post = array( 'ID' => $entry_id, 'post_status' => 'outstanding_amount' );
	wp_update_post($entry_post);
	add_action('save_post', 'ts_save_custom_meta_box');

	update_post_meta($entry_id, 'invoice_due', true);
	update_post_meta($entry_id, 'invoice_id', $invoice_id);
	update_post_meta($invoice_id, 'entry_id', $entry_id);

}

function ts_invoice_mark_as_paid(  $entry_id, $user_id, $payment_method='stripe_payment', $iv_amount, $invoice_id ) {
    $ts_entry_previous_status = get_post_meta( $entry_id, 'ts_entry_hidden_post_status', true);
    ts_change_post_status($entry_id, $ts_entry_previous_status );

    update_post_meta($entry_id, 'invoice_due', false);
    ts_change_post_status($invoice_id, 'paid' );

}