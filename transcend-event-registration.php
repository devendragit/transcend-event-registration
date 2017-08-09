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

require_once(TS_INCLUDES . 'ts-main.php');
require_once(TS_INCLUDES . 'ts-post-types.php');
require_once(TS_INCLUDES . 'ts-taxonomies.php');
require_once(TS_INCLUDES . 'ts-functions.php');
require_once(TS_INCLUDES . 'ts-tools.php');
require_once(TS_INCLUDES . 'ts-defaults.php');
require_once(TS_INCLUDES . 'ts-shortcodes.php');
require_once(TS_INCLUDES . 'ts-ajax.php');
require_once(TS_INCLUDES . 'ts-pages.php');
require_once(TS_INCLUDES . 'ts-acf-fields.php');
require_once(TS_INCLUDES . 'ts-notifications.php');

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

	      @wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
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

	    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	    $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
	    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

	    $message .= wp_login_url() . "\r\n";

	    //wp_mail($user->user_email, sprintf(__('[%s] Your username and password test'), $blogname), $message);

	    if(in_array('studio', $user->roles) ||  in_array('individual', $user->roles)) {

	      $headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@etranscend.com>', 'BCC: Carl D <carld.projects@gmail.com>');

	      $message = '
	      <p style="text-align:center; margin-bottom:30px;"><span style="display:inline-block;padding:20px;background-color:#000;"><img src="'. TS_URI .'assets/images/logo.png" /></span></p>
	      <p style="text-align:center; font-size:1.6em; font-weight:bold;">You have been invited to join Transcend.</p>
	      <p style="text-align:center; font-size:1.3em; font-weight:bold;">Below are the details to access your account:</p><br /><br />
	      <p>Please visit: <a href="'. home_url('register') .'">'. home_url('register') .'</p>
	      <p>Enter the username and password provided below:</p>
	      <p><strong>Username: </strong>'. $user->user_login .'</p>
	      <p><strong>Password: </strong></p>
	      <p>We can’t wait to dance with you!!</p>
	      ';

	      wp_mail($user->user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message, $headers);
	    }
	}
}