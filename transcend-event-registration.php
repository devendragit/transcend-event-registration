<?php
/*
Plugin Name: Transcend Event Registration
Plugin URI: 
Description: Event registratin for Transcend
Author: Carlos Reyes
Version: 1.0
Author URI: http://sitesbycarlos.com/
*/

if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}

/* Paths */
define('TS_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('TS_INCLUDES', TS_DIR . trailingslashit('includes'));
define('TS_LIBRARIES', TS_DIR . trailingslashit('libraries'));
define('TS_TEMPLATES', TS_DIR . trailingslashit('templates'));

/* URI */
define('TS_URI', trailingslashit(plugin_dir_url(__FILE__)));
define('TS_PROFILE', admin_url('admin.php?page=ts-profile'));
define('TS_ADMIN_DASHBOARD', admin_url('index.php'));
define('TS_ORGANIZER_DASHBOARD', admin_url('admin.php?page=ts-entries'));
define('TS_STUDIO_DASHBOARD', admin_url('admin.php?page=ts-my-entries'));
define('TS_INDIVIDUAL_DASHBOARD', admin_url('admin.php?page=ts-my-entries'));
define('TS_ZIP_ATTACHMENTS_URL', plugins_url() ."/".dirname( plugin_basename( __FILE__ ) ) ."/includes" );

$ts_upload_dir = wp_upload_dir();
$musics_dir = $ts_upload_dir['basedir'].'/transcend-musics';
$critiques_dir = $ts_upload_dir['basedir'].'/transcend-critiques';

define('TS_MUSIC_ZIP_FOLDER', $musics_dir );
define('TS_CRITIQUES_FOLDER', $critiques_dir );

/* API */ /*NOT SURE IF IT'S SAFE TO ADD IT HERE, PLEASE ADVISE*/
define('MC_API_KEY', '3afbad4ea0d6293c8743a81a40986356-us15');

require_once(TS_INCLUDES . 'ts-main.php');
require_once(TS_INCLUDES . 'ts-post-types.php');
require_once(TS_INCLUDES . 'ts-taxonomies.php');
require_once(TS_INCLUDES . 'ts-functions.php');
require_once(TS_INCLUDES . 'ts-tools.php');
require_once(TS_INCLUDES . 'ts-defaults.php');
require_once(TS_INCLUDES . 'ts-shortcodes.php');
require_once(TS_INCLUDES . 'ts-ajax.php');
require_once(TS_INCLUDES . 'ts-admin.php');
require_once(TS_INCLUDES . 'ts-user.php');
require_once(TS_INCLUDES . 'ts-acf-fields.php');
require_once(TS_INCLUDES . 'ts-acf-hooks.php');
require_once(TS_INCLUDES . 'ts-notifications.php');
require_once(TS_INCLUDES . 'ts-cron-jobs.php');
require_once(TS_INCLUDES . 'ts-meta-boxes.php');
require_once(TS_INCLUDES . 'ts-results.php');

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

if (! function_exists('wp_new_user_notification') ) {

	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		
	    if ( $deprecated !== null ) {
	      _deprecated_argument( __FUNCTION__, '4.3.1' );
	    }

	    global $wpdb, $wp_hasher;
	    $user = get_userdata( $user_id );

	    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	    if ( 'user' !== $notify ) {
	      $message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
	      $message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
	      $message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

	      //@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
	    }

	    if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
	      return;
	    }

	    $key = wp_generate_password( 20, false );

	    do_action( 'retrieve_password_key', $user->user_login, $key );

	    if ( empty( $wp_hasher ) ) {
	      require_once ABSPATH . WPINC . '/class-phpass.php';
	      $wp_hasher = new PasswordHash( 8, true );
	    }
	    $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	    if(in_array('individual', $user->roles)) {

	      $headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@etranscend.com>', 'BCC: Carl D <carld.projects@gmail.com>');
          $subject = 'Congratulations, '.$user->user_login .', you are invited to attend Transcend!';
	      $message = '
	      <p style="text-align:center; margin-bottom:30px;"><span style="display:inline-block;padding:20px;background-color:#000;"><img src="'. TS_URI .'assets/images/logo.png" /></span></p>
	      <p style="text-align:center; font-size:1.6em; font-weight:bold;">You have been invited to join Transcend.</p><br />
	      <p style="text-align:center; font-size:1.3em;">Thank you so much for your submission to attend Transcend this year. Registration happens by submission-only, and we are happy to extend an invitation for you to attend! You demonstrate the dedication, passion, and aspirations that we want Transcend dancers to feel united by.</p>
          <p style="text-align:center; font-size:1.3em;">Please <a href="'. network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') .'">click here</a> to create your password and access your account.</p>
          <p style="text-align:center; font-size:1.3em;">Your username is <strong>'. $user->user_login .'</strong></p><br />
	      <p style="font-size:1.3em;">Please let us know if you have any questions, and we look forward to seeing you this year!</p>
	      <p style="font-size:1.3em;">Best regards,</p>
	      <p style="font-size:1.3em;">the Transcend Team</p>
	      ';

	      wp_mail($user->user_email, $subject, $message, $headers);
	    }

        if(in_array('studio', $user->roles)) {

            $headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@etranscend.com>', 'BCC: Carl D <carld.projects@gmail.com>');
            $subject = 'Congratulations, '.$user->user_login .', you are invited to attend Transcend!';
            $message = '
	      <p style="text-align:center; margin-bottom:30px;"><span style="display:inline-block;padding:20px;background-color:#000;"><img src="'. TS_URI .'assets/images/logo.png" /></span></p>
	      <p style="text-align:center; font-size:1.6em; font-weight:bold;">You have been invited to join Transcend.</p>
	      <p style="text-align:center; font-size:1.3em;">Please <a href="'. network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') .'">click here</a> to create your password and access your account.</p>
	      <p style="text-align:center; font-size:1.3em;">Your username is <strong>'. $user->user_login .'</strong></p><br />
	      <p style="font-size:1.3em;">Please let us know if you have any questions, and we hope to see you this year!</p>
	      <p style="font-size:1.3em;">Best regards,</p>
	      <p style="font-size:1.3em;">the Transcend Team</p>
	      ';

            wp_mail($user->user_email, $subject, $message, $headers);
        }

	}
}