<?php
function ts_new_entry_admin_notification($entry_id, $user_id, $payment_method='stripe_payment') {

	$args = array(
		'role' => 'event_organizer', 
	);	
	$admins = get_users($args);
	$admin_emails = wp_list_pluck($admins, 'user_email');
	$admin_emails[] = get_option('admin_email');
	$admin_emails = array('sitesbycarlos@gmail.com'); //temp
	$to 	 = $admin_emails;
	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@transcendtour.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: Carl D <carld.projects@gmail.com>');

	$user = get_userdata($user_id);

	$studio_name 	 = get_field('studio', 'user_'. $user_id);
	$studio_director = get_field('studio_director', 'user_'. $user_id);
	$studio_address  = get_field('address', 'user_'. $user_id);
	$studio_city 	 = get_field('city', 'user_'. $user_id);
	$studio_state 	 = get_field('state', 'user_'. $user_id);
	$studio_zipcode  = get_field('zipcode', 'user_'. $user_id);
	$studio_country  = get_field('country', 'user_'. $user_id);
	$studio_phone 	 = get_field('phone', 'user_'. $user_id);
	$studio_email 	 = $user->user_email;
	$studio_cell 	 = get_post_meta($entry_id, 'studio_cell', true);
	$studio_contact  = get_post_meta($entry_id, 'studio_contact', true);

	$studio_address  = $studio_address .' '. $studio_city .' '. $studio_state .' '. $studio_zipcode .' '. $studio_country;
	
	$subject = 'New Registration - '. $studio_name;
	$payment = $payment_method == 'stripe_payment' ? 'Credit Card (Stripe)' : 'Mail in Check';

	$body = '';

	$body.= '<h2>Method of Payment: '. $payment .'</h2>';
	$body.= '<h2>Profile:</h2>';
	$body.= ts_display_user_details($user_id);
	$body.= '<h2>Summary:</h2>';
	$body.= ts_display_entry_details($entry_id, $user_id);

	wp_mail($to, $subject, $body, $headers);	
}

function ts_new_entry_user_notification($entry_id, $user_id) {

	$user 	 = get_user_by('id', $user_id);
	$to 	 = array($user->user_email);
	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@transcendtour.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: Carl D <carld.projects@gmail.com>');
	$subject = 'You have successfully registered for Transcend';

	$body = '
	<p style="text-align:center; margin-bottom:30px;"><span style="display:inline-block;padding:20px;background-color:#000;"><img src="'. TS_URI .'assets/images/logo.png" /></span></p>
	<p style="text-align:center; font-size:1.6em; font-weight:bold;">Thank you for registering for Transcend.</p>
	<p style="text-align:center; font-size:1.3em; font-weight:bold;">Here are the details of your registration:</p>
	';

	$details = ts_display_entry_details($entry_id, $user_id);

	$body.= $details;
	 
	wp_mail($to, $subject, $body, $headers);	
}

function ts_reg_edited_notification($entry_id, $user_id) {

	$args = array(
		'role' => 'event_organizer', 
	);	
	$admins = get_users($args);
	$admin_emails = wp_list_pluck($admins, 'user_email');
	$admin_emails[] = get_option('admin_email');

	$admin_emails = array('sitesbycarlos@gmail.com'); //temp
	$to 	 = $admin_emails;

	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@transcendtour.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: Carl D <carld.projects@gmail.com>');
	$subject = 'A registration has been edited';

	$body = '
	<p style="font-size:1.3em; font-weight:bold;">A registration has been edited.</p>
	<p style="font-weight:bold;">Here is the new details:</p>
	';

	$body.= '<h2>Profile:</h2>';
	$body.= ts_display_user_details($user_id);
	$body.= '<h2>Summary:</h2>';
	$body.= ts_display_entry_details($entry_id, $user_id);

	wp_mail($to, $subject, $body, $headers);	
}

function ts_new_invoice_user_notification($entry_id, $invoice_id) {

	$entry 	 = get_post($entry_id);
	$user_id = $entry->post_author;
	$user 	 = get_user_by('id', $user_id);
	$to 	 = array($user->user_email);

    $invoice_amount = get_post_meta($invoice_id, 'invoice_amount', true);
    $invoice_note = get_post_meta($invoice_id, 'invoice_note', true);
    $pay_now_link = admin_url('admin.php?page=ts-entry-pay-invoice&action=pay_invoice&id='.$entry_id.'&evid='.$invoice_id);;

    $headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@transcendtour.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: Carl D <carld.projects@gmail.com>');
    $subject = 'Invoice #'. $invoice_id .' has been created';

    $body = '
            <p style="font-size:1.3em; font-weight:bold;">An invoice has been created.</p>
            <p style="font-weight:bold;">Here is the summary:</p>
            ';
    $body .= '
            <table cellpadding="1" cellspacing="0" border="0">
                <tr>
                    <td><strong>Amount Due:</strong></td>
                    <td>'. $invoice_amount .'</td>
                </tr>
                <tr>
                    <td><strong>Note:</strong></td>
                    <td>'. $invoice_note .'</td>
                </tr>
                 <tr>
                    <td><strong>Pay Now:</strong></td>
                    <td>'. $pay_now_link .'</td>
                </tr>
            </table>	
        ';
    wp_mail($to, $subject, $body, $headers);
}

function ts_tour_results_notification($tour_id) {

	$ids = ts_tour_studio_ids($tour_id);
	$args = array(
		'include' => $ids, 
	);	
	$users = get_users($args);
	$users_emails = wp_list_pluck($users, 'user_email');

	$to 	 = $users_emails;
	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@transcendtour.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: Carl D <carld.projects@gmail.com>');
	$subject = 'Video critiques and Weekend results';

	$body = '
	<p style="text-align:center; margin-bottom:30px;"><span style="display:inline-block;padding:20px;background-color:#000;"><img src="'. TS_URI .'assets/images/logo.png" /></span></p>
	<p style="text-align:center; font-size:1.6em; font-weight:bold;">You can now log into your online portal to view video critiques and weekend results.</p>
	<p style="text-align:center; font-size:1.6em; font-weight:bold;"><a href="'. admin_url('admin.php?page=ts-my-results&tour='. $tour_id) .'">Click Here</a></p>
	';

	wp_mail($to, $subject, $body, $headers);	
}