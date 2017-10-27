<?php
function ts_new_entry_admin_notification($entry_id, $user_id, $payment_method='stripe_payment') {

	$admin 	 = get_user_by('login', 'transcend_admin');
	$to 	 = array($admin->user_email);
	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@etranscend.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: TranscendTour Info<info@transcendtour.com>');

	$email 	 = get_field('email', 'user_'. $user_id);

	$studio_name 	 = get_field('studio', 'user_'. $user_id);
	$studio_director = get_field('studio_director', 'user_'. $user_id);
	$studio_address  = get_field('address', 'user_'. $user_id);
	$studio_city 	 = get_field('city', 'user_'. $user_id);
	$studio_state 	 = get_field('state', 'user_'. $user_id);
	$studio_zipcode  = get_field('zipcode', 'user_'. $user_id);
	$studio_country  = get_field('country', 'user_'. $user_id);
	$studio_phone 	 = get_field('phone', 'user_'. $user_id);
	$studio_email 	 = $email ? $email : $user->user_email;
	$studio_cell 	 = get_post_meta($entry_id, 'studio_cell', true);
	$studio_contact  = get_post_meta($entry_id, 'studio_contact', true);

	$studio_address  = $studio_address .' '. $studio_city .' '. $studio_state .' '. $studio_zipcode .' '. $studio_country;
	
	$subject = 'New Registration - '. $studio_name;
	$payment = $payment_method == 'stripe_payment' ? 'Credit Card (Stripe)' : 'Mail in Check';

	$body 	 = '
		<table cellpadding="1" cellspacing="0" border="0">
			<tr>
				<td><strong>Method of Payment:</strong></td>
				<td>'. $payment .'</td>
			</tr>
			<tr>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td><strong>Studio Name:</strong></td>
				<td>'. $studio_name .'</td>
			</tr>
			<tr>
				<td><strong>Director\'s Name:</strong></td>
				<td>'. $studio_director .'</td>
			</tr>
			<tr>
				<td><strong>Address:</strong></td>
				<td>'. $studio_address .'</td>
			</tr>
			<tr>
				<td><strong>Studio Phone Number:</strong></td>
				<td>'. $studio_phone .'</td>
			</tr>
			<tr>
				<td><strong>Email:</strong></td>
				<td>'. $studio_email .'</td>
			</tr>
			<tr>
				<td><strong>Cell:</strong></td>
				<td>'. $studio_cell .'</td>
			</tr>
			<tr>
				<td><strong>Studio Contact Name:</strong></td>
				<td>'. $studio_contact .'</td>
			</tr>
		</table>	
	';
	 
	wp_mail($to, $subject, $body, $headers);	
}

function ts_new_entry_user_notification($entry_id, $user_id) {

	$user 	 = get_user_by( 'id', $user_id);
	$to 	 = array($user->user_email);
	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@etranscend.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: TranscendTour Info<info@transcendtour.com>');
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

	$admin 	 = get_user_by('login', 'transcend_admin');
	$to 	 = array($admin->user_email);
	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@etranscend.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: TranscendTour Info<info@transcendtour.com>');
	$subject = 'A registration has been edited';

	$body = '
	<p style="font-size:1.3em; font-weight:bold;">A registration has been edited.</p>
	<p style="font-weight:bold;">Here is the new summary:</p>
	';

	$details = ts_display_entry_details($entry_id, $user_id);

	$body.= $details;
	 
	wp_mail($to, $subject, $body, $headers);	
}

function ts_new_invoice_user_notification($entry_id, $invoice_id) {

    $get_profile = get_post_meta($entry_id, 'profile', true);
    $get_entry_types = get_the_terms($entry_id, 'ts_entry_type');
    if ( $get_entry_types && ! is_wp_error( $get_entry_types ) ) {
        $get_entry_types_pluck = wp_list_pluck($get_entry_types,'name');
        if ( $get_profile ) {
            $email_field = in_array('Studio',$get_entry_types_pluck) ? 'studio_email' : in_array('Individual',$get_entry_types_pluck) ? 'email' : '';
            if( $email_field ){
                $to = $get_profile[$email_field];

                $invoice_amount = get_post_meta($invoice_id, 'invoice_amount', true);
                $invoice_note = get_post_meta($invoice_id, 'invoice_note', true);
                $pay_now_link = admin_url('admin.php?page=ts-entry-pay-invoice&action=pay_invoice&id='.$entry_id.'&evid='.$invoice_id);;

                $headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@transcendtour.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: TranscendTour Info<info@transcendtour.com>');
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
        }
    }

}

function ts_tour_results_notification($tour_id) {

	$user 	 = get_user_by( 'id', $user_id);
	$to 	 = array($user->user_email);
	$headers = array('Content-Type: text/html; charset=UTF-8','From: Transcend <noreply@etranscend.com>', 'CC: Jasmine R <jr@sitesbycarlos.com>', 'BCC: TranscendTour Info<info@transcendtour.com>');
	$subject = 'video critiques and weekend results';

	$body = '
	<p style="text-align:center; margin-bottom:30px;"><span style="display:inline-block;padding:20px;background-color:#000;"><img src="'. TS_URI .'assets/images/logo.png" /></span></p>
	<p style="text-align:center; font-size:1.6em; font-weight:bold;">You can now log into your online portal to view video critiques and weekend results.</p>
	<p style="text-align:center; font-size:1.6em; font-weight:bold;"><a href="'. admin_url('admin.php?page=ts-results&tour='. $tour_id) .'">Click Here</a></p>
	';

	wp_mail($to, $subject, $body, $headers);	
}