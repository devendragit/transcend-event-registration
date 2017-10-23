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
add_action('admin_init', 'ts_custom_admin_head');

/* Temp */
//add_action('init', 'ts_import_studios');
//add_action('init', 'ts_import_individual');
add_action('init', 'ts_create_terms', 11);
//add_action('init', 'ts_create_tour_posts');
//add_action('init', 'ts_update_tour_posts');
add_action('init', 'ts_update_entries');
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
add_action('registration_paid', 'ts_clear_remaining_amount', 10, 6);
add_action('registration_paid', 'ts_copy_meta_data', 10, 1);
add_action('registration_edited', 'ts_set_remaining_amount_meta', 10, 3);
add_action('registration_recompleted', 'ts_set_entry_meta', 10, 1);
add_action('registration_amount_credited', 'ts_create_credit_post', 10, 2);
add_action('ts_autodelete_credit','ts_autodelete_credit_cron_job', 10 ,1 );
add_action('registration_amount_credited', 'ts_set_entry_meta', 10, 1);
add_action('invoice_paid', 'ts_set_entry_meta', 10, 1);
add_action('registration_manually_mark_as_paid', 'ts_registration_manually_mark_as_paid', 10, 1);
add_action('competition_schedule_updated', 'ts_competition_schedule_updated', 10, 1);
add_action('competition_score_updated', 'ts_competition_score_updated', 10, 1);
add_action('acf/save_post', 'ts_calculate_overall_score', 20);

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
add_filter('random_password', 'ts_disable_random_password', 10, 2);
add_filter('acf/pre_save_post', 'ts_pre_save_schedule');

/** Front-end **/
add_action('wp_footer', 'ts_footer_scripts');

/* Filters */
add_filter('body_class', 'ts_frontend_body_class');

/* Shortcodes */
add_shortcode('ts-event-registration-form', 'ts_event_registration_shortcode');
add_shortcode('ts-pay-invoice-form', 'ts_pay_invoice_shortcode');
add_shortcode('ts-workshop-schedules', 'ts_workshop_schedules_shortcode');
add_shortcode('ts-competition-schedules', 'ts_competition_schedules_shortcode');
add_shortcode('ts-results', 'ts_results_shortcode');

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
            array('credit','credits'),
            array('award','awards'),
            array('score','scores'),
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
        wp_register_style('buttons-dataTables', TS_URI .'assets/js/jquery.dataTables/css/buttons.dataTables.min.css');
        wp_register_style('jquery-validationEngine-style', TS_URI .'assets/js/jquery.validationEngine/css/validationEngine.jquery.css');
        wp_register_style('grid12', TS_URI .'assets/css/grid12.css');
        wp_register_style('bootstrap', TS_URI .'assets/css/bootstrap.min.css');
        wp_register_style('font-awesome', TS_URI .'assets/css/font-awesome.min.css');
        wp_register_style('ts-admin-style', TS_URI .'assets/css/ts-admin-style.css');
        wp_register_style('ts-custom-style', TS_URI .'assets/css/ts-custom-style.css');
        wp_register_style('ts-frontend-style', TS_URI .'assets/css/ts-frontend-style.css');
        wp_register_style('ts-shortcode-style', TS_URI .'assets/css/ts-shortcode-style.css');
        wp_register_style('ts-fonts', TS_URI .'assets/fonts/fonts.css');

        /*JS*/
        if( !wp_script_is('jquery-ui') ) {
           wp_register_script( 'jquery-ui' , TS_URI .'assets/js/jquery-ui.js' );
        }
        wp_register_script('jquery-dataTables', TS_URI .'assets/js/jquery.dataTables/js/jquery.dataTables.min.js', array('jquery', 'jquery-ui-core'), '', true);
        wp_register_script('dataTables-buttons', TS_URI .'assets/js/jquery.dataTables/js/dataTables.buttons.min.js', array('jquery'), '', true);
        wp_register_script('buttons-html5', TS_URI .'assets/js/jquery.dataTables/js/buttons.html5.min.js', array('jquery'), '', true);
        wp_register_script('buttons-print', TS_URI .'assets/js/jquery.dataTables/js/buttons.print.min.js', array('jquery'), '', true);
        wp_register_script('buttons-flash', TS_URI .'assets/js/jquery.dataTables/js/buttons.flash.min.js', array('jquery'), '', true);
        wp_register_script('vfs-fonts', TS_URI .'assets/js/jquery.dataTables/js/vfs_fonts.js', array('jquery'), '', true);
        wp_register_script('jszip', TS_URI .'assets/js/jquery.dataTables/js/jszip.min.js', array('jquery'), '', true);
        wp_register_script('pdfmake', TS_URI .'assets/js/jquery.dataTables/js/pdfmake.min.js', array('jquery'), '', true);
        wp_register_script('jquery-validationEngine-languages', TS_URI .'assets/js/jquery.validationEngine/languages/jquery.validationEngine-en.js', array('jquery'), '', true);
        wp_register_script('jquery-validationEngine', TS_URI .'assets/js/jquery.validationEngine/jquery.validationEngine.js', array('jquery'), '', true);
        wp_register_script('jquery-maskedinput', TS_URI .'assets/js/jquery.maskedinput.js', array('jquery'), '', true);
        wp_register_script('jquery-moment', TS_URI .'assets/js/moment.min.js', array('jquery'), '', true);
        wp_register_script('bootstrap', TS_URI .'assets/js/bootstrap.min.js', array('jquery'), '', true);
        wp_register_script('ts-custom-script', TS_URI .'assets/js/ts-custom-script.js', array('jquery'), '', false);
        wp_register_script('ts-shortcode-script', TS_URI .'assets/js/ts-shortcode-script.js', array('jquery'), '', false);
    }
}

function ts_enqueue_admin_scripts() {

    if(current_user_can('is_custom_user')) {

        global $pagenow;

        if($pagenow != 'users.php') {
            wp_enqueue_style('jquery-ui-css');
            wp_enqueue_style('jquery-dataTables-style');
            wp_enqueue_style('buttons-dataTables');
            wp_enqueue_style('jquery-validationEngine-style');
            wp_enqueue_style('grid12');
            wp_enqueue_style('bootstrap');
            wp_enqueue_style('font-awesome');
            wp_enqueue_style('ts-custom-style');

            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-validationEngine-languages');
            wp_enqueue_script('jquery-validationEngine');
            wp_enqueue_script('jquery-maskedinput');
            wp_enqueue_script('jquery-dataTables');
            wp_enqueue_script('dataTables-buttons');
            wp_enqueue_script('buttons-html5');
            wp_enqueue_script('buttons-print');
            wp_enqueue_script('jszip');
            wp_enqueue_script('pdfmake');
            wp_enqueue_script('vfs-fonts');
            wp_enqueue_script('bootstrap');
            wp_enqueue_script('ts-custom-script');
            wp_enqueue_script('jquery-moment');
        }
        wp_enqueue_style('ts-admin-style');
        wp_enqueue_style('ts-fonts');
        wp_enqueue_style('ts-shortcode-style');

        wp_enqueue_script('ts-shortcode-script');
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
        remove_menu_page('ts-new-schedule');
        remove_menu_page('ts-view-schedule');
        remove_menu_page('ts-new-competition-schedule');
        remove_menu_page('ts-view-competition-schedule');
        remove_menu_page('ts-view-scores');
        remove_menu_page('ts-view-awards');
    }

    if(current_user_can('is_customer')) {
        remove_menu_page('ts-edit-entry');
        remove_menu_page('ts-post-entry');
    }

    $post_types = array('ts_tour', 'ts_event', 'ts_entry', 'ts_studio_roster', 'ts_sibling', 'ts_routine', 'ts_coupon', 'ts_invoice', 'ts_credit', 'ts_score', 'ts_award');

    foreach ($post_types  as $p) {
        if(current_user_can('is_custom_user')) {
            remove_menu_page('edit.php?post_type='. $p);
            remove_submenu_page('edit.php?post_type='. $p, 'post-new.php?post_type='. $p);
            remove_submenu_page('edit.php?post_type='. $p, 'edit.php?post_type='. $p);
        }
    }
}

function ts_register_custom_menu_pages() {

    if(current_user_can('is_customer')) {
        add_menu_page('My Dashboard', 'My Dashboard', 'add_ts_entry', 'ts-my-entries', 'ts_my_entries_page', 'dashicons-dashboard', 6);
        add_menu_page('My Schedules', 'My Schedules', 'is_custom_user', 'ts-schedules', 'ts_mysched_preview', 'dashicons-calendar-alt', 7);
            add_submenu_page( 'ts-schedules', 'Workshop Schedules', 'Workshop Schedules', 'is_custom_user', 'ts-workshop-schedules', 'ts_workshopsched_preview');
            add_submenu_page( 'ts-schedules', 'Competition Schedules', 'Competition Schedules', 'is_custom_user', 'ts-competition-schedules', 'ts_competitionsched_preview');
        add_menu_page('Results', 'Results', 'is_custom_user', 'ts-results', 'ts_results_preview', 'dashicons-analytics', 7);
        add_menu_page('Pay Invoice', 'Pay Invoice', 'is_custom_user', 'ts-entry-pay-invoice', 'ts_post_pay_invoice_page', '', 104);
        add_menu_page('Credits', 'My Credits', 'is_custom_user', 'ts-credits', 'ts_credits_page', 'dashicons-cart', 105);

        add_menu_page('Add Registration', 'Add Registration', 'add_ts_entry', 'ts-post-entry', 'ts_post_entry_page', '', 101);
        add_menu_page('Edit Registration', 'Edit Registration', 'add_ts_entry', 'ts-edit-entry', 'ts_post_entry_page', '', 102);
    }
    else if (current_user_can('is_organizer')) {
        add_menu_page('Registrations', 'Registrations', 'is_organizer', 'ts-entries', 'ts_entries_page', 'dashicons-groups', 6);
            add_submenu_page( 'ts-entries', 'Workshop Participants', 'Workshop Participants', 'is_organizer', 'ts-workshop-entries', 'ts_workshopentries_page');
            add_submenu_page( 'ts-entries', 'Competition Routines', 'Competition Routines', 'is_organizer', 'ts-competition-entries', 'ts_competitionentries_page');
        add_menu_page('Tour Dates', 'Tour Dates', 'is_organizer', 'ts-tours', 'ts_tours_page', 'dashicons-admin-site', 7);
        add_menu_page('Schedules', 'Schedules', 'is_organizer', 'ts-schedules', 'ts_schedules_page', 'dashicons-calendar-alt', 8);
            add_submenu_page( 'ts-schedules', 'Workshop Schedule', 'Workshop Schedule', 'is_organizer', 'ts-workshop-schedules', 'ts_workshopschedules_page');
            add_submenu_page( 'ts-schedules', 'Competition Schedule', 'Competition Schedule', 'is_organizer', 'ts-competition-schedules', 'ts_competitionschedules_page');
        add_menu_page('Special Awards', 'Special Awards', 'is_organizer', 'ts-special-awards', 'ts_special_awards_page', 'dashicons-awards', 9);
        add_menu_page('Critiques', 'Critiques', 'is_custom_user', 'ts-critiques', 'ts_critiques_page', 'dashicons-video-alt3', 11);
        add_menu_page('Results', 'Results', 'is_custom_user', 'ts-results', 'ts_results_page', 'dashicons-analytics', 12);
        add_menu_page('Vouchers', 'Vouchers', 'is_organizer', 'ts-vouchers', 'ts_vouchers_page', 'dashicons-tickets', 104);
        add_menu_page('Invoices', 'Invoices', 'is_organizer', 'ts-invoices', 'ts_invoices_page', 'dashicons-feedback', 106);

        add_menu_page('View Entry', 'View Entry', 'is_organizer', 'ts-view-entry', 'ts_view_entry_page', '', 103);
        add_menu_page('New Workshop Schedule', 'New Workshop Schedule', 'is_organizer', 'ts-new-schedule', 'ts_view_schedule_page', '', 108);
        add_menu_page('View Workshop Schedule', 'View Workshop Schedule', 'is_organizer', 'ts-view-schedule', 'ts_view_schedule_page', '', 109);
        add_menu_page('New Competition Schedule', 'New Competition Schedule', 'is_organizer', 'ts-new-competition-schedule', 'ts_view_competition_schedule_page', '', 110);
        add_menu_page('View Competition Schedule', 'View Competition Schedule', 'is_organizer', 'ts-view-competition-schedule', 'ts_view_competition_schedule_page', '', 111);

        add_menu_page('Scores', 'Scores', 'is_organizer', 'ts-scores', 'ts_score_page', 'dashicons-index-card', 112);
        add_menu_page('View Scores', 'View Scores', 'is_organizer', 'ts-view-scores', 'ts_view_scores_page', '', 113);

        add_menu_page('Awards', 'Awards', 'is_organizer', 'ts-awards', 'ts_award_page', 'dashicons-awards', 114);
        add_menu_page('View Awards', 'View Awards', 'is_organizer', 'ts-view-awards', 'ts_view_awards_page', '', 115);
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
    add_action('wp_ajax_close_tour', 'ajax_close_tour');
    add_action('wp_ajax_sched_status', 'ajax_sched_status');
    add_action('wp_ajax_create_invoice', 'ajax_create_invoice');
    add_action('wp_ajax_download_all_music', 'ajax_download_all_music');
    add_action('wp_ajax_save_music_info', 'ajax_save_music_info');
    add_action('wp_ajax_save_mark_as_paid', 'ajax_save_mark_as_paid');
    add_action('wp_ajax_save_special_awards', 'ajax_save_special_awards');
    add_action('wp_ajax_load_participant_info', 'ajax_load_participant_info');
    add_action('wp_ajax_publish_results', 'ajax_publish_results');
    add_action('wp_ajax_add_critique', 'ajax_add_critique');
    add_action('wp_ajax_remove_critique', 'ajax_remove_critique');
}

/* Commented Out. Reason: I believe we are not using this function yet.
 if (! wp_next_scheduled('ts_hourly_sched')) {
	wp_schedule_event(time(), 'hourly', 'ts_hourly_sched');
}
*/
