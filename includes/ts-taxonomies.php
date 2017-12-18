<?php
function ts_register_custom_taxonomies() {

	register_taxonomy(
		'ts_entry_type',
		array('ts_entry'),
		array(
			'public' => false,
			'hierarchical' => false,
			'label' => 'Type',
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'entry-type'),
			'capabilities' =>  array('add_ts_entry'),
			'show_admin_column' => true,
		) 
	); 	

	register_taxonomy(
		'ts_rostertype',
		array('ts_studio_roster', 'ts_sibling'),
		array(
			'public' => false,
			'hierarchical' => false,
			'label' => 'Type',
			'show_ui' => false,
			'show_in_menu' => false,
			'query_var' => true,
			'rewrite' => array('slug' => 'roster-type'),
			'capabilities' =>  array('add_ts_roster', 'add_ts_indiv_dancer'),
			'show_admin_column' => true,
		) 
	); 	

	register_taxonomy(
		'ts_agediv',
		array('ts_studio_roster', 'ts_sibling', 'ts_routine'),
		array(
			'public' => false,
			'hierarchical' => false,
			'label' => 'Type',
			'show_ui' => false,
			'show_in_menu' => false,
			'query_var' => true,
			'rewrite' => array('slug' => 'age-division'),
			'capabilities' =>  array('add_ts_roster', 'add_ts_indiv_dancer'),
			'show_admin_column' => true,
		) 
	);

    register_taxonomy(
        'ts_schedules_type',
        array('ts_event'),
        array(
            'public' => false,
            'hierarchical' => false,
            'label' => 'Type',
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'schedules-type'),
            'capabilities' =>  array('is_organizer'),
            'show_admin_column' => true,
        )
    );

    register_taxonomy(
        'ts_adjudicated_awards',
        array('ts_award'),
        array(
            'public' => false,
            'hierarchical' => false,
            'label' => 'Adjudicated Awards',
            'show_ui' => false,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'adjudicated-awards'),
            'capabilities' =>  array('is_organizer'),
            'show_admin_column' => false,
        )
    );

}