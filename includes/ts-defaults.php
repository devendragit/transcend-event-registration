<?php
function ts_get_steps_studio() {

	$steps = array();

	$steps['profile'] = array(
		'id' => 1,
		'title' => 'Profile',
		'title_short' => 'Profile',
	);

	$steps['studio_roster'] = array(
		'id' => 2,
		'title' => 'Studio Roster',
		'title_short' => 'Studio Roster',
	);

	$steps['workshop'] = array(
		'id' => 3,
		'title' => 'Workshop',
		'title_short' => 'Workshop',
	);

	$steps['competition'] = array(
		'id' => 4,
		'title' => 'Competition',
		'title_short' => 'Competition',
	);

	$steps['confirmation'] = array(
		'id' => 5,
		'title' => 'Confirmation',
		'title_short' => 'Confirmation',
	);

	$steps['payment'] = array(
		'id' => 6,
		'title' => 'Payment',
		'title_short' => 'Payment',
	);

	$steps['completed'] = array(
		'id' => 7,
		'title' => 'Completed',
		'title_short' => 'Completed',
	);

	return $steps;
}

function ts_get_steps_individual() {

	$steps = array();

	$steps['profile'] = array(
		'id' => 1,
		'title' => 'Profile',
		'title_short' => 'Profile',
	);

	$steps['workshop'] = array(
		'id' => 2,
		'title' => 'Workshop',
		'title_short' => 'Workshop',
	);

	$steps['competition'] = array(
		'id' => 3,
		'title' => 'Competition',
		'title_short' => 'Competition',
	);

	$steps['confirmation'] = array(
		'id' => 4,
		'title' => 'Confirmation',
		'title_short' => 'Confirmation',
	);

	$steps['payment'] = array(
		'id' => 5,
		'title' => 'Payment',
		'title_short' => 'Payment',
	);

	$steps['completed'] = array(
		'id' => 6,
		'title' => 'Completed',
		'title_short' => 'Completed',
	);

	return $steps;
}

function ts_get_tour_cities() {

	$tour = array();

	$tour[] = array(
		'id' => 1,
		'title' => 'November 2-4, 2017 - Provo, Ut',
		'city' => 'Provo, Ut',	
		'venue' => 'Utah Valley Convention Center',
		'date_from' => 'November 2, 2017',
		'date_to' => 'November 4, 2017',
	);

	$tour[] = array(
		'id' => 2,
		'title' => 'January 12-14, 2018 - Glendale, AZ',
		'city' => 'Glendale, AZ',	
		'venue' => 'Renaissance Glendale Hotel and Spa',
		'date_from' => 'January 12, 2018',
		'date_to' => 'January 14, 2018',
	);

	$tour[] = array(
		'id' => 3,
		'title' => 'February 2-4, 2018 - Miami, FL',
		'city' => 'Miami, FL',	
		'venue' => 'Doubletree by Hilton Hotel',
		'date_from' => 'February 2, 2018',
		'date_to' => 'February 4, 2018',
	);

	$tour[] = array(
		'id' => 4,
		'title' => 'February 9-11, 2018 - Valley Forge, PA',
		'city' => 'Valley Forge, PA',	
		'venue' => 'Valley Forge Casino Resort',
		'date_from' => 'February 9, 2018',
		'date_to' => 'February 11, 2018',
	);

	$tour[] = array(
		'id' => 5,
		'title' => 'March 9-11, 2018 - Greenville, NC',
		'city' => 'Greenville, NC',	
		'venue' => 'Greenville Convention Center',
		'date_from' => 'March 9, 2018',
		'date_to' => 'March 11, 2018',
	);

	$tour[] = array(
		'id' => 6,
		'title' => 'April 6-8, 2018 - Las Vegas, NV',
		'city' => 'Las Vegas, NV',	
		'venue' => 'Hilton Lake Las Vegas Resort and Spa',
		'date_from' => 'April 6, 2018',
		'date_to' => 'April 8, 2018',
	);
    $tour[] = array(
        'id' => 7,
        'title' => 'Aug 21-23, 2017 test tour date - Las Vegas, NV',
        'city' => 'Las Vegas, NV',
        'venue' => 'Hilton Lake Las Vegas Resort and Spa',
        'date_from' => 'August 21, 2017',
        'date_to' => 'August 23, 2017',
    );
	return $tour;
}

function ts_get_discounts() {

	$discounts = array();

	$discounts[] = array(
		'id' => 1,
		'title' => '50% Off Regional Scholarship',
		'desc' => '50% Off whatever workshop age division they are registering for (for the following tour season)',
	);

	$discounts[] = array(
		'id' => 2,
		'title' => 'One-City Regional Scholarship',
		'desc' => 'Free for one tour stop they are registering for (for the following tour season)',
	);

	$discounts[] = array(
		'id' => 3,
		'title' => 'Transcendent',
		'desc' => 'Free for all tour stops for one year from when they receive the scholarship',
	);

	$discounts[] = array(
		'id' => 4,
		'title' => 'Multi-City Dancer Discount',
		'desc' => '15% Off when they register for more than one tour stop in one year (each subsequent registration receives 15% off workshop fees)',
	);

	$discounts[] = array(
		'id' => 5,
		'title' => 'Multi-City Teacher Discount',
		'desc' => '50% Off when they register for more than one tour stop in one year (each subsequent registration receives 50% off workshop fees)',
	);

	$discounts[] = array(
		'id' => 6,
		'title' => 'College Discount',
		'desc' => '20% off workshop fees',
	);

	$discounts[] = array(
		'id' => 7,
		'title' => 'SAG Discount',
		'desc' => '10% off workshop fees',
	);

	return $discounts;
}	

function ts_get_workshop_durations() {

	$duration = array();

	$duration[] = array(
		'id' => 1,
		'title' => 'Full Weekend',
		'desc' => 'Standard',
	);

	$duration[] = array(
		'id' => 2,
		'title' => '1-Day',
		'desc' => 'One-day',
	);

	return $duration;
}

function ts_get_fees_meta() {

	$fee_meta = array();

	$fee_meta['munchkin'] = array(
		'fee_early' => 95,
		'fee_standard' => 125,
		'fee_early_oneday' => 95,
		'fee_standard_oneday' => 125,
	);

	$fee_meta['mini'] = array(
		'fee_early' => 245,
		'fee_standard' => 285,
		'fee_early_oneday' => 160,
		'fee_standard_oneday' => 185,
	);

	$fee_meta['junior'] = array(
		'fee_early' => 245,
		'fee_standard' => 285,
		'fee_early_oneday' => 160,
		'fee_standard_oneday' => 185,
	);

	$fee_meta['teen'] = array(
		'fee_early' => 245,
		'fee_standard' => 285,
		'fee_early_oneday' => 160,
		'fee_standard_oneday' => 185,
	);

	$fee_meta['senior'] = array(
		'fee_early' => 245,
		'fee_standard' => 285,
		'fee_early_oneday' => 160,
		'fee_standard_oneday' => 185,
	);

	$fee_meta['pro'] = array(
		'fee_early' => 250,
		'fee_standard' => 250,
		'fee_early_oneday' => 150,
		'fee_standard_oneday' => 150,
	);

	$fee_meta['teacher'] = array(
		'fee_early' => 285,
		'fee_standard' => 315,
		'fee_early_oneday' => 185,
		'fee_standard_oneday' => 210,
	);

	return $fee_meta;
}

function ts_get_routine_genres() {

	$genres = array();

	$genres[1] = array(
		'id' => 1,
		'title' => 'Ballet',
	);

	$genres[2] = array(
		'id' => 2,
		'title' => 'Jazz',
	);

	$genres[3] = array(
		'id' => 3,
		'title' => 'Hip-Hop',
	);

	$genres[4] = array(
		'id' => 4,
		'title' => 'Contemporary',
	);

	$genres[5] = array(
		'id' => 5,
		'title' => 'Lyrical',
	);

	$genres[6] = array(
		'id' => 6,
		'title' => 'Tap',
	);

	$genres[7] = array(
		'id' => 7,
		'title' => 'Musical Theater',
	);

	$genres[9] = array(
		'id' => 9,
		'title' => 'Open',
	);

	$genres[8] = array(
		'id' => 8,
		'title' => 'Improv',
	);

	return $genres;
}

function ts_get_routine_flows() {

	$flows = array();

	$flows[1] = array(
		'id' => 1,
		'title' => 'Enter With/Exit With',
	);

	$flows[2] = array(
		'id' => 2,
		'title' => 'Enter Without/Exit Without',
	);

	$flows[3] = array(
		'id' => 3,
		'title' => 'Enter With/Exit Without',
	);

	$flows[4] = array(
		'id' => 4,
		'title' => 'Enter Without/Exit With',
	);

	return $flows;
}

function ts_get_routine_props() {

	$props = array();

	$props[2] = array(
		'id' => 2,
		'title' => 'No',
	);

	$props[1] = array(
		'id' => 1,
		'title' => 'Yes',
	);

	return $props;
}

function ts_get_competition_categories() {

	$categories = array();

	$categories[1] = array(
		'id' => 1,
		'title' => 'Solo',
		'desc' => '(1 person)',
		'fee' => 100,
		'time_limit' => 180,
	);

	$categories[2] = array(
		'id' => 2,
		'title' => 'Duo/Trio',
		'desc' => '(2-3 people)',
		'fee' => 60,
		'time_limit' => 180,
	);

	$categories[3] = array(
		'id' => 3,
		'title' => 'Small Group',
		'desc' => '(4-9 people)',
		'fee' => 47,
		'time_limit' => 180,
	);

	$categories[4] = array(
		'id' => 4,
		'title' => 'Large Group',
		'desc' => '(10-16 people)',
		'fee' => 47,
		'time_limit' => 180,
	);

	$categories[5] = array(
		'id' => 5,
		'title' => 'Line',
		'desc' => '(17-24 people)',
		'fee' => 47,
		'time_limit' => 240,
	);

	$categories[6] = array(
		'id' => 6,
		'title' => 'Production',
		'desc' => '(25+ people)',
		'fee' => 47,
		'time_limit' => 300,
	);

	return $categories;
}

function ts_get_adjudicated_awards() {

    $adjudicated_awards = array();

    $adjudicated_awards['platinum'] = array(
        'id' => 1,
        'title' => 'Platinum',
        'min_score' => 290,
        'high_score' => 295,
    );

    $adjudicated_awards['high-silver'] = array(
        'id' => 2,
        'title' => 'High Silver',
        'min_score' => 250,
        'high_score' => 264,
    );

    $adjudicated_awards['high-gold'] = array(
        'id' => 3,
        'title' => 'High Gold',
        'min_score' => 275,
        'high_score' => 289,
    );

    $adjudicated_awards['silver'] = array(
        'id' => 4,
        'title' => 'Silver',
        'min_score' => 235,
        'high_score' => 249,
    );

    $adjudicated_awards['gold'] = array(
        'id' => 5,
        'title' => 'Gold',
        'min_score' => 265,
        'high_score' => 274,
    );

    $adjudicated_awards['bronze'] = array(
        'id' => 6,
        'title' => 'Bronze',
        'min_score' => 200,
        'high_score' => 234,
    );

    return $adjudicated_awards;
}

function ts_winners_placeholder() {

	$winners = array();

	$winners[1] = array(
		'id' => 1,
		'number' => 1,
		'name' => 'Routine 1',
		'studio' => 'Studio Name a',
	);

	$winners[2] = array(
		'id' => 2,
		'number' => 2,
		'name' => 'Routine 2',
		'studio' => 'Studio Name 2',
	);

	$winners[3] = array(
		'id' => 3,
		'number' => 3,
		'name' => 'Routine 3',
		'studio' => 'Studio Name 3',
	);

	$winners[4] = array(
		'id' => 4,
		'number' => 4,
		'name' => 'Routine 4',
		'studio' => 'Studio Name 4',
	);

	$winners[5] = array(
		'id' => 5,
		'number' => 5,
		'name' => 'Routine 5',
		'studio' => 'Studio Name 5',
	);

	return $winners;
}