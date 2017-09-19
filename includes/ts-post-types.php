<?php
function ts_register_custom_post_types() {

	register_post_type('ts_tour', array(
		'label' => 'Tours',
		'description' => '',
		'public' => true,
		'menu_position' => 7,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'capability_type' => array('tour','tours'),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'tour', 'with_front' => true),
		'query_var' => true,
		'supports' => array('title'),
		'taxonomies' => array(),
		'labels' => array (
			'name' => 'Tours',
			'singular_name' => 'Tour',
			'menu_name' => 'Tours',
			'all_items' => 'Tours',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Tour',
			'edit' => 'Edit',
			'edit_item' => 'Edit Tour',
			'new_item' => 'New Tour',
			'view' => 'View Tour',
			'view_item' => 'View Tour',
			'search_items' => 'Search Tours',
			'not_found' => 'No Tours Found',
			'not_found_in_trash' => 'No Tours Found in Trash',
			'parent' => 'Parent Tour',
		)
	));

	register_post_type('ts_event', array(
		'label' => 'Events',
		'description' => '',
		'public' => true,
		'menu_position' => 7,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'capability_type' => array('event','events'),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'event', 'with_front' => true),
		'query_var' => true,
		'supports' => array('title'),
		'taxonomies' => array(),
		'labels' => array (
			'name' => 'Events',
			'singular_name' => 'Event',
			'menu_name' => 'Events',
			'all_items' => 'Events',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Event',
			'edit' => 'Edit',
			'edit_item' => 'Edit Event',
			'new_item' => 'New Event',
			'view' => 'View Event',
			'view_item' => 'View Event',
			'search_items' => 'Search Events',
			'not_found' => 'No Events Found',
			'not_found_in_trash' => 'No Events Found in Trash',
			'parent' => 'Parent Event',
		)
	));

	register_post_type('ts_entry', array(
		'label' => 'Entries',
		'description' => '',
		'public' => true,
		'menu_position' => 6,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'capability_type' => array('entry','entries'),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'entry', 'with_front' => true),
		'query_var' => true,
		'supports' => array('title','author'),
		'taxonomies' => array('entry_type'),
		'labels' => array (
			'name' => 'Entries',
			'singular_name' => 'Entry',
			'menu_name' => 'Entries',
			'all_items' => 'Entries',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Entry',
			'edit' => 'Edit',
			'edit_item' => 'Edit Entry',
			'new_item' => 'New Entry',
			'view' => 'View Entry',
			'view_item' => 'View Entry',
			'search_items' => 'Search Entries',
			'not_found' => 'No Entries Found',
			'not_found_in_trash' => 'No Entries Found in Trash',
			'parent' => 'Parent Entry',
		)
	));	

	register_post_type('ts_coupon', array(
		'label' => 'Vouchers',
		'description' => '',
		'public' => true,
		'menu_position' => 11,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'capability_type' => array('coupon','coupons'),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'coupon', 'with_front' => true),
		'query_var' => true,
		'supports' => array('title'),
		'taxonomies' => array(),
		'labels' => array (
			'name' => 'Vouchers',
			'singular_name' => 'Voucher',
			'menu_name' => 'Vouchers',
			'all_items' => 'Vouchers',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Voucher',
			'edit' => 'Edit',
			'edit_item' => 'Edit Voucher',
			'new_item' => 'New Voucher',
			'view' => 'View Voucher',
			'view_item' => 'View Voucher',
			'search_items' => 'Search Vouchers',
			'not_found' => 'No Vouchers Found',
			'not_found_in_trash' => 'No Vouchers Found in Trash',
			'parent' => 'Parent Voucher',
		)
	));
	
	register_post_type('ts_studio_roster', array(
		'label' => 'Studio Rosters',
		'description' => '',
		'public' => true,
		'menu_position' => 101,
		'capability_type' => array('studio_roster','studio_rosters'),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'studio-roster', 'with_front' => true),
		'query_var' => true,
		'supports' => array('title','author'),
		'taxonomies' => array('ts_rostertype', 'ts_agediv'),
		'labels' => array (
			'name' => 'Studio Rosters',
			'singular_name' => 'Studio Roster',
			'menu_name' => 'Studio Rosters',
			'all_items' => 'Studio Rosters',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Studio Roster',
			'edit' => 'Edit',
			'edit_item' => 'Edit Studio Roster',
			'new_item' => 'New Studio Roster',
			'view' => 'View Studio Roster',
			'view_item' => 'View Studio Roster',
			'search_items' => 'Search Studio Rosters',
			'not_found' => 'No Studio Rosters Found',
			'not_found_in_trash' => 'No Studio Rosters Found in Trash',
			'parent' => 'Parent Studio Roster',
		)
	));

	register_post_type('ts_sibling', array(
		'label' => 'Siblings',
		'description' => '',
		'public' => true,
		'menu_position' => 103,
		'capability_type' => array('indiv_sibling', 'indiv_siblings'),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'individual-sibling', 'with_front' => true),
		'query_var' => true,
		'supports' => array('title','author'),
		'taxonomies' => array('ts_rostertype', 'ts_agediv'),
		'labels' => array (
			'name' => 'Siblings',
			'singular_name' => 'Sibling',
			'menu_name' => 'Siblings',
			'all_items' => 'Siblings',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Sibling',
			'edit' => 'Edit',
			'edit_item' => 'Edit Sibling',
			'new_item' => 'New Sibling',
			'view' => 'View Sibling',
			'view_item' => 'View Sibling',
			'search_items' => 'Search Siblings',
			'not_found' => 'No Siblings Found',
			'not_found_in_trash' => 'No Siblings Found in Trash',
			'parent' => 'Parent Sibling',
		)
	));

	register_post_type('ts_routine', array(
		'label' => 'Routines',
		'description' => '',
		'public' => true,
		'menu_position' => 102,
		'capability_type' => array('routine','routines'),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'routine', 'with_front' => true),
		'query_var' => true,
		'supports' => array('title','author'),
		'taxonomies' => array(),
		'labels' => array (
			'name' => 'Routines',
			'singular_name' => 'Routine',
			'menu_name' => 'Routines',
			'all_items' => 'Routines',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Routine',
			'edit' => 'Edit',
			'edit_item' => 'Edit Routine',
			'new_item' => 'New Routine',
			'view' => 'View Routine',
			'view_item' => 'View Routine',
			'search_items' => 'Search Routines',
			'not_found' => 'No Routines Found',
			'not_found_in_trash' => 'No Routines Found in Trash',
			'parent' => 'Parent Routine',
		)
	));

    register_post_type('ts_invoice', array(
        'label' => 'Invoices',
        'description' => '',
        'public' => true,
        'menu_position' => 7,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => array('invoice','invoices'),
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => 'invoice', 'with_front' => true),
        'query_var' => true,
        'supports' => array('title'),
        'taxonomies' => array(),
        'labels' => array (
            'name' => 'Invoices',
            'singular_name' => 'Invoice',
            'menu_name' => 'Invoices',
            'all_items' => 'Invoices',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Invoice',
            'edit' => 'Edit',
            'edit_item' => 'Edit Invoice',
            'new_item' => 'New Invoice',
            'view' => 'View Invoice',
            'view_item' => 'View Invoice',
            'search_items' => 'Search Invoices',
            'not_found' => 'No Invoices Found',
            'not_found_in_trash' => 'No Invoices Found in Trash',
            'parent' => 'Parent Invoice',
        )
    ));

    register_post_type('ts_credit', array(
        'label' => 'My credits',
        'description' => '',
        'public' => true,
        'menu_position' => 8,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => array('credit','credits'),
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => 'credit', 'with_front' => true),
        'query_var' => true,
        'supports' => array('title','author'),
        'taxonomies' => array(),
        'labels' => array (
            'name' => 'My credits',
            'singular_name' => 'Credit',
            'menu_name' => 'Credits',
            'all_items' => 'Credits',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Credit',
            'edit' => 'Edit',
            'edit_item' => 'Edit Credit',
            'new_item' => 'New Credit',
            'view' => 'View Credit',
            'view_item' => 'View Credit',
            'search_items' => 'Search Credits',
            'not_found' => 'No Credits Found',
            'not_found_in_trash' => 'No Credits Found in Trash',
            'parent' => 'Parent Credit',
        )
    ));

}

function ts_register_custom_post_status() {

	register_post_status( 'unpaid', array(
		'label'                     => _x( 'Incomplete', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'label_count'               => _n_noop( 'Unpaid <span class="count">(%s)</span>', 'Unpaid <span class="count">(%s)</span>' ),
	) );	

	register_post_status( 'unpaidcheck', array(
		'label'                     => _x( 'Incomplete', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'label_count'               => _n_noop( 'Unpaid <span class="count">(%s)</span>', 'Unpaid <span class="count">(%s)</span>' ),
	) );	

	register_post_status( 'paid', array(
		'label'                     => _x( 'Complete', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'label_count'               => _n_noop( 'Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>' ),
	) );	

	register_post_status( 'paidcheck', array(
		'label'                     => _x( 'Complete', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'label_count'               => _n_noop( 'Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>' ),
	) );

    register_post_status( 'outstanding_amount', array(
        'label'                     => _x( 'Outstanding Amount', 'post' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => false,
        'show_in_admin_status_list' => false,
        'label_count'               => _n_noop( 'Outstanding Amount <span class="count">(%s)</span>', 'Outstanding Amount <span class="count">(%s)</span>' ),
    ) );

	register_post_status( 'inactive', array(
		'label'                     => _x( 'Inactive', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => false,
		'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>' ),
	) );	
}

