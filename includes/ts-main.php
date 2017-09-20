<?php
/* Actions */
add_action('after_switch_theme', 'flush_rewrite_rules');
add_action('init', 'ts_register_custom_taxonomies', 10);
add_action('init', 'ts_register_custom_post_types', 10);
add_action('init', 'ts_register_custom_post_status', 10);
add_action('init', 'ajax_post_init');
add_action('in_admin_header', 'ts_admin_header_nav', 999);
add_action('admin_bar_menu', 'ts_modify_nodes', 999);
add_action('admin_menu', 'ts_register_custom_menu_pages');
add_action('admin_menu', 'ts_remove_default_menus', 999);
add_action('init', 'ts_session_start', 1);
add_action('wp_logout', 'ts_session_end');
add_action('wp_login', 'ts_session_start');
add_action('wp_login', 'ts_count_user_login', 10, 2);
add_action('wp_loaded', 'ts_register_ts_scripts', 11);
add_action('admin_enqueue_scripts', 'ts_enqueue_admin_scripts');
add_action('wp_enqueue_scripts', 'ts_enqueue_frontend_scripts');
add_action('login_enqueue_scripts', 'ts_login_scripts');
add_action('admin_init','ts_redirect_dashboard');
add_action('admin_head-user-edit.php', 'ts_profile_subject_start', 1);
add_action('admin_footer-user-edit.php', 'ts_profile_subject_end', 1);
add_action('admin_head-profile.php', 'ts_profile_subject_start', 1);
add_action('admin_footer-profile.php', 'ts_profile_subject_end', 1);
add_action('pre_user_query','ts_pre_user_query');
add_action('admin_menu', 'ts_remove_admin_footer');
add_action('admin_head', 'ts_remove_help_tabs');
add_action('admin_print_scripts', 'ts_remove_admin_notices', 1); 
add_action('admin_bar_menu', 'ts_modify_admin_bar', 999);
add_action('wp_logout', 'ts_redirect_after_logout');
add_action('wp_ajax_query-attachments','ts_restrict_non_Admins',1);
add_action('wp_ajax_nopriv_query-attachments','ts_restrict_non_Admins',1);
add_action("add_meta_boxes", "ts_custom_meta_boxes");
add_action("save_post", "ts_save_custom_meta_box", 10, 3);

/* Temp */
//add_action('init', 'ts_import_studios');
//add_action('init', 'ts_import_individual');
//add_action('init', 'ts_create_terms', 11);
//add_action('init', 'ts_create_tour_posts');
//add_action('init', 'ts_update_tour_posts');
//add_action('init', 'ts_update_entries');
//add_action('init', 'ts_update_agedivs');
//add_action('init', 'ts_update_agediv_fees');
//add_action('init', 'ts_update_agediv_order');
//add_action('init', 'ts_update_roster_agedivs');
//add_action('init', 'ts_update_roster_order');

/* Custom */
add_action('registration_comfirmed', 'ts_confirm_registration', 10, 1);
add_action('registration_completed', 'ts_new_entry_admin_notification', 10, 3);
add_action('registration_completed', 'ts_new_entry_user_notification', 10, 2);
add_action('registration_completed', 'ts_set_entry_meta', 10, 1);
add_action('registration_completed', 'ts_addto_mailchimp_list', 10, 2);
add_action('registration_edited', 'ts_reg_edited_notification', 10, 2);
add_action('registration_paid', 'ts_mark_as_paid', 10, 3);
add_action('registration_paid', 'ts_save_paid_amount', 10, 4);
add_action('ts_cron_jobs', 'ts_auto_delete_music_cron', 10, 1);
add_action('ts_invoice_created', 'ts_new_invoice_user_notification', 10, 2);
add_action('ts_invoice_created', 'ts_update_meta_after_invoice_creation', 10, 2);
add_action('invoice_paid', 'ts_mark_as_paid', 10, 3);
add_action('invoice_paid', 'ts_invoice_mark_as_paid', 10, 5);

/* Remove */
remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');

/* Filters */
add_filter('admin_body_class', 'ts_admin_body_class', 999);
add_filter('login_redirect', 'ts_login_redirect', 10, 3);
add_filter('show_admin_bar', '__return_false');
add_filter('parse_query', 'ts_restrict_media_to_user');
add_filter('pre_site_transient_update_core','ts_remove_core_updates');
add_filter('pre_site_transient_update_plugins','ts_remove_core_updates');
add_filter('pre_site_transient_update_themes','ts_remove_core_updates');
add_filter('editable_roles', 'ts_filter_editable_roles');
add_filter('map_meta_cap', 'ts_map_meta_cap', 10, 4);
add_filter('login_headerurl', 'ts_login_logo_url');
add_filter('login_headertitle', 'ts_login_logo_url_title');
add_filter('media_upload_default_tab', 'ts_media_library_default_tab', 99);
add_filter('gettext', 'ts_forgot_username_text', 1, 3);
add_filter('media_view_strings','ts_remove_medialibrary_tab');

/** Front-end **/
add_action('wp_footer', 'ts_footer_scripts');

/* Filters */
add_filter('body_class', 'ts_frontend_body_class');

/* Shortcodes */
add_shortcode('ts-event-registration-form', 'ts_event_registration_shortcode');
add_shortcode('ts-pay-invoice-form', 'ts_pay_invoice_shortcode');

register_activation_hook(__FILE__, 'ts_plugin_activate');

function ts_plugin_activate() {
	ts_remove_roles();
	ts_add_new_roles();
	ts_add_role_caps();
	ts_create_terms();
	ts_create_tour_posts();
	flush_rewrite_rules();
}

function ts_remove_roles() {
	//remove_role('subscriber');
	remove_role('author');
	remove_role('contributor');
	remove_role('editor');
}

function ts_add_new_roles() {

	remove_role('event_organizer');
	remove_role('studio');
	remove_role('individual');

	add_role(
	    'event_organizer',
	    __('Event Organizer'),
	    array(
			'read' => true,
			'upload_files' => true,
	        'list_users' => true,
	        'add_users' => true,
	        'create_users' => true,
	        'edit_users' => true,
	        'promote_users' => true,
	        'delete_users' => true,
	        'is_organizer' => true,
	        'is_custom_user' => true,
	  )
	);

	add_role(
	    'studio',
	    __('Studio'),
	    array(
			'read' => true,
			'upload_files' => true,
			'add_ts_entry' => true,
			'add_ts_observer' => true,
			'add_ts_roster' => true,
	        'is_custom_user' => true,
	        'is_customer' => true,
			'is_studio' => true,
	  )
	);

	add_role(
	    'individual',
	    __('Individual'),
	    array(
			'read' => true,
			'upload_files' => true,
			'add_ts_entry' => true,
			'add_ts_observer' => true,
			'add_ts_indiv_dancer' => true,
	        'is_custom_user' => true,
	        'is_customer' => true,
			'is_individual' => true,
	  )
	);
}

function ts_add_role_caps() {

	$roles = array('event_organizer', 'studio', 'individual');

	foreach($roles as $r) { 

	    $role = get_role($r);

		$capability_types = array(
			array('tour','tours'),
			array('event','events'),
			array('entry','entries'),
			array('studio_roster','studio_rosters'),
			array('indiv_sibling','indiv_siblings'),
			array('routine','routines'),
			array('coupon','coupons'),
            array('invoice','invoices'),
		);

		foreach ($capability_types  as $type) {

			$s = $type[0];
			$p = $type[1];

			$role->add_cap('read_'. $s);

		    if($r == 'studio') {
		    	if($s=='entry' || $s=='studio_roster' || $s=='routine') {
					$role->add_cap('read_private_'. $p);
					$role->add_cap('edit_'. $s);
					$role->add_cap('edit_'. $p);
					$role->add_cap('edit_published_'. $p);
					$role->add_cap('publish_'. $p);
					$role->add_cap('delete_private_'. $p);
					$role->add_cap('delete_published_'. $p);
					$role->add_cap('delete_'. $p);
				}		
			}	

		    if($r == 'individual') {
		    	if($s=='entry' || $s=='indiv_sibling' || $s=='routine') {
					$role->add_cap('read_private_'. $p);
					$role->add_cap('edit_'. $s);
					$role->add_cap('edit_'. $p);
					$role->add_cap('edit_published_'. $p);
					$role->add_cap('publish_'. $p);
					$role->add_cap('delete_private_'. $p);
					$role->add_cap('delete_published_'. $p);
					$role->add_cap('delete_'. $p);
				}		
			}	

		    if($r == 'event_organizer') {
				$role->add_cap('read_private_'. $p);
				$role->add_cap('edit_'. $s);
				$role->add_cap('edit_'. $p);
				$role->add_cap('edit_published_'. $p);
				$role->add_cap('publish_'. $p);
				$role->add_cap('delete_private_'. $p);
				$role->add_cap('delete_published_'. $p);
				$role->add_cap('delete_'. $p);
				$role->add_cap('edit_others_'. $p);
				$role->add_cap('delete_others_'. $p);
			}	
		}
    }     
}

function ts_register_ts_scripts() {

	if(current_user_can('is_custom_user')) {

		/*CSS*/
		wp_register_style('jquery-ui-css', TS_URI .'assets/css/jquery-ui.css');
		wp_register_style('jquery-dataTables-style', TS_URI .'assets/js/jquery.dataTables/css/jquery.dataTables.min.css');
		wp_register_style('jquery-validationEngine-style', TS_URI .'assets/js/jquery.validationEngine/css/validationEngine.jquery.css');
		wp_register_style('grid12', TS_URI .'assets/css/grid12.css');
		wp_register_style('bootstrap', TS_URI .'assets/css/bootstrap.min.css');
		wp_register_style('font-awesome', TS_URI .'assets/css/font-awesome.min.css');
		wp_register_style('ts-admin-style', TS_URI .'assets/css/ts-admin-style.css');
		wp_register_style('ts-custom-style', TS_URI .'assets/css/ts-custom-style.css');
		wp_register_style('ts-frontend-style', TS_URI .'assets/css/ts-frontend-style.css');
		wp_register_style('ts-fonts', TS_URI .'assets/fonts/fonts.css');

		/*JS*/
		wp_register_script('jquery-dataTables', TS_URI .'assets/js/jquery.dataTables/js/jquery.dataTables.min.js', array('jquery', 'jquery-ui-core'), '', true);
		wp_register_script('jquery-validationEngine-languages', TS_URI .'assets/js/jquery.validationEngine/languages/jquery.validationEngine-en.js', array('jquery'), '', true);
		wp_register_script('jquery-validationEngine', TS_URI .'assets/js/jquery.validationEngine/jquery.validationEngine.js', array('jquery'), '', true);
		wp_register_script('jquery-maskedinput', TS_URI .'assets/js/jquery.maskedinput.js', array('jquery'), '', true);
		wp_register_script('bootstrap', TS_URI .'assets/js/bootstrap.min.js', array('jquery'), '', true);
		wp_register_script('ts-custom-script', TS_URI .'assets/js/ts-custom-script.js', array('jquery'), '', false);
	}
}

function ts_enqueue_admin_scripts() {

	if(current_user_can('is_custom_user')) {

		global $pagenow;

		if($pagenow != 'users.php') {
			wp_enqueue_style('jquery-ui-css');
			wp_enqueue_style('jquery-dataTables-style');
			wp_enqueue_style('jquery-validationEngine-style');
			wp_enqueue_style('grid12');
			wp_enqueue_style('bootstrap');
			wp_enqueue_style('font-awesome');
			wp_enqueue_style('ts-custom-style');
			
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('jquery-dataTables');
			wp_enqueue_script('jquery-validationEngine-languages');
			wp_enqueue_script('jquery-validationEngine');
			wp_enqueue_script('jquery-maskedinput');
			wp_enqueue_script('bootstrap');
			wp_enqueue_script('ts-custom-script');
		}
		wp_enqueue_style('ts-admin-style');
		wp_enqueue_style('ts-fonts');
	}
}

function ts_enqueue_frontend_scripts() {

	if(current_user_can('is_custom_user') && is_page('register')) {

		wp_enqueue_style('jquery-ui-css');
		wp_enqueue_style('jquery-dataTables-style');
		wp_enqueue_style('jquery-validationEngine-style');
		wp_enqueue_style('grid12');
		wp_enqueue_style('bootstrap');
		wp_enqueue_style('font-awesome');
		wp_enqueue_style('ts-custom-style');
		wp_enqueue_style('ts-fonts');

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-validationEngine-languages');
		wp_enqueue_script('jquery-validationEngine');
		wp_enqueue_script('jquery-maskedinput');
		wp_enqueue_script('bootstrap');
		wp_enqueue_script('ts-custom-script');
	}
}

function ts_login_scripts() {
	wp_register_style('ts-frontend-style', TS_URI .'assets/css/ts-frontend-style.css');
	wp_enqueue_style('ts-frontend-style');
}

function ts_remove_default_menus() {

	if(current_user_can('is_custom_user')) {
		remove_menu_page('separator1');
		remove_menu_page('separator2');
		remove_menu_page('separator-last');
		remove_menu_page('index.php');
		remove_menu_page('upload.php');
		remove_menu_page('tools.php');
		remove_menu_page('wpcf7');
		remove_menu_page('gf_edit_forms');
	}	

	if(current_user_can('is_organizer')) {
		remove_menu_page('ts-view-entry');
	}

	if(current_user_can('is_customer')) {
		remove_menu_page('ts-edit-entry');
		remove_menu_page('ts-post-entry');
	}

	$post_types = array('ts_tour', 'ts_event', 'ts_entry', 'ts_studio_roster', 'ts_sibling', 'ts_routine', 'ts_coupon');

	/*foreach ($post_types  as $p) {
		if(current_user_can('is_custom_user')) { 
			remove_menu_page('edit.php?post_type='. $p);
			remove_submenu_page('edit.php?post_type='. $p, 'post-new.php?post_type='. $p);	
			remove_submenu_page('edit.php?post_type='. $p, 'edit.php?post_type='. $p);
		}	
	}*/
}

function ts_register_custom_menu_pages() {

	if(current_user_can('is_customer')) {
		add_menu_page('My Dashboard', 'My Dashboard', 'add_ts_entry', 'ts-my-entries', 'ts_my_entries_page', 'dashicons-dashboard', 6);
		add_menu_page('Add Registration', 'Add Registration', 'add_ts_entry', 'ts-post-entry', 'ts_post_entry_page', '', 101);
		add_menu_page('Edit Registration', 'Edit Registration', 'add_ts_entry', 'ts-edit-entry', 'ts_post_entry_page', '', 102);
        add_menu_page('Pay Invoice', 'Pay Invoice', 'is_custom_user', 'ts-entry-pay-invoice', 'ts_post_pay_invoice_page', '', 104);
	}
	else if (current_user_can('is_organizer')) {
		add_menu_page('Registrations', 'Registrations', 'is_organizer', 'ts-entries', 'ts_entries_page', 'dashicons-groups', 6);
			add_submenu_page( 'ts-entries', 'Workshop Participants', 'Workshop Participants', 'is_organizer', 'ts-workshop-entries', 'ts_workshopentries_page');
			add_submenu_page( 'ts-entries', 'Competition Routines', 'Competition Routines', 'is_organizer', 'ts-competition-entries', 'ts_competitionentries_page');
		add_menu_page('View Entry', 'View Entry', 'is_organizer', 'ts-view-entry', 'ts_view_entry_page', '', 103);
		add_menu_page('Vouchers', 'Vouchers', 'is_organizer', 'ts-vouchers', 'ts_vouchers_page', 'dashicons-tickets', 104);
		add_menu_page('Tours', 'Tours', 'is_organizer', 'ts-tours', 'ts_tours_page', 'dashicons-admin-site', 105);
	}
}

function ajax_post_init() {

    wp_register_script('ajax-post-script', TS_URI .'assets/js/ajax-post-script.js', array('jquery', 'jquery-ui-dialog')); 
	wp_enqueue_script('ajax-post-script');
    wp_localize_script('ajax-post-script', 'ajax_post_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'delete_item' => 'Are you sure you want to delete this item? This can not be undone!',
		'tokens' => array(
			'default' => wp_create_nonce('ts-default'), 
			'save_item' => wp_create_nonce('ts-save-item'), 
			'delete_item' => wp_create_nonce('ts-delete-item'),
		)
  	));

    add_action('wp_ajax_new_registration', 'ajax_new_registration');
    add_action('wp_ajax_studio_registration', 'ajax_studio_registration');
    add_action('wp_ajax_individual_registration', 'ajax_individual_registration');
    add_action('wp_ajax_edit_entry', 'ajax_edit_entry');
    add_action('wp_ajax_adjust_fee', 'ajax_adjust_fee');
    add_action('wp_ajax_set_tour_city', 'ajax_set_tour_city');
    add_action('wp_ajax_add_participants', 'ajax_add_participants');
    add_action('wp_ajax_save_roster', 'ajax_save_roster');
    add_action('wp_ajax_add_observer', 'ajax_add_observer');
    add_action('wp_ajax_add_munchkin_observer', 'ajax_add_munchkin_observer');
    add_action('wp_ajax_add_routine_dancers', 'ajax_add_routine_dancers');
    add_action('wp_ajax_apply_coupon', 'ajax_apply_coupon');
    add_action('wp_ajax_remove_coupon', 'ajax_remove_coupon');
    add_action('wp_ajax_remove_participant', 'ajax_remove_participant');
    add_action('wp_ajax_remove_observer', 'ajax_remove_observer');
    add_action('wp_ajax_remove_munchkin_observer', 'ajax_remove_munchkin_observer');
    add_action('wp_ajax_delete_routine', 'ajax_delete_routine');
    add_action('wp_ajax_delete_item', 'ajax_delete_item');
    add_action('wp_ajax_delete_all', 'ajax_delete_all');
    add_action('wp_ajax_save_voucher', 'ajax_save_voucher');
    add_action('wp_ajax_pay_invoice', 'ajax_pay_invoice');
    add_action('wp_ajax_save_tour', 'ajax_save_tour');
}

/* Commented Out. Reason: I believe we are not using this function yet.
 if (! wp_next_scheduled('ts_hourly_sched')) {
	wp_schedule_event(time(), 'hourly', 'ts_hourly_sched');
}
*/
