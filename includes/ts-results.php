<?php
function ts_hiscore_solo_mini($tour_id) {
	$winners = ts_winners_array($tour_id, 'Mini', 1);
    return $winners;
}

function ts_hiscore_solo_junior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Junior', 1);
    return $winners;
}

function ts_hiscore_solo_teen($tour_id) {
	$winners = ts_winners_array($tour_id, 'Teen', 1);
    return $winners;
}

function ts_hiscore_solo_senior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Senior', 1);
    return $winners;
}

function ts_hiscore_solo_pro($tour_id) {
	$winners = ts_winners_array($tour_id, 'Pro', 1);
    return $winners;
}

/************************/

function ts_hiscore_duotrio_mini($tour_id) {
	$winners = ts_winners_array($tour_id, 'Mini', 2);
}

function ts_hiscore_duotrio_junior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Junior', 2);
    return $winners;
}

function ts_hiscore_duotrio_teen($tour_id) {
	$winners = ts_winners_array($tour_id, 'Teen', 2);
    return $winners;
}

function ts_hiscore_duotrio_senior($tour_id) {
	$winners = ts_winners_array($tour_id, 'Senior', 2);
    return $winners;
}

function ts_hiscore_duotrio_pro($tour_id) {
	$winners = ts_winners_array($tour_id, 'Pro', 2);
    return $winners;
}

/************************/

function ts_hiscore_smallgroup_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 3, 3);
    return $winners;
}

function ts_hiscore_smallgroup_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 3, 3);
    return $winners;
}

/************************/

function ts_hiscore_largegroup_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 4, 3);
    return $winners;
}

function ts_hiscore_largegroup_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 4, 3);
    return $winners;
}

/************************/

function ts_hiscore_line_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 5, 3);
    return $winners;
}

function ts_hiscore_line_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 5, 3);
    return $winners;
}

function ts_hiscore_line_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 5, 3);
    return $winners;
}

function ts_hiscore_line_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 5, 3);
    return $winners;
}

function ts_hiscore_line_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 5, 3);
    return $winners;
}

/************************/

function ts_hiscore_production_mini($tour_id) {
    $winners = ts_winners_array($tour_id, 'Mini', 6, 3);
    return $winners;
}

function ts_hiscore_production_junior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Junior', 6, 3);
    return $winners;
}

function ts_hiscore_production_teen($tour_id) {
    $winners = ts_winners_array($tour_id, 'Teen', 6, 3);
    return $winners;
}

function ts_hiscore_production_senior($tour_id) {
    $winners = ts_winners_array($tour_id, 'Senior', 6, 3);
    return $winners;
}

function ts_hiscore_production_pro($tour_id) {
    $winners = ts_winners_array($tour_id, 'Pro', 6, 3);
    return $winners;
}

/************************/

function ts_hiscore_overall_mini($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Mini');
    return $winners;
}

function ts_hiscore_overall_junior($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Junior');
    return $winners;
}

function ts_hiscore_overall_teen($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Teen');
    return $winners;
}

function ts_hiscore_overall_senior($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Senior');
    return $winners;
}

function ts_hiscore_overall_pro($tour_id) {
	$winners = ts_overallwinners_array($tour_id, 'Pro');
    return $winners;
}