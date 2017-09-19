<?php
function ts_auto_delete_music_cron( $date ) {
    $args = array(
        'post_status' => array('pending', 'publish', 'unpaid', 'paid', 'unpaidcheck', 'paidcheck'),
        'meta_query' => array(
            array(
                'key' => 'tour_end_date',
                'value' => $date
            ),
        )
    );
    $entries = ts_get_posts('ts_entry', -1, $args);

    $routines_musickey = array();
    if($entries) {
        foreach ($entries as $entry) {
            setup_postdata($entry);
            $entry_id = $entry->ID;
            $competition = get_post_meta($entry_id, 'competition', true);
            if($competition){
                $routines = $competition['routines'];
                if($routines){
                    foreach ($routines as $key => $routine){
                        $music_attachment_id = (int)$routine['music'];
                        ts_delete_attachment( $music_attachment_id, true );
                        $routines_musickey[] = $music_attachment_id;
                        $competition['routines'][$key]['music'] = '';
                    }
                }
            }
            update_post_meta($entry_id, 'competition', $competition);
        }
    }

    if( $routines_musickey ) {
        $args = array(
            'post_status' => array('publish'),
            'meta_query' => array(
                array(
                    'key' => 'music',
                    'value' => $routines_musickey,
                    'compare' => 'IN'
                ),
            )
        );
        $get_routines = ts_get_posts('ts_routine', -1, $args);
        if($get_routines) {
            foreach ($get_routines as $get_routine) {
                setup_postdata($get_routine);
                $routine_id = $get_routine->ID;
                update_post_meta($routine_id, 'music', '');
            }
        }
    }
}

function ts_autodelete_credit_cron_job( $credit_id ) {

    $entry_id = get_post_meta( $credit_id, 'entry_id', true );
    delete_post_meta($entry_id,'amount_credited');
    update_post_meta($entry_id,'credit_expired',true);
    wp_delete_post($credit_id);

}