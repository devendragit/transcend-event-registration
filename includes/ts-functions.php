<?php
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

	$posts = get_posts($args);

	return $posts;
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

	$posts = get_posts($args);

	return $posts;
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
			if(absint(get_user_meta($user->ID, 'ts_login_count', true)) > 0){
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

	return $entry_data;
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
		$entry_data['remaining_amount'] = get_post_meta($entry_id, 'remaining_amount', true);
		$entry_data['amount_credited'] = get_post_meta($entry_id, 'amount_credited', true);
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

function ts_get_workshop_fee($id, $duration_id=1, $eid, $tour_city=false, $force_early=false) {

	if(! $tour_city){
		$entry_data = ts_get_session_entry_data($eid);
		if( empty( $entry_data ) ) {
			$entry_data = ts_get_entry_data_from_post($eid);
		}
		$tour_city 	= ts_check_value($entry_data, 'workshop', 'tour_city');
	}

	$tour_date 	= get_post_meta($tour_city, 'date_from', true);
	$age_div 	= wp_get_object_terms($id, 'ts_agediv');

	if(($tour_date && ts_get_days_before_date($tour_date) > 30) || $force_early ) {
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

function ts_get_total_workshop_fee($eid, $data=false, $force_early=false) {

	$entry_data 		= $data===false ? ts_get_session_entry_data($eid) : $data;
	$workshop 			= ts_check_value($entry_data, 'workshop');
	$participants 		= ts_check_value($workshop, 'participants');
	$observer 			= ts_check_value($workshop, 'observers');
	$munchkin_observer 	= ts_check_value($workshop, 'munchkin_observers');


	$workshop_fee = 0;

	if(is_array($participants) && ! empty($participants)){
		foreach ($participants as $key => $value) {
			$duration = (int)$value['duration'];
			$base_fee = ts_get_workshop_fee($key, $duration, $eid, false, $force_early);
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

function ts_get_discounted_total_workshop_fee($eid, $data=false, $force_early=false) {

	$total_workshop_fee = ts_get_total_workshop_fee($eid, $data, $force_early);
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
		foreach ($routines as $key=>$routine) {
			$dancers_count = get_post_meta($key, 'dancers_count', true);
			$dancers_count_edited = get_post_meta($key, 'dancers_count_edited', true);
			$count = $dancers_count_edited ? $dancers_count_edited : $dancers_count;
			//$dancers = is_array($routine['dancers']) ? $routine['dancers'] : explode(",", $routine['dancers']);
			//$count = count($dancers);
			$fee = ts_get_routine_fee($count);
			$competition_fee = $competition_fee+$fee;
		}
	}
	return $competition_fee;
}

function ts_grand_total($eid, $data=false, $force_early=false) {

	$workshop_fee_discounted = ts_get_discounted_total_workshop_fee($eid, $data, $force_early);
	$competition_fee = ts_get_total_competition_fee($eid, $data);
	$grand_total = $workshop_fee_discounted+$competition_fee;

	return $grand_total;
}

function ts_discounted_grand_total($total, $discount_code, $entry_id) {

	if($discount_code=='')
		return $total;

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

	$date_paid = date_format(date_create('now'),'Y/m/d');
	update_post_meta($entry_id, 'date_paid', $date_paid);
}

function ts_save_paid_amount($entry_id, $user_id, $payment_method='stripe_payment', $grand_total) {

	$entry_data = ts_get_entry_data_from_post($entry_id, $user_id);
	$competition_fee = ts_get_total_competition_fee($entry_id, $entry_data);

	update_post_meta($entry_id, 'paid_amount_competition', $competition_fee);
	update_post_meta($entry_id, 'paid_amount', $grand_total);
	if(isset($entry_data['discount_code'])) {
		update_post_meta($entry_id, 'discount_code_applied', true);
	}
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

	if (false !== strpos($translated_text, 'Reset Password')){
		$translated_text = str_replace('Reset Password', 'Create Password', $translated_text);
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

    /*if( 'ts_event' === $post->post_type) {
        $schedule_id = $post_id;
        $schedule_type_array 	= wp_get_object_terms($schedule_id, 'ts_schedules_type');
        $competition_schedule = isset($schedule_type_array[0]->name) && 'Competition' === $schedule_type_array[0]->name ? true : false;
        if( $competition_schedule ) {
			do_action( 'competition_schedule_updated', $schedule_id );
        }
    }

    if( 'ts_score' === $post->post_type) {
        $score_id = $post_id;
        do_action( 'competition_score_updated', $score_id );
    }*/

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

function ts_is_paid($entry_id) {
	$entry_status = get_post_status($entry_id);
	if($entry_status=='paid' || $entry_status=='paidcheck')
		return true;
	else
		return false;
}

function ts_is_noworkshopentry($entry_data) {
	$participants = ts_check_value($entry_data, 'workshop', 'participants');
	if(! empty($participants) )
		return false;
	else
		return true;
}

function ts_get_form_action() {
	$action = '';
	if(current_user_can('is_studio')) {
		$action = 'studio_registration';
	}
	else if(current_user_can('is_individual')) {
		$action = 'individual_registration';
	}
	return $action;
}

function ts_disable_random_password( $password ) {
	if ( is_page('createpass') ) {
		return '';
	}
	return $password;
}

function ts_get_discount_value($voucher_code=false) {
	$discount_value = 0;
	$voucher_id  = ts_post_exists($voucher_code, '', '', 'ts_coupon');

	if($voucher_code) {
		$discount_value = get_post_meta($voucher_id, 'discount', true);
	}

	return $discount_value;
}

function ts_set_remaining_amount_meta( $entry_id, $user_id, $remaining_amount ) {
	update_post_meta($entry_id, 'remaining_due', true);
	update_post_meta($entry_id, 'remaining_amount', $remaining_amount);
}

function ts_clear_remaining_amount($entry_id, $user_id, $method, $grand_total, $remaining, $remaining_amount) {
	if( $remaining && 0 !== $remaining_amount && 'stripe_payment' === $method ) :
		/*
		$paid_amount = absint(get_post_meta($entry_id, 'paid_amount', true));
		$paid_amount = $paid_amount + absint($remaining_amount);
		update_post_meta($entry_id, 'paid_amount',$paid_amount);
		*/
		delete_post_meta($entry_id, 'remaining_due');
		delete_post_meta($entry_id, 'remaining_amount');

	endif;
}

function ts_copy_meta_data($entry_id) {
	$workshop = get_post_meta($entry_id, 'workshop',true);
	$competition = get_post_meta($entry_id, 'competition',true);

	update_post_meta($entry_id,'paid_workshop',$workshop);
	update_post_meta($entry_id,'paid_competition',$competition);
}

function ts_set_payment_error_meta($entry_id, $error) {
	$error_msg = 'Error['. $error['code'] .'] : '. $error['message'];
	update_post_meta($entry_id, 'payment_failed', true);
	update_post_meta($entry_id, 'payment_error_msg', $error_msg);
}

function ts_clear_payment_error($entry_id) {
	delete_post_meta($entry_id, 'payment_failed');
	delete_post_meta($entry_id, 'payment_error_msg');
}

function ts_return_credit_total($grand_total, $entry_id) {
	$credit_amount = 0;
	$workshop_participants = $paid_workshop_participants = array();
	$competition_routines = $paid_competition_routines = array();

	$workshop = get_post_meta($entry_id, 'workshop',true);
	$paid_workshop = get_post_meta($entry_id, 'paid_workshop',true);

	$competition = get_post_meta($entry_id, 'competition',true);
	$paid_competition = get_post_meta($entry_id, 'paid_competition',true);

	if( $competition && ! empty( $competition['routines'] ) ) {
		$competition_routines = $competition['routines'];
	}
	if( $paid_competition && ! empty( $paid_competition['routines'] ) ) {
		$paid_competition_routines = $paid_competition['routines'];
	}

	if( $workshop && ! empty( $workshop['participants'] ) ) {
		$workshop_participants = $workshop['participants'];
	}
	if( $paid_workshop && ! empty( $paid_workshop['participants'] ) ) {
		$paid_workshop_participants = $paid_workshop['participants'];
	}

	$participant_results = array_diff_key($paid_workshop_participants,$workshop_participants);
	if( $participant_results && is_array( $participant_results ) ) {
		foreach( $participant_results as $key => $participant_result ) {
			if( 'observers' !== $key && 'munchkin_observers' !== $key ) {
				$credit_amount +=  absint($participant_result['fee']);
			}
		}
	}

	$routines_results = array_diff_key($paid_competition_routines,$competition_routines);
	if( $routines_results && is_array( $routines_results ) ) {
		foreach( $routines_results as $routines_result ) {
			$credit_amount +=  absint($routines_result['fee']);
		}
	}

	return $credit_amount;
}

function ts_create_credit_post( $entry_id, $amount_credited ) {

	$credit_id = (int) get_post_meta($entry_id,'credit_id',true);
	$credit_status = ts_post_exists_by_id($credit_id);
	$credit_expiry_timestamp = ts_get_local_timestamp(date('Y/m/d', strtotime('+1 year')));

	if( $credit_status ) {

		wp_clear_scheduled_hook( 'ts_autodelete_credit', array( $credit_id ) );
		wp_schedule_single_event($credit_expiry_timestamp, 'ts_autodelete_credit', array( $credit_id ) );
		update_post_meta( $credit_id,'amount_credited',$amount_credited );
		update_post_meta( $credit_id,'amount_expiry_date',date('Y/m/d', strtotime('+1 year')));

	} else {
		$user_id 	= get_current_user_id();

		$creditArgs = array(
			'post_title' => 'Credit #' . $entry_id,
			'post_type' => 'ts_credit',
			'author' => $user_id,
			'post_status' => 'publish',
		);
		$newCredit = wp_insert_post($creditArgs, true);

		if($newCredit && !is_wp_error($newCredit)) {

			wp_schedule_single_event($credit_expiry_timestamp, 'ts_autodelete_credit', array( $newCredit ) );
			update_post_meta( $newCredit,'amount_credited',$amount_credited );
			update_post_meta( $newCredit,'amount_expiry_date',date('Y/m/d', strtotime('+1 year')));
			update_post_meta( $newCredit,'entry_id',$entry_id );
			update_post_meta( $entry_id,'credit_id',$newCredit );
		}

	}

	delete_post_meta($entry_id, 'remaining_due');
	delete_post_meta($entry_id, 'remaining_amount');

}

function ts_credit_totals( $autherid = false ) {

	$total= 0;
	if( $autherid ) {
		$creditArgs = array(
			'author' => $autherid,
		);
		$credits = ts_get_posts( 'ts_credit',-1,$creditArgs );
		if($credits) {
			foreach ($credits as $credit) {
				setup_postdata($credit);
				$credit_id = $credit->ID;
				$amount_credited = (int)get_post_meta($credit_id, 'amount_credited', true);
				$total=$total+$amount_credited;
			}
		}
	}

	return $total;
}

function ts_create_invoice($entry_id){
	$check_entry = get_post_meta($entry_id, 'completed', true);
	$status = get_post_status($entry_id);
	$invoice_due = get_post_meta($entry_id, 'invoice_due', true);
	$invoice_id = get_post_meta($entry_id, 'invoice_id', true);
	$invoice_status = false;
	if($invoice_id) {
		$invoice_status = get_post_status($invoice_id);
	}
	?>
	<div id="popup-create-invoice" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Invoice</h4>
				</div>
				<div class="modal-body <?php echo $check_entry; ?>">
					<?php
					if ( ( $status === 'paid' || $status === 'paidcheck' ) && 'paid' != $invoice_status ) {
						?>
						<form method="post" action="" id="form-create-invoice" name="form-create-invoice" >
							<div class="ts-entry-invoice">
								<input type="hidden" name="entryid" value="<?php echo $entry_id; ?>" />
								<label for="ts-entry-invoice-amount"><?php _e('Invoice Amount'); ?></label>
								<input name="ts-entry-invoice-amount" type="number" value="" placeholder="$0.00" step=".01" required>
								<label for="ts-entry-invoice-note"><?php _e('Invoice Note'); ?></label>
								<textarea name="ts-entry-invoice-note" rows="3" cols="50" required></textarea>
								<input type="hidden" name="ts_entry_hidden_post_status" value="<?php echo $status;?>">
							</div>
							<input type="submit" value="Generate Invoice" class="btn btn-blue">
						</form>
						<?php
					} elseif( $invoice_due ) {
						_e('Invoice has been created. Please check the status here! ');
						echo '<a href="'.get_edit_post_link($invoice_id).'">Click here</a>';
					} elseif( $invoice_status !== false && $invoice_status === 'paid' ) {
						_e('Invoice has been paid! ');
					} else {
						_e('Please wait until registration & payment is completed!');
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function ts_custom_admin_head() {

    global $pagenow;

    if($pagenow == 'admin.php' && ($_GET['page'] == 'ts-new-schedule' || $_GET['page'] == 'ts-view-schedule' || $_GET['page'] == 'ts-new-competition-schedule' || $_GET['page'] == 'ts-edit-competition-schedule' || $_GET['page'] == 'ts-new-workshop-schedule' || $_GET['page'] == 'ts-edit-workshop-schedule') || $_GET['page'] == 'ts-view-scores') {
        acf_form_head();
    }
}

function ts_competition_score_updated( $score_id ) {
    $tour_id = get_post_meta($score_id, 'event_city', true);
    $args = array(
        'post_status' => array('publish'),
        'meta_query' => array(
            array(
                'key' => 'event_city',
                'value' => $tour_id
            ),
        )
    );
    $awards = ts_get_posts('ts_award', 1, $args);
    if( $awards ) {
        foreach( $awards as $award ) {
            setup_postdata($award);
            $award_id = $award->ID;
            update_post_meta($award_id,'score_id',$score_id);
            update_post_meta($score_id,'award_id',$award_id);
			update_post_meta($award_id,'event_city',$tour_id);
        }
    } else {
        $award = array(
            'post_status'  => 'publish' ,
            'post_title'  => get_the_title($score_id),
            'post_type'  => 'ts_award',
        );

        $award_id = wp_insert_post($award);
        if( $award_id and !is_wp_error($award_id) ) {
            update_post_meta($award_id,'score_id',$score_id);
            update_post_meta($score_id,'award_id',$award_id);
			update_post_meta($award_id,'event_city',$tour_id);
        }
    }
}

function ts_display_awards_wrapper($score_id){
    $tour_scores = get_field('tour_scores', $score_id);
    if( isset( $tour_scores ) && is_array( $tour_scores ) ) {
        $lineup_days =wp_list_pluck($tour_scores,'lineup','day');
        foreach($lineup_days as $key=>$value) {
            $lineup = $value;
            $date = $key;
            ts_display_individual_day_awards($date, $lineup);
        }
    }
}

function ts_display_individual_day_awards($date, $lineup) {
    $get_day_name =  date('l', strtotime($date));
    $age_divisions = array_values(array_unique(wp_list_pluck($lineup, 'age_division')));
    usort($lineup, 'ts_sort_score');
    ?>
        <div class="display-individual-day-awards">
            <h4 class="text-center"><?php echo $get_day_name;?> Awards Ceremony</h4>
            <div class="outer Results">
                <div class="tabs_2">
                    <ul class="TabList clearfix">
                        <li><a class="tab_2-1" href="#tab_2-1">Category High Scores</a></li>
                        <li><a class="tab_2-2" href="#tab_2-2">Overall</a></li>
                        <li><a class="tab_2-3" href="#tab_2-3">Scholarships</a></li>
                    </ul><!--FilterList-->
                    <div id="tab_2-1">
                        <?php echo ts_display_category_high_scores($age_divisions, $lineup);?>
                    </div><!--tab_2-1-->
                    <div id="tab_2-2">
						<?php echo ts_display_overall_high_scores($lineup);?>
                    </div><!--tab_2-2-->
                    <div id="tab_2-3">

                    </div><!--tab_2-3-->
                </div><!--tabs_2-->
            </div><!--Results-->
        </div>
    <?php
}

function ts_display_category_high_scores($age_divisions, $lineup) {
	$ts_competition_categories = array('Solo', 'Duo/Trio', 'Small Group', 'Large Group', 'Line', 'Production');
    ?>
    <?php if($age_divisions): ?>
        <?php foreach($age_divisions as $age_division) :?>
        <h3><?php echo $age_division; ?></h3>
        <div class="SchedTable">
            <div class="TableCont">
                <div class="TableHeading">
                    <div class="clearfix RowHeading">
                        <div>
                            <span>Routine Name</span>
                        </div>
                        <div>
                            <span>Studio</span>
                        </div>
                        <div>
                            <span>Category</span>
                        </div>
                        <div>
                            <span>Place</span>
                        </div>
                        <div>
                            <span>Adjudicated Award</span>
                        </div>
                    </div>
                </div>
                <div class="TableBody text-center">
                    <?php
					$c = 1;
                    foreach($ts_competition_categories as $ts_competition_category):
						$scores_returns = ts_multi_array_search($lineup, array('age_division' => $age_division, 'category' => $ts_competition_category));
						if(!empty($scores_returns)) :
							$award_c = 1;
							foreach($scores_returns as $scores_return) {
								if( 6 === $award_c ) {
									break;
								} else if( ('Solos' === $ts_competition_category || ' Duo/Trio' === $ts_competition_category) && 4 === $award_c) {
									break;
								}
								$routine = get_the_title($scores_return['routine']);
								$studio = $scores_return['studio'];
								$category = $scores_return['category'];
								$place = $award_c . ' Place '.$age_division .' '.$ts_competition_category;
								$adjudicated_awards = ts_find_adjudicated_awards($scores_return['score']);
								?>
								<div class="clearfix">
									<div>
										<span><?php echo $routine;?></span>
									</div>
									<div>
										<span><?php echo $studio;?></span>
									</div>
									<div>
										<span><?php echo $category;?></span>
									</div>
									<div>
										<span><?php echo $place;?></span>
									</div>
									<div>
										<span><?php echo $adjudicated_awards;?></span>
									</div>
								</div>
								<?php
								$award_c++;
							}
						endif;
					$c++;
					endforeach;
					?>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
    <?php
}

function ts_display_overall_high_scores($lineup) {
	?>
	<h3>Overall High Scores</h3>
	<div class="SchedTable">
		<div class="TableCont">
			<div class="TableHeading">
				<div class="clearfix RowHeading">
					<div>
						<span>Routine Name</span>
					</div>
					<div>
						<span>Studio</span>
					</div>
					<div>
						<span>Age Division</span>
					</div>
					<div>
						<span>Place</span>
					</div>
					<div>
						<span>Adjudicated Award</span>
					</div>
				</div>
			</div>
			<div class="TableBody text-center">
				<?php $c = 1;
				foreach($lineup as $line):
					if( 4 === $c ) {
					break;
					}
					$routine = get_the_title($line['routine']);
					$studio = $line['studio'];
					$category = $line['category'];
					$age_division = $line['age_division'];
					$ts_competition_category = $line['category'];
					$place = $age_division .' Overall ' .$c. ' place';
					$adjudicated_awards = ts_find_adjudicated_awards($line['score']);
					?>
					<div class="clearfix">
						<div>
							<span><?php echo $routine;?></span>
						</div>
						<div>
							<span><?php echo $studio;?></span>
						</div>
						<div>
							<span><?php echo $category;?></span>
						</div>
						<div>
							<span><?php echo $place;?></span>
						</div>
						<div>
							<span><?php echo $adjudicated_awards;?></span>
						</div>
					</div>
					<?php $c++; endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

function ts_find_adjudicated_awards($score) {
	$adjudicated_awards_title = '';
	$score = (int) $score;
	$adjudicated_awards = ts_get_adjudicated_awards();
	foreach( $adjudicated_awards as $adjudicated_award ) {
		if( $score >= $adjudicated_award['min_score'] && $score <= $adjudicated_award['high_score'] ) {
			$adjudicated_awards_title = $adjudicated_award['title'];
		}
	}

	return $adjudicated_awards_title;
}

function ts_display_workshop_schedules($schedules) {

	echo '
	<div class="inner SampleSched">';
	foreach ($schedules as $schedule) {
		$schedule_id = $schedule->ID;
		$counter = 1;

		echo '<h3 class="t-center">'. $schedule->post_title .'</h3>';

        while(has_sub_field('event_schedules', $schedule_id)):
            $column_1 = get_sub_field('column_1');
            $column_2 = get_sub_field('column_2');
            $column_3 = get_sub_field('column_3');
            $column_4 = get_sub_field('column_4');
            $column_5 = get_sub_field('column_5');
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
		                    	<span><?php echo $column_1;?></span>
		                    </div>
		                    <div>
		                    	<span><?php echo $column_2;?></span>
		                    </div>
		                    <div>
		                    	<span><?php echo $column_3;?></span>
		                    </div>
		                    <div>
		                    	<span><?php echo $column_4;?></span>
		                    </div>
		                    <div>
		                    	<span><?php echo $column_5;?></span>
		                    </div>
		                </div>
		                <?php $c = 1;
		                while(has_sub_field('lineup')): ?>
		                    <div class="clearfix Row_<?php echo $c; ?> <?php echo get_sub_field('columns');?>">
		                        <div>
		                            <span><?php echo get_sub_field('time'); ?>&nbsp;</span>
		                        </div>
		                        <div>
		                            <span><?php echo get_sub_field('seniors'); ?>&nbsp;</span>
		                        </div>
		                        <div>
		                            <span><?php echo get_sub_field('teens'); ?>&nbsp;</span>
		                        </div>
		                        <div>
		                            <span><?php echo get_sub_field('juniors'); ?>&nbsp;</span>
		                        </div>
		                        <div>
		                            <span><?php echo get_sub_field('minis'); ?>&nbsp;</span>
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
	}
	echo '
	</div>';
}

function ts_display_competition_schedules($schedules, $routines_array=array()) {

	if(! empty($schedules)) {
		echo '
		<div class="inner SampleSched">';
		foreach ($schedules as $schedule) {
			$schedule_id = $schedule->ID;
			$counter = 1;
			echo '
			<h3 class="t-center">'. $schedule->post_title .'</h3>';

			while(has_sub_field('competition_event_schedules', $schedule_id)):
				?>
				<div class="CompetitionSched SchedTable">
					<div class="TableCont">
						<div id="Day_<?php echo $counter; ?>" class="TableHeading">
							<?php echo date('l', strtotime(get_sub_field('day'))); ?>
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
								$highlight = in_array(get_sub_field('routine'), $routines_array) ? 'highlighted-row' : '';
								if('Judges Break' === get_sub_field('action')) {
									?>
									<div class="clearfix t-center Row_<?php echo $c; ?> highlight-red">
										<span>Judges Break</span>
									</div>
									<?php
								}
								else if('Awards' === get_sub_field('action')) {
									?>
									<div class="clearfix t-center Row_<?php echo $c; ?> highlight-red">
										<span>Awards</span>
									</div>
									<?php
								}
								else {
									?>
									<div class="clearfix Row_<?php echo $c; ?> <?php echo $col;?> <?php echo $highlight;?>">
										<div>
											<span><?php echo get_sub_field('number'); ?>&nbsp;</span>
										</div>
										<div>
											<span><?php echo strtoupper(substr(get_sub_field('time_start'), 0, -3) .'-'. get_sub_field('time_end')); ?>&nbsp;</span>
										</div>
										<div>
											<span><?php echo get_sub_field('studio'); ?>&nbsp;</span>
										</div>
										<div>
											<span><?php echo get_the_title(get_sub_field('routine')); ?>&nbsp;</span>
										</div>
										<div>
											<span><?php echo get_sub_field('age_division'); ?>&nbsp;</span>
										</div>
										<div>
											<span><?php echo get_sub_field('category'); ?>&nbsp;</span>
										</div>
										<div>
											<span><?php echo get_sub_field('genre'); ?>&nbsp;</span>
										</div>
									</div>
									<?php
								}
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
		echo '
		</div>';
	}
}

function ts_save_routine_number($schedule_id) {

	$schedules = get_field('competition_event_schedules', $schedule_id);

	foreach ($schedules as $s) {
		$lineup = $s['lineup'];
		foreach ($lineup as $l) {
			update_post_meta($l['routine'], 'routine_number', $l['number']);
		}

	}
}

function ts_adjudicated_award($score) {

	$award = '';

    if((200 <= $score) && ($score <= 234)) {
        $award = 'Bronze';
    }
    else if((235 <= $score) && ($score <= 249)) {
        $award = 'Silver';
    }
    else if((250 <= $score) && ($score <= 264)) {
        $award = 'High Silver';
    }
    else if((265 <= $score) && ($score <= 274)) {
        $award = 'Gold';
    }
    else if((275 <= $score) && ($score <= 289)) {
        $award = 'High Gold';
    }
    else if((290 <= $score) && ($score <= 295)) {
        $award = 'Platinum';
    }
    else if((296 <= $score) && ($score <= 300)) {
        $award = 'The Transcendental Award';
    }

    return $award;
}

function ts_routine_cat_hs($routine_id, $tour_id) {
	$agediv = get_post_meta($routine_id, 'agediv', true);
	$cat = get_post_meta($routine_id, 'cat', true);
	$limit = $cat==1 || $cat==2 ? 5 : 3;
	$winners = ts_winners_posts($tour_id, $agediv, $cat, $limit);
	$routine = get_post($routine_id);
	if(in_array($routine, $winners)) {
		$key = array_search($routine, $winners);
		return ts_add_suffix($key+1) .' Place';
	}
}

function ts_routine_overall_hs($routine_id, $tour_id) {
	$agediv = get_post_meta($routine_id, 'agediv', true);
	$winners = ts_overallwinners_posts($tour_id, $agediv, 3);
	$routine = get_post($routine_id);
	if(in_array($routine, $winners)) {
		$key = array_search($routine, $winners);
		return ts_add_suffix($key+1) .' Place';
	}
}

function ts_winners_posts($tour_id, $agediv, $cat, $limit=5) {
	$args = array(
		'posts_per_page' => $limit,
		'post__in' => ts_tour_routines_ids($tour_id),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'agediv',
				'value'   => $agediv,
				'compare' => '=',
			),
			array(
				'key'     => 'cat',
				'value'   => $cat,
				'compare' => '=',
			),
			array(
				'key'     => 'total_score',
				'value'   => 0,
				'compare' => '>',
			),
		),
		'orderby' => 'meta_value_num',
		'meta_key' => 'total_score',
		'order' => 'DESC',
	);
	$routines = ts_get_posts('ts_routine',-1,$args);
	return $routines;
}

function ts_winners_array($tour_id, $agediv, $cat, $limit=5) {

	$winners = array();
	$routines = ts_winners_posts($tour_id, $agediv, $cat, $limit);
	if($routines){
		foreach ($routines as $r) {
			$id = $r->ID;
			$winners[] = array(
				'id' => $id,
				'number' => get_post_meta($id, 'routine_number', true),
				'name' => get_the_title($id),
				'studio' => ts_post_studio($id),
				'score' => get_post_meta($id, 'total_score', true),
			);
		}
	}
	return $winners;
}

function ts_overallwinners_posts($tour_id, $agediv, $limit=3) {
	$args = array(
		'posts_per_page' => $limit,
		'post__in' => ts_tour_routines_ids($tour_id),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'agediv',
				'value'   => $agediv,
				'compare' => '=',
			),
			array(
				'key'     => 'total_score',
				'value'   => 0,
				'compare' => '>',
			),
		),
		'orderby' => 'meta_value_num',
		'meta_key' => 'total_score',
		'order' => 'DESC',
	);
	$routines = ts_get_posts('ts_routine',-1,$args);
	return $routines;
}

function ts_overallwinners_array($tour_id, $agediv, $limit=3) {
	$winners = array();
	$routines = ts_overallwinners_posts($tour_id, $agediv, $limit);
	if($routines){
		foreach ($routines as $r) {
			$id = $r->ID;
			$winners[] = array(
				'id' => $id,
				'number' => get_post_meta($id, 'routine_number', true),
				'name' => get_the_title($id),
				'studio' => ts_post_studio($id),
				'score' => get_post_meta($id, 'total_score', true),
			);
		}
	}
	return $winners;
}

function ts_generate_tour_music_zip( $schedules, $tour_id ) {
	if( $tour_id && 'publish' === get_post_status($tour_id) ) {

		$musiczip_filename = get_post_meta($tour_id, 'musiczip_filename', true);
		if($musiczip_filename) {
			$musiczip_file = TS_MUSIC_ZIP_FOLDER . "/" .$musiczip_filename;
			unlink($musiczip_file);
		}

		// Prepare File

		if ( !is_dir(TS_MUSIC_ZIP_FOLDER) ) {
			mkdir(TS_MUSIC_ZIP_FOLDER, 0775, true);
		}

		$file = tempnam(TS_MUSIC_ZIP_FOLDER, "zip");
		$zip = new ZipArchive();
		if($zip->open($file, ZipArchive::OVERWRITE) === TRUE) {
			$counter = 1;
			for( $i=0 ;$i<count($schedules); $i++ ){
				if(is_array($schedules[$i]['lineup'])) {
					for( $y=0 ;$y<count($schedules[$i]['lineup']); $y++ ) {
						if( 'Judges Break' != $schedules[$i]['lineup'][$y]['action'] || 'Awards' != $schedules[$i]['lineup'][$y]['action'] ) {
							$routine_id = $schedules[$i]['lineup'][$y]['routine'];
							$music_id = (int)get_post_meta($routine_id,'music',true);
							if($music_id) {
								$name = explode('/', get_attached_file($music_id) );
								$name = $name[sizeof($name) - 1];
								$newMediaFileName = get_the_title( $routine_id );
								$name = $newMediaFileName . strstr($name, '.');
								$name = sprintf("%03d",$counter).'_'.$name;
								$zip->addFile(get_attached_file($music_id), $name);
							}
							$counter++;
						}
					}
				}
			}
			$filename_array = explode('/', $zip->filename);
			$filename = $filename_array[sizeof($filename_array) - 1];
			update_post_meta( $tour_id, 'musiczip_filename', $filename);
		}
		$zip->close();
	}
}

function ts_select_tour_city($base_url, $tour_id='', $off='') {
	?>
	<select name="tour_city" class="select-redirect validate[required]">
		<option value="">Select Tour City</option>
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
				$disabled 	= is_array($off) && in_array($ct_id, $off) ? 'disabled' : '';
				?>
				<option <?php echo $disabled; ?> <?php echo $selected; ?> value="<?php echo $ct_id; ?>" data-url="<?php echo $base_url .'&tour=' . $ct_id; ?>" ><?php echo $title; ?></option>
				<?php
			}
		}
		?>
	</select>
	<?php
}

function ts_tours_with_workshop_schedule() {
	$args = array(
		'post_status' => 'any',
		'meta_query' => array(
			array(
				'key'     => 'event_city',
				'value'   => '',
				'compare' => '!=',
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
	$schedules = ts_get_posts('ts_event', -1, $args);
	$tour_ids = array();
	if($schedules) {
		foreach ($schedules as $s) {
			$tour_id = get_post_meta($s->ID, 'event_city', true);
			if(! in_array($tour_id, $tour_ids))
				$tour_ids[] = $tour_id;
		}
	}
	return $tour_ids;
}

function ts_tours_with_competition_schedule() {
	$args = array(
		'post_status' => 'any',
		'meta_query' => array(
			array(
				'key'     => 'event_city',
				'value'   => '',
				'compare' => '!=',
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
	$schedules = ts_get_posts('ts_event', -1, $args);
	$tour_ids = array();
	if($schedules) {
		foreach ($schedules as $s) {
			$tour_id = get_post_meta($s->ID, 'event_city', true);
			if(! in_array($tour_id, $tour_ids))
				$tour_ids[] = $tour_id;
		}
	}
	return $tour_ids;
}

function ts_save_routine_total_score($score_id) {

	$tour_scores = get_field('tour_scores', $score_id);

	foreach ($tour_scores as $s) {
		$lineup = $s['lineup'];
		foreach ($lineup as $l) {
			update_post_meta($l['routine'], 'total_score', $l['score']);
		}
	}
}

function ts_display_results($tour_id) {

	wp_enqueue_style('jquery-ui-css');

if($tour_id && current_user_can('is_organizer')) {
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
<table class="ts-data-table" data-length="25" data-exporttitle="Adjudicated Awards" data-exportcol="0,1,2,3" data-dom="fBrt<'table-footer clearfix'p>">
	<thead><tr>
		<th style="width: 10%; text-align: center;">#</th>
		<th style="width: 30%; text-align: center;">Routine Name</th>
		<th style="width: 30%; text-align: center;">Studio</th>
		<th style="width: 30%; text-align: center;">Award</th>
	</tr></thead>
<tbody>
	<?php
foreach ($routines as $r) {
	$id = $r->ID;
	$number = get_post_meta($id, 'routine_number', true);
	$score = get_post_meta($id, 'total_score', true);
	$name = get_the_title($id);
	$studio = ts_post_studio($id);
	?>
<tr id="routine-<?php echo $id; ?>">
	<td style="text-align: center;"><?php echo $number; ?></div>
	<td style="text-align: center;"><?php echo $name; ?></div>
	<td style="text-align: center;"><?php echo $studio; ?></div>
	<td style="text-align: center;"><?php echo ts_adjudicated_award($score); ?></div>
	</tr>
	<?php
} ?>
	</tbody>
	</table>
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
					<?php ts_display_awards_table($hiscore_solo_mini, 'High Scores - Mini Solo'); ?>
				</div>
				<?php
			}
			$ts_hiscore_solo_junior = ts_hiscore_solo_junior($tour_id);
			if(! empty($ts_hiscore_solo_junior)){
				?>
				<div class="col-md-6">
					<h4>Solo - Junior</h4>
					<?php ts_display_awards_table($ts_hiscore_solo_junior, 'High Scores - Junior Solo'); ?>
				</div>
				<?php
			}
			$ts_hiscore_solo_teen = ts_hiscore_solo_teen($tour_id);
			if(! empty($ts_hiscore_solo_teen)){
				?>
				<div class="col-md-6">
					<h4>Solo - Teen</h4>
					<?php ts_display_awards_table($ts_hiscore_solo_teen, 'High Scores - Teen Solo'); ?>
				</div>
				<?php
			}
			$ts_hiscore_solo_senior = ts_hiscore_solo_senior($tour_id);
			if(! empty($ts_hiscore_solo_senior)){
				?>
				<div class="col-md-6">
					<h4>Solo - Senior</h4>
					<?php ts_display_awards_table($ts_hiscore_solo_senior, 'High Scores - Senior Solo'); ?>
				</div>
				<?php
			}
			$ts_hiscore_solo_pro = ts_hiscore_solo_pro($tour_id);
			if(! empty($ts_hiscore_solo_pro)){
				?>
				<div class="col-md-6">
					<h4>Solo - Pro</h4>
					<?php ts_display_awards_table($ts_hiscore_solo_pro, 'High Scores - Pro Solo'); ?>
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
					<?php ts_display_awards_table($hiscore_duotrio_mini, 'High Scores - Mini Duo/Trio'); ?>
				</div>
				<?php
			}
			$ts_hiscore_duotrio_junior = ts_hiscore_duotrio_junior($tour_id);
			if(! empty($ts_hiscore_duotrio_junior)){
				?>
				<div class="col-md-6">
					<h4>Duo/Trio - Junior</h4>
					<?php ts_display_awards_table($ts_hiscore_duotrio_junior, 'High Scores - Junior Duo/Trio'); ?>
				</div>
				<?php
			}
			$ts_hiscore_duotrio_teen = ts_hiscore_duotrio_teen($tour_id);
			if(! empty($ts_hiscore_duotrio_teen)){
				?>
				<div class="col-md-6">
					<h4>Duo/Trio - Teen</h4>
					<?php ts_display_awards_table($ts_hiscore_duotrio_teen, 'High Scores - Teen Duo/Trio'); ?>
				</div>
				<?php
			}
			$ts_hiscore_duotrio_senior = ts_hiscore_duotrio_senior($tour_id);
			if(! empty($ts_hiscore_duotrio_senior)){
				?>
				<div class="col-md-6">
					<h4>Duo/Trio - Senior</h4>
					<?php ts_display_awards_table($ts_hiscore_duotrio_senior, 'High Scores - Senior Duo/Trio'); ?>
				</div>
				<?php
			}
			$ts_hiscore_duotrio_pro = ts_hiscore_duotrio_pro($tour_id);
			if(! empty($ts_hiscore_duotrio_pro)){
				?>
				<div class="col-md-6">
					<h4>Duo/Trio - Pro</h4>
					<?php ts_display_awards_table($ts_hiscore_duotrio_pro, 'High Scores - Pro Duo/Trio'); ?>
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
					<?php ts_display_awards_table($hiscore_smallgroup_mini, 'High Scores - Mini Small Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_smallgroup_junior = ts_hiscore_smallgroup_junior($tour_id);
			if(! empty($ts_hiscore_smallgroup_junior)){
				?>
				<div class="col-md-6">
					<h4>Small Group - Junior</h4>
					<?php ts_display_awards_table($ts_hiscore_smallgroup_junior, 'High Scores - Junior Small Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_smallgroup_teen = ts_hiscore_smallgroup_teen($tour_id);
			if(! empty($ts_hiscore_smallgroup_teen)){
				?>
				<div class="col-md-6">
					<h4>Small Group - Teen</h4>
					<?php ts_display_awards_table($ts_hiscore_smallgroup_teen, 'High Scores - Teen Small Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_smallgroup_senior = ts_hiscore_smallgroup_senior($tour_id);
			if(! empty($ts_hiscore_smallgroup_senior)){
				?>
				<div class="col-md-6">
					<h4>Small Group - Senior</h4>
					<?php ts_display_awards_table($ts_hiscore_smallgroup_senior, 'High Scores - Senior Small Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_smallgroup_pro = ts_hiscore_smallgroup_pro($tour_id);
			if(! empty($ts_hiscore_smallgroup_pro)){
				?>
				<div class="col-md-6">
					<h4>Small Group - Pro</h4>
					<?php ts_display_awards_table($ts_hiscore_smallgroup_pro, 'High Scores - Pro Small Group'); ?>
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
					<?php ts_display_awards_table($hiscore_largegroup_mini, 'High Scores - Mini Large Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_largegroup_junior = ts_hiscore_largegroup_junior($tour_id);
			if(! empty($ts_hiscore_largegroup_junior)){
				?>
				<div class="col-md-6">
					<h4>Large Group - Junior</h4>
					<?php ts_display_awards_table($ts_hiscore_largegroup_junior, 'High Scores - Junior Large Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_largegroup_teen = ts_hiscore_largegroup_teen($tour_id);
			if(! empty($ts_hiscore_largegroup_teen)){
				?>
				<div class="col-md-6">
					<h4>Large Group - Teen</h4>
					<?php ts_display_awards_table($ts_hiscore_largegroup_teen, 'High Scores - Teen Large Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_largegroup_senior = ts_hiscore_largegroup_senior($tour_id);
			if(! empty($ts_hiscore_largegroup_senior)){
				?>
				<div class="col-md-6">
					<h4>Large Group - Senior</h4>
					<?php ts_display_awards_table($ts_hiscore_largegroup_senior, 'High Scores - Senior Large Group'); ?>
				</div>
				<?php
			}
			$ts_hiscore_largegroup_pro = ts_hiscore_largegroup_pro($tour_id);
			if(! empty($ts_hiscore_largegroup_pro)){
				?>
				<div class="col-md-6">
					<h4>Large Group - Pro</h4>
					<?php ts_display_awards_table($ts_hiscore_largegroup_pro, 'High Scores - Pro Large Group'); ?>
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
					<?php ts_display_awards_table($hiscore_line_mini, 'High Scores - Mini Line'); ?>
				</div>
				<?php
			}
			$ts_hiscore_line_junior = ts_hiscore_line_junior($tour_id);
			if(! empty($ts_hiscore_line_junior)){
				?>
				<div class="col-md-6">
					<h4>Line - Junior</h4>
					<?php ts_display_awards_table($ts_hiscore_line_junior, 'High Scores - Junior Line'); ?>
				</div>
				<?php
			}
			$ts_hiscore_line_teen = ts_hiscore_line_teen($tour_id);
			if(! empty($ts_hiscore_line_teen)){
				?>
				<div class="col-md-6">
					<h4>Line - Teen</h4>
					<?php ts_display_awards_table($ts_hiscore_line_teen, 'High Scores - Teen Line'); ?>
				</div>
				<?php
			}
			$ts_hiscore_line_senior = ts_hiscore_line_senior($tour_id);
			if(! empty($ts_hiscore_line_senior)){
				?>
				<div class="col-md-6">
					<h4>Line - Senior</h4>
					<?php ts_display_awards_table($ts_hiscore_line_senior, 'High Scores - Senior Line'); ?>
				</div>
				<?php
			}
			$ts_hiscore_line_pro = ts_hiscore_line_pro($tour_id);
			if(! empty($ts_hiscore_line_pro)){
				?>
				<div class="col-md-6">
					<h4>Line - Pro</h4>
					<?php ts_display_awards_table($ts_hiscore_line_pro, 'High Scores - Pro Line'); ?>
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
					<?php ts_display_awards_table($hiscore_production_mini, 'High Scores - Mini Production'); ?>
				</div>
				<?php
			}
			$ts_hiscore_production_junior = ts_hiscore_production_junior($tour_id);
			if(! empty($ts_hiscore_production_junior)){
				?>
				<div class="col-md-6">
					<h4>Production - Junior</h4>
					<?php ts_display_awards_table($ts_hiscore_production_junior, 'High Scores - Junior Production'); ?>
				</div>
				<?php
			}
			$ts_hiscore_production_teen = ts_hiscore_production_teen($tour_id);
			if(! empty($ts_hiscore_production_teen)){
				?>
				<div class="col-md-6">
					<h4>Production - Teen</h4>
					<?php ts_display_awards_table($ts_hiscore_production_teen, 'High Scores - Teen Production'); ?>
				</div>
				<?php
			}
			$ts_hiscore_production_senior = ts_hiscore_production_senior($tour_id);
			if(! empty($ts_hiscore_production_senior)){
				?>
				<div class="col-md-6">
					<h4>Production - Senior</h4>
					<?php ts_display_awards_table($ts_hiscore_production_senior, 'High Scores - Senior Production'); ?>
				</div>
				<?php
			}
			$ts_hiscore_production_pro = ts_hiscore_production_pro($tour_id);
			if(! empty($ts_hiscore_production_pro)){
				?>
				<div class="col-md-6">
					<h4>Production - Pro</h4>
					<?php ts_display_awards_table($ts_hiscore_production_pro, 'High Scores - Pro Production'); ?>
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
					<?php ts_display_awards_table($hiscore_overall_mini, 'Overall High Scores - Mini'); ?>
				</div>
				<?php
			}
			$ts_hiscore_overall_junior = ts_hiscore_overall_junior($tour_id);
			if(! empty($ts_hiscore_overall_junior)){
				?>
				<div class="col-md-6">
					<h4>Overall - Junior</h4>
					<?php ts_display_awards_table($ts_hiscore_overall_junior, 'Overall High Scores - Junior'); ?>
				</div>
				<?php
			}
			$ts_hiscore_overall_teen = ts_hiscore_overall_teen($tour_id);
			if(! empty($ts_hiscore_overall_teen)){
				?>
				<div class="col-md-6">
					<h4>Overall - Teen</h4>
					<?php ts_display_awards_table($ts_hiscore_overall_teen, 'Overall High Scores - Teen'); ?>
				</div>
				<?php
			}
			$ts_hiscore_overall_senior = ts_hiscore_overall_senior($tour_id);
			if(! empty($ts_hiscore_overall_senior)){
				?>
				<div class="col-md-6">
					<h4>Overall - Senior</h4>
					<?php ts_display_awards_table($ts_hiscore_overall_senior, 'Overall High Scores - Senior'); ?>
				</div>
				<?php
			}
			$ts_hiscore_overall_pro = ts_hiscore_overall_pro($tour_id);
			if(! empty($ts_hiscore_overall_pro)){
				?>
				<div class="col-md-6">
					<h4>Overall - Pro</h4>
					<?php ts_display_awards_table($ts_hiscore_overall_pro, 'Overall High Scores - Pro'); ?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	$special_awards = get_post_meta($tour_id, 'special_awards', true);
if($special_awards) {
	$studio_innovator 		= isset($special_awards['studio_innovator']) ? $special_awards['studio_innovator'] : '';
	$choreo12below_num 		= isset($special_awards['twelve_below']['choreography']['routine_number']) 		? $special_awards['twelve_below']['choreography']['routine_number'] : '';
	$choreo12below_id 		= isset($special_awards['twelve_below']['choreography']['routine_id']) 			? $special_awards['twelve_below']['choreography']['routine_id'] : '';
	$standnom12below_num 	= isset($special_awards['twelve_below']['standout_nominee']['routine_number']) 	? $special_awards['twelve_below']['standout_nominee']['routine_number'] : '';
	$standnom12below_id 	= isset($special_awards['twelve_below']['standout_nominee']['routine_id']) 		? $special_awards['twelve_below']['standout_nominee']['routine_id'] : '';
	$standwin12below_num 	= isset($special_awards['twelve_below']['standout_winner']['routine_number']) 	? $special_awards['twelve_below']['standout_winner']['routine_number'] : '';
	$standwin12below_id 	= isset($special_awards['twelve_below']['standout_winner']['routine_id']) 		? $special_awards['twelve_below']['standout_winner']['routine_id'] : '';
	$choreo13above_num 		= isset($special_awards['thirteen_above']['choreography']['routine_number']) 	 ? $special_awards['thirteen_above']['choreography']['routine_number'] : '';
	$choreo13above_id 		= isset($special_awards['thirteen_above']['choreography']['routine_id']) 		 ? $special_awards['thirteen_above']['choreography']['routine_id'] : '';
	$standnom13above_num 	= isset($special_awards['thirteen_above']['standout_nominee']['routine_number']) ? $special_awards['thirteen_above']['standout_nominee']['routine_number'] : '';
	$standnom13above_id 	= isset($special_awards['thirteen_above']['standout_nominee']['routine_id']) 	 ? $special_awards['thirteen_above']['standout_nominee']['routine_id'] : '';
	$standwin13above_num 	= isset($special_awards['thirteen_above']['standout_winner']['routine_number'])  ? $special_awards['thirteen_above']['standout_winner']['routine_number'] : '';
	$standwin13above_id 	= isset($special_awards['thirteen_above']['standout_winner']['routine_id']) 	 ? $special_awards['thirteen_above']['standout_winner']['routine_id'] : '';
	?>
	<h3>Specialty Awards:</h3>
	<div class="awards-container">
		<h4>(for all 12 and under)</h4>
		<table class="ts-data-table" data-length="-1" data-exporttitle="Specialty Awards (for all 12 and under)" data-exportcol="0,1,2,3" data-dom="fBrt<'table-footer clearfix'p>">
			<thead>
			<tr>
				<th style="width: 25%;">Award</th>
				<th style="width: 25%; text-align: center;">Routine #</th>
				<th style="width: 25%; text-align: center;">Routine Name</th>
				<th style="width: 25%; text-align: center;">Studio</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>Choreography Award:</div>
	<td style="text-align: center;"><?php echo $choreo12below_num; ?></div>
	<td style="text-align: center;"><?php echo get_the_title($choreo12below_id);?></div>
	<td style="text-align: center;"><?php echo ts_post_studio($choreo12below_id);?></div>
		</tr>
		<tr>
			<td>Judges Standout Nominee:</div>
			<td style="text-align: center;"><?php echo $standnom12below_num; ?></div>
			<td style="text-align: center;"><?php echo get_the_title($standnom12below_id);?></div>
			<td style="text-align: center;"><?php echo ts_post_studio($standnom12below_id);?></div>
		</tr>
		<tr>
			<td>Judges Standout Winner:</div>
			<td style="text-align: center;"><?php echo $standwin12below_num; ?></div>
			<td style="text-align: center;"><?php echo get_the_title($standwin12below_id);?></div>
			<td style="text-align: center;"><?php echo ts_post_studio($standwin12below_id);?></div>
		</tr>
		</tbody>
		</table>
		<h4>(for all 13 and above)</h4>
		<table class="ts-data-table" data-length="-1" data-exporttitle="Specialty Awards (for all 13 and above)" data-exportcol="0,1,2,3" data-dom="fBrt<'table-footer clearfix'p>">
			<thead>
			<tr>
				<th style="width: 25%;">Award</th>
				<th style="width: 25%; text-align: center;">Routine #</th>
				<th style="width: 25%; text-align: center;">Routine Name</th>
				<th style="width: 25%; text-align: center;">Studio</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>Choreography Award:</div>
				<td style="text-align: center;"><?php echo $choreo13above_num; ?></div>
				<td style="text-align: center;"><?php echo get_the_title($choreo13above_id);?></div>
				<td style="text-align: center;"><?php echo ts_post_studio($choreo13above_id);?></div>
			</tr>
			<tr>
				<td>Judges Standout Nominee:</div>
				<td style="text-align: center;"><?php echo $standnom13above_num; ?></div>
				<td style="text-align: center;"><?php echo get_the_title($standnom13above_id);?></div>
				<td style="text-align: center;"><?php echo ts_post_studio($standnom13above_id);?></div>
			</tr>
			<tr>
				<td>Judges Standout Winner:</div>
				<td style="text-align: center;"><?php echo $standwin13above_num; ?></div>
				<td style="text-align: center;"><?php echo get_the_title($standwin13above_id);?></div>
				<td style="text-align: center;"><?php echo ts_post_studio($standwin13above_id);?></div>
			</tr>
			</tbody>
		</table>
		<h4>Studio Innovator: <strong><?php echo $studio_innovator; ?></strong></h4>
		</div>
		<?php
		}
		$scholarships = get_post_meta($tour_id, 'scholarships', true);
		if($scholarships) {
			?>
			<h3>Scholarships:</h3>
			<div class="scholarships-container">
				<table class="ts-data-table" data-length="-1" data-exporttitle="Sunday Scholarships" data-exportcol="0,1,2,3,4" data-dom="fBrt<'table-footer clearfix'p>">
					<?php
					$scholarships = get_post_meta($tour_id, 'scholarships', true);
					if(! empty($scholarships)) {
						?>
						<thead>
						<tr>
							<th>Name</th>
							<th style="text-align: center;">Age Division</th>
							<th style="text-align: center;">Studio</th>
							<th style="text-align: center;">Scholarship Number</th>
							<th style="text-align: center;">Scholarship</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($scholarships as $key=>$val) {
							$id = $key;
							if(empty($val)) continue;
							?>
							<tr id="item-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
								<td><?php echo get_the_title($id); ?></td>
								<td style="text-align: center;"><?php echo ts_participant_agediv($id); ?></td>
								<td style="text-align: center;"><?php echo ts_post_studio($id); ?></td>
								<td style="text-align: center;"><?php echo $val['number']; ?></td>
								<td style="text-align: center;"><?php echo $val['title']; ?></td>
							</tr>
							<?php
						}
						?>
						</tbody>
						<?php
					}
					?>
				</table>
			</div>
			<?php
		}
		}
		}

		function ts_display_awards_table($routines, $title="") {
			?>
			<table class="ts-data-table" data-length="-1" data-exporttitle="<?php echo $title; ?>" data-exportcol="0,1,2,3" data-dom="fBrt<'table-footer clearfix'p>">
				<thead>
				<tr>
					<th style="text-align: center; width: 60px;">#</th>
					<th>Name</th>
					<th>Studio</th>
					<th style="text-align: center;">Award</th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($routines as $key=>$val) {
					$id 	= $val['id'];
					$number = $val['number'];
					$name 	= $val['name'];
					$studio = $val['studio'];
					$award 	= ts_add_suffix($key+1);
					//$score = get_post_meta($id, 'total_score', true);
					?>
					<tr id="routine-<?php echo $id; ?>">
						<td style="text-align: center;"><?php echo $number; ?></td>
						<td><?php echo $name; ?></td>
						<td><?php echo $studio; ?></td>
						<td style="text-align: center;"><?php echo $award; //echo $score; ?> Place</td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
			<?php
		}

		function ts_display_results_frontend() {

		wp_enqueue_style('jquery-ui-css');
		$status = 'draft';

		if(isset($_GET['tour']) && $_GET['tour']!='') {
			$tour_id = $_GET['tour'];
			$status = get_post_meta($tour_id, 'results_status', true);
		}
		if(is_admin()) {
			$base_url = admin_url('admin.php?page=ts-results');
		}
		else {
			$base_url = get_permalink() .'?page=results';
		}
		?>
		<p><?php ts_select_tour_city($base_url, $tour_id); ?></p>
		<?php
		if($tour_id && $status=='publish') {
		?>
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
						<?php ts_display_awards_table_frontend($hiscore_solo_mini); ?>
					</div>
					<?php
				}
				$ts_hiscore_solo_junior = ts_hiscore_solo_junior($tour_id);
				if(! empty($ts_hiscore_solo_junior)){
					?>
					<div class="col-md-6">
						<h4>Solo - Junior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_solo_junior); ?>
					</div>
					<?php
				}
				$ts_hiscore_solo_teen = ts_hiscore_solo_teen($tour_id);
				if(! empty($ts_hiscore_solo_teen)){
					?>
					<div class="col-md-6">
						<h4>Solo - Teen</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_solo_teen); ?>
					</div>
					<?php
				}
				$ts_hiscore_solo_senior = ts_hiscore_solo_senior($tour_id);
				if(! empty($ts_hiscore_solo_senior)){
					?>
					<div class="col-md-6">
						<h4>Solo - Senior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_solo_senior); ?>
					</div>
					<?php
				}
				$ts_hiscore_solo_pro = ts_hiscore_solo_pro($tour_id);
				if(! empty($ts_hiscore_solo_pro)){
					?>
					<div class="col-md-6">
						<h4>Solo - Pro</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_solo_pro); ?>
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
						<?php ts_display_awards_table_frontend($hiscore_duotrio_mini); ?>
					</div>
					<?php
				}
				$ts_hiscore_duotrio_junior = ts_hiscore_duotrio_junior($tour_id);
				if(! empty($ts_hiscore_duotrio_junior)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Junior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_duotrio_junior); ?>
					</div>
					<?php
				}
				$ts_hiscore_duotrio_teen = ts_hiscore_duotrio_teen($tour_id);
				if(! empty($ts_hiscore_duotrio_teen)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Teen</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_duotrio_teen); ?>
					</div>
					<?php
				}
				$ts_hiscore_duotrio_senior = ts_hiscore_duotrio_senior($tour_id);
				if(! empty($ts_hiscore_duotrio_senior)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Senior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_duotrio_senior); ?>
					</div>
					<?php
				}
				$ts_hiscore_duotrio_pro = ts_hiscore_duotrio_pro($tour_id);
				if(! empty($ts_hiscore_duotrio_pro)){
					?>
					<div class="col-md-6">
						<h4>Duo/Trio - Pro</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_duotrio_pro); ?>
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
						<?php ts_display_awards_table_frontend($hiscore_smallgroup_mini); ?>
					</div>
					<?php
				}
				$ts_hiscore_smallgroup_junior = ts_hiscore_smallgroup_junior($tour_id);
				if(! empty($ts_hiscore_smallgroup_junior)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Junior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_smallgroup_junior); ?>
					</div>
					<?php
				}
				$ts_hiscore_smallgroup_teen = ts_hiscore_smallgroup_teen($tour_id);
				if(! empty($ts_hiscore_smallgroup_teen)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Teen</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_smallgroup_teen); ?>
					</div>
					<?php
				}
				$ts_hiscore_smallgroup_senior = ts_hiscore_smallgroup_senior($tour_id);
				if(! empty($ts_hiscore_smallgroup_senior)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Senior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_smallgroup_senior); ?>
					</div>
					<?php
				}
				$ts_hiscore_smallgroup_pro = ts_hiscore_smallgroup_pro($tour_id);
				if(! empty($ts_hiscore_smallgroup_pro)){
					?>
					<div class="col-md-6">
						<h4>Small Group - Pro</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_smallgroup_pro); ?>
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
						<?php ts_display_awards_table_frontend($hiscore_largegroup_mini); ?>
					</div>
					<?php
				}
				$ts_hiscore_largegroup_junior = ts_hiscore_largegroup_junior($tour_id);
				if(! empty($ts_hiscore_largegroup_junior)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Junior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_largegroup_junior); ?>
					</div>
					<?php
				}
				$ts_hiscore_largegroup_teen = ts_hiscore_largegroup_teen($tour_id);
				if(! empty($ts_hiscore_largegroup_teen)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Teen</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_largegroup_teen); ?>
					</div>
					<?php
				}
				$ts_hiscore_largegroup_senior = ts_hiscore_largegroup_senior($tour_id);
				if(! empty($ts_hiscore_largegroup_senior)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Senior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_largegroup_senior); ?>
					</div>
					<?php
				}
				$ts_hiscore_largegroup_pro = ts_hiscore_largegroup_pro($tour_id);
				if(! empty($ts_hiscore_largegroup_pro)){
					?>
					<div class="col-md-6">
						<h4>Large Group - Pro</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_largegroup_pro); ?>
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
						<?php ts_display_awards_table_frontend($hiscore_line_mini); ?>
					</div>
					<?php
				}
				$ts_hiscore_line_junior = ts_hiscore_line_junior($tour_id);
				if(! empty($ts_hiscore_line_junior)){
					?>
					<div class="col-md-6">
						<h4>Line - Junior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_line_junior); ?>
					</div>
					<?php
				}
				$ts_hiscore_line_teen = ts_hiscore_line_teen($tour_id);
				if(! empty($ts_hiscore_line_teen)){
					?>
					<div class="col-md-6">
						<h4>Line - Teen</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_line_teen); ?>
					</div>
					<?php
				}
				$ts_hiscore_line_senior = ts_hiscore_line_senior($tour_id);
				if(! empty($ts_hiscore_line_senior)){
					?>
					<div class="col-md-6">
						<h4>Line - Senior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_line_senior); ?>
					</div>
					<?php
				}
				$ts_hiscore_line_pro = ts_hiscore_line_pro($tour_id);
				if(! empty($ts_hiscore_line_pro)){
					?>
					<div class="col-md-6">
						<h4>Line - Pro</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_line_pro); ?>
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
						<?php ts_display_awards_table_frontend($hiscore_production_mini); ?>
					</div>
					<?php
				}
				$ts_hiscore_production_junior = ts_hiscore_production_junior($tour_id);
				if(! empty($ts_hiscore_production_junior)){
					?>
					<div class="col-md-6">
						<h4>Production - Junior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_production_junior); ?>
					</div>
					<?php
				}
				$ts_hiscore_production_teen = ts_hiscore_production_teen($tour_id);
				if(! empty($ts_hiscore_production_teen)){
					?>
					<div class="col-md-6">
						<h4>Production - Teen</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_production_teen); ?>
					</div>
					<?php
				}
				$ts_hiscore_production_senior = ts_hiscore_production_senior($tour_id);
				if(! empty($ts_hiscore_production_senior)){
					?>
					<div class="col-md-6">
						<h4>Production - Senior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_production_senior); ?>
					</div>
					<?php
				}
				$ts_hiscore_production_pro = ts_hiscore_production_pro($tour_id);
				if(! empty($ts_hiscore_production_pro)){
					?>
					<div class="col-md-6">
						<h4>Production - Pro</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_production_pro); ?>
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
						<?php ts_display_awards_table_frontend($hiscore_overall_mini); ?>
					</div>
					<?php
				}
				$ts_hiscore_overall_junior = ts_hiscore_overall_junior($tour_id);
				if(! empty($ts_hiscore_overall_junior)){
					?>
					<div class="col-md-6">
						<h4>Overall - Junior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_overall_junior); ?>
					</div>
					<?php
				}
				$ts_hiscore_overall_teen = ts_hiscore_overall_teen($tour_id);
				if(! empty($ts_hiscore_overall_teen)){
					?>
					<div class="col-md-6">
						<h4>Overall - Teen</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_overall_teen); ?>
					</div>
					<?php
				}
				$ts_hiscore_overall_senior = ts_hiscore_overall_senior($tour_id);
				if(! empty($ts_hiscore_overall_senior)){
					?>
					<div class="col-md-6">
						<h4>Overall - Senior</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_overall_senior); ?>
					</div>
					<?php
				}
				$ts_hiscore_overall_pro = ts_hiscore_overall_pro($tour_id);
				if(! empty($ts_hiscore_overall_pro)){
					?>
					<div class="col-md-6">
						<h4>Overall - Pro</h4>
						<?php ts_display_awards_table_frontend($ts_hiscore_overall_pro); ?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
		$special_awards = get_post_meta($tour_id, 'special_awards', true);
		if($special_awards) {
		$studio_innovator 		= isset($special_awards['studio_innovator']) ? $special_awards['studio_innovator'] : '';
		$choreo12below_num 		= isset($special_awards['twelve_below']['choreography']['routine_number']) 		? $special_awards['twelve_below']['choreography']['routine_number'] : '';
		$choreo12below_id 		= isset($special_awards['twelve_below']['choreography']['routine_id']) 			? $special_awards['twelve_below']['choreography']['routine_id'] : '';
		$standnom12below_num 	= isset($special_awards['twelve_below']['standout_nominee']['routine_number']) 	? $special_awards['twelve_below']['standout_nominee']['routine_number'] : '';
		$standnom12below_id 	= isset($special_awards['twelve_below']['standout_nominee']['routine_id']) 		? $special_awards['twelve_below']['standout_nominee']['routine_id'] : '';
		$standwin12below_num 	= isset($special_awards['twelve_below']['standout_winner']['routine_number']) 	? $special_awards['twelve_below']['standout_winner']['routine_number'] : '';
		$standwin12below_id 	= isset($special_awards['twelve_below']['standout_winner']['routine_id']) 		? $special_awards['twelve_below']['standout_winner']['routine_id'] : '';
		$choreo13above_num 		= isset($special_awards['thirteen_above']['choreography']['routine_number']) 	 ? $special_awards['thirteen_above']['choreography']['routine_number'] : '';
		$choreo13above_id 		= isset($special_awards['thirteen_above']['choreography']['routine_id']) 		 ? $special_awards['thirteen_above']['choreography']['routine_id'] : '';
		$standnom13above_num 	= isset($special_awards['thirteen_above']['standout_nominee']['routine_number']) ? $special_awards['thirteen_above']['standout_nominee']['routine_number'] : '';
		$standnom13above_id 	= isset($special_awards['thirteen_above']['standout_nominee']['routine_id']) 	 ? $special_awards['thirteen_above']['standout_nominee']['routine_id'] : '';
		$standwin13above_num 	= isset($special_awards['thirteen_above']['standout_winner']['routine_number'])  ? $special_awards['thirteen_above']['standout_winner']['routine_number'] : '';
		$standwin13above_id 	= isset($special_awards['thirteen_above']['standout_winner']['routine_id']) 	 ? $special_awards['thirteen_above']['standout_winner']['routine_id'] : '';
		?>
		<h3>Specialty Awards:</h3>
		<div class="awards-container">
			<h4>(for all 12 and under)</h4>
			<table class="ts-data-table" data-length="-1" data-dom="frt<'table-footer clearfix'p>">
				<thead>
				<tr>
					<th style="width: 25%;">Award</th>
					<th style="width: 25%; text-align: center;">Routine #</th>
					<th style="width: 25%; text-align: center;">Routine Name</th>
					<th style="width: 25%; text-align: center;">Studio</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>Choreography Award:</div>
	<td style="text-align: center;"><?php echo $choreo12below_num; ?></div>
	<td style="text-align: center;"><?php echo get_the_title($choreo12below_id);?></div>
	<td style="text-align: center;"><?php echo ts_post_studio($choreo12below_id);?></div>
		</tr>
		<tr>
			<td>Judges Standout Nominee:</div>
			<td style="text-align: center;"><?php echo $standnom12below_num; ?></div>
			<td style="text-align: center;"><?php echo get_the_title($standnom12below_id);?></div>
			<td style="text-align: center;"><?php echo ts_post_studio($standnom12below_id);?></div>
		</tr>
		<tr>
			<td>Judges Standout Winner:</div>
			<td style="text-align: center;"><?php echo $standwin12below_num; ?></div>
			<td style="text-align: center;"><?php echo get_the_title($standwin12below_id);?></div>
			<td style="text-align: center;"><?php echo ts_post_studio($standwin12below_id);?></div>
		</tr>
		</tbody>
		</table>
		<h4>(for all 13 and above)</h4>
		<table class="ts-data-table" data-length="-1" data-dom="frt<'table-footer clearfix'p>">
			<thead>
			<tr>
				<th style="width: 25%;">Award</th>
				<th style="width: 25%; text-align: center;">Routine #</th>
				<th style="width: 25%; text-align: center;">Routine Name</th>
				<th style="width: 25%; text-align: center;">Studio</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>Choreography Award:</div>
				<td style="text-align: center;"><?php echo $choreo13above_num; ?></div>
				<td style="text-align: center;"><?php echo get_the_title($choreo13above_id);?></div>
				<td style="text-align: center;"><?php echo ts_post_studio($choreo13above_id);?></div>
			</tr>
			<tr>
				<td>Judges Standout Nominee:</div>
				<td style="text-align: center;"><?php echo $standnom13above_num; ?></div>
				<td style="text-align: center;"><?php echo get_the_title($standnom13above_id);?></div>
				<td style="text-align: center;"><?php echo ts_post_studio($standnom13above_id);?></div>
			</tr>
			<tr>
				<td>Judges Standout Winner:</div>
				<td style="text-align: center;"><?php echo $standwin13above_num; ?></div>
				<td style="text-align: center;"><?php echo get_the_title($standwin13above_id);?></div>
				<td style="text-align: center;"><?php echo ts_post_studio($standwin13above_id);?></div>
			</tr>
			</tbody>
		</table>
		<h4>Studio Innovator: <strong><?php echo $studio_innovator; ?></strong></h4>
		</div>
		<?php
		}
		$scholarships = get_post_meta($tour_id, 'scholarships', true);
		if($scholarships) {
			?>
			<h3>Scholarships:</h3>
			<div class="scholarships-container">
				<table class="ts-data-table" data-length="-1" data-dom="frt<'table-footer clearfix'p>">
					<?php
					$scholarships = get_post_meta($tour_id, 'scholarships', true);
					if(! empty($scholarships)) {
						?>
						<thead>
						<tr>
							<th>Name</th>
							<th style="text-align: center;">Age Division</th>
							<th style="text-align: center;">Studio</th>
							<th style="text-align: center;">Scholarship Number</th>
							<th style="text-align: center;">Scholarship</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($scholarships as $key=>$val) {
							$id = $key;
							if(empty($val)) continue;
							?>
							<tr id="item-<?php echo $id; ?>" data-id="<?php echo $id; ?>">
								<td><?php echo get_the_title($id); ?></td>
								<td style="text-align: center;"><?php echo ts_participant_agediv($id); ?></td>
								<td style="text-align: center;"><?php echo ts_post_studio($id); ?></td>
								<td style="text-align: center;"><?php echo $val['number']; ?></td>
								<td style="text-align: center;"><?php echo $val['title']; ?></td>
							</tr>
							<?php
						}
						?>
						</tbody>
						<?php
					}
					?>
				</table>
			</div>
			<?php
		}
		}
		else if($tour_id && $status=='draft') {
			echo '
		<h3 class="t-center">No results for this tour city yet.</h3>';
		}
		}

		function ts_display_awards_table_frontend($routines) {
			?>
			<table class="ts-data-table" data-length="-1" data-dom="frt<'table-footer clearfix'p>">
				<thead>
				<tr>
					<th style="text-align: center; width: 60px;">#</th>
					<th>Name</th>
					<th>Studio</th>
					<th style="text-align: center;">Award</th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($routines as $key=>$val) {
					$id 	= $val['id'];
					$number = $val['number'];
					$name 	= $val['name'];
					$studio = $val['studio'];
					$award 	= ts_add_suffix($key+1);
					?>
					<tr id="routine-<?php echo $id; ?>">
						<td style="text-align: center;"><?php echo $number; ?></td>
						<td><?php echo $name; ?></td>
						<td><?php echo $studio; ?></td>
						<td style="text-align: center;"><?php echo $award; ?> Place</td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
			<?php
		}

function ts_admin_footer_code() {
	$screen = get_current_screen();
	if($screen->parent_base!='ts-post-entry'){
	    ?>
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
}

function ts_waiver_popup() {
	?>
	<div id="popup-waiver" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="popup-waiver-form" id="popup-waiver-form" class="validate boxed" method="post" action="">
					<?php
					if(current_user_can('is_studio')) {
						?>
						<h2 style="text-align: center;">WAIVER AND RELEASE OF LIABILITY</h2>
						<p><strong>PLEASE BE ADVISED</strong>:</p>
						<p>***This form must be read and signed before the participant is permitted to take part in any Transcend event sessions (each, “an Event”). By signing this agreement, the participant and the participant"™s parent/guardian affirms having read it. ***</p>
						<ol>
							<li>In consideration of being allowed to participate in any way in any Transcend event, I, the undersigned, acknowledge, appreciate, and agree that:
								<ol>
									<li>I know that via my participation, I may risk bodily injury, as well as the risk of damage to or loss of property; and</li>
									<li>I understand that by signing this form and participating in an Event, I assume all such risks, both known and unknown; and</li>
									<li>I agree to comply with all terms and condition communicated by TRANSCEND, the Event venue, and all persons managing the event. I agree to pay full attention at all times during my participation in any Event. I agree that if I observe any unusual hazard, I will immediately bring such to the attention of the nearest staff person associated with the Event. I agree that if I am feeling ill, dizzy, or in any way uncomfortable with my ability to safely participate in any event, I will notify staff and withdraw from participation the event;</li>
									<li>I, for myself, and on behalf of my heirs, assigns, hereby release, indemnify, hold harmless, and covenant not to sue Transcend Productions, LLC or its affiliates ("Transcend"), its officers, officials, volunteers, employees, agents, and/or other participants, sponsors, advertisers, and, if applicable, the owners and lessors of premises used for the activity ("RELEASEES"), any injury and/or loss or damage to person or property, whether caused by the negligence of the releases or otherwise related to any event in which I participate, except that which is the result of gross negligence or intentional misconduct, to the fullest extent permitted by law.</li>
									<li>I understand that Transcend from time to time produces audio-visual programs, promotions, and other materials relating to its Events. I and my Guardian hereby grant Transcend and its agents, successors, assigns and licensees the perpetual right to use my name, likeness, biographical information, photographs, voice, personal characteristics, and other personal identification (collectively “Likeness”) and any digital, videotape, sound and audio-visual recordings in any way (collectively “Recordings”) in any and all manner and media, now known or hereafter devised, throughout the world, for any and all purposes including, without limitation, in productions and in connection with the advertising and promotion of productions and/or Transcend, provided that Transcend is under no obligation to exercise any of the foregoing rights.</li>
									<li>I have elected, on a voluntary basis, and, if I am under the age of 18, with the acknowledgement and permission of my parents or legal guardians (individually and collectively referred to herein as “Guardian”), to participate in dance and other athletic performance-related activities (individually and collectively, “Activities”) for which I am registered with TRANSCEND, I and my Guardian agree and acknowledge that I may only attend Events under the supervision of a chaperone who is at least 21 years of age (“Chaperone”), that such Chaperone is responsible for my supervision at all times, and that Transcend is not responsible in any way or to any extent for supervision of me or for my welfare during my attendance at Events and participation in Activities.</li>
									<li>By signing this waiver, you release Transcend and all its employees from all claims arising out of related to any injury which may be sustained by you/your child while attending any dance class, performance, or other event associated with Transcend. You also affirm you now have, and will continue to carry, proper primary medical, health, and hospitalization and accident insurance, which you consider adequate for the protection of both your child.</li>
								</ol>
							</li>
						</ol>
						<p><strong>For Studio Owners/Directors of Registered Participants:</strong></p>
						<p class="f-larger">I have read this Release of Liability and Waiver Agreement, fully understand its terms, and sign it freely and voluntarily on behalf of all my registered participants.</p>
						<p><label><input type="checkbox" class="validate[required]" name="agree1" value="1" /><span></span></label></p>
						<?php
					}
					else if(current_user_can('is_individual')){
						?>
						<h2 style="text-align:center;">WAIVER AND RELEASE OF LIABILITY</h2>
						<p><strong>PLEASE BE ADVISED</strong>:</p>
						<p>***This form must be read and signed before the participant is permitted to take part in any Transcend event sessions (each, "an Event"). By signing this agreement, the participant and the participant's parent/guardian affirms having read it. ***</p>
						<ol>
							<li>In consideration of being allowed to participate in any way in any Transcend event, I, the undersigned, acknowledge, appreciate, and agree that:
								<ol>
									<li>I know that via my participation, I may risk bodily injury, as well as the risk of damage to or loss of property; and</li>
									<li>I understand that by signing this form and participating in an Event, I assume all such risks, both known and unknown; and</li>
									<li>I agree to comply with all terms and condition communicated by TRANSCEND, the Event venue, and all persons managing the event. I agree to pay full attention at all times during my participation in any Event. I agree that if I observe any unusual hazard, I will immediately bring such to the attention of the nearest staff person associated with the Event. I agree that if I am feeling ill, dizzy, or in any way uncomfortable with my ability to safely participate in any event, I will notify staff and withdraw from participation the event;</li>
									<li>I, for myself, and on behalf of my heirs, assigns, hereby release, indemnify, hold harmless, and covenant not to sue Transcend Productions, LLC or its affiliates ("Transcend"), its officers, officials, volunteers, employees, agents, and/or other participants, sponsors, advertisers, and, if applicable, the owners and lessors of premises used for the activity ("RELEASEES"), any injury and/or loss or damage to person or property, whether caused by the negligence of the releases or otherwise related to any event in which I participate, except that which is the result of gross negligence or intentional misconduct, to the fullest extent permitted by law.</li>
									<li>I understand that Transcend from time to time produces audio-visual programs, promotions, and other materials relating to its Events. I and my Guardian hereby grant Transcend and its agents, successors, assigns and licensees the perpetual right to use my name, likeness, biographical information, photographs, voice, personal characteristics, and other personal identification (collectively "Likeness") and any digital, videotape, sound and audio-visual recordings in any way (collectively "Recordings") in any and all manner and media, now known or hereafter devised, throughout the world, for any and all purposes including, without limitation, in productions and in connection with the advertising and promotion of productions and/or Transcend, provided that Transcend is under no obligation to exercise any of the foregoing rights.</li>
									<li>I have elected, on a voluntary basis, and, if I am under the age of 18, with the acknowledgement and permission of my parents or legal guardians (individually and collectively referred to herein as "Guardian"), to participate in dance and other athletic performance-related activities (individually and collectively, "Activities") for which I am registered with TRANSCEND,  I and my Guardian agree and acknowledge that I may only attend Events under the supervision of a chaperone who is at least 21 years of age ("Chaperone"), that such Chaperone is responsible for my supervision at all times, and that Transcend is not responsible in any way or to any extent for supervision of me or for my welfare during my attendance at Events and participation in Activities.</li>
									<li>By signing this waiver, you release Transcend and all its employees from all claims arising out of related to any injury which may be sustained by you/your child while attending any dance class, performance, or other event associated with Transcend. You also affirm you now have, and will continue to carry, proper primary medical, health, and hospitalization and accident insurance, which you consider adequate for the protection of both your child.</li>
								</ol>
							</li>
						</ol>
						<p><strong>If under 18 (for Parents/Guardians): </strong></p>
						<p class="f-larger">This is to certify that I/we, as parent(s)/guardian(s) with legal responsibility for this participant, do consent and agree not only to his/her release, but also for myself/ourselves, and my/ourselves, and my/our heirs, assigns and next of kin to release and indemnify the Releases from any and all Liability incident to my/our minor child's involvement as stated above, even arising from the negligence of the releases, to the fullest extent permitted by law.</p>
						<p><label><input type="radio" class="validate[required]" name="agree[]" value="1" /><span></span></label></p>
						<p><strong>If over 18: </strong></p>
						<p class="f-larger">I have read this Release of Liability and Waiver Agreement, fully understand its terms, and sign it freely and voluntarily.</p>
						<p><label><input type="radio" class="validate[required]" name="agree[]" value="1" /><span></span></label></p>
						<?php
					}
					?>
					<input class="btn btn-green" type="submit" value="Continue">
				</form>
			</div>
		</div>
	</div>
	<?php
}
