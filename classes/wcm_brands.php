<?php

function register_bip_taxonomy_brands() {

	global $woocommerce;
	
	if(!isset($woocommerce)) return;

    $labels = array(
		'name'              => _x( 'Brands', 'taxonomy general name','woo_bip' ),
		'singular_name'     => _x( 'Brand', 'taxonomy singular name','woo_bip' ),
		'search_items'      => __( 'Search Brands','woo_bip'  ),
		'all_items'         => __( 'All Brands','woo_bip'  ),
		'parent_item'       => __( 'Parent Brand','woo_bip'  ),
		'parent_item_colon' => __( 'Parent Brand:','woo_bip'  ),
		'edit_item'         => __( 'Edit Brand','woo_bip'  ),
		'update_item'       => __( 'Update Brand' ,'woo_bip' ),
		'add_new_item'      => __( 'Add New Brand','woo_bip'  ),
		'new_item_name'     => __( 'New Brand Name','woo_bip'  ),
		'menu_name'         => __( 'Brands','woo_bip'  ),
	);

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'hierarchical' => true,
        'rewrite' => array('slug'=>'product-brands'),
        'query_var' => true,		
    );

	if(post_type_exists('product'))
		register_taxonomy( 'brands', 'product', $args );

}

add_action( 'init', 'register_bip_taxonomy_brands' );

function register_bip_attribute_brands() {

    global $woocommerce, $wpdb;

    if(!isset($woocommerce)) return;

    $attribute = array(
        'attribute_label'   => __( 'Brands', 'woo_bip' ),
        'attribute_name'    => 'brands',
        'attribute_type'    => 'select',
        'attribute_orderby' => 'menu_order',
        'attribute_public'  => 0
    );

    $attribute_name = $wpdb->get_var( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = 'brands'" );

    if (!$attribute_name ) {
        $wpdb->insert($wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute);
        do_action('woocommerce_attribute_added', $wpdb->insert_id, $attribute);
        flush_rewrite_rules();
        delete_transient('wc_attribute_taxonomies');
    }

}

add_action( 'init', 'register_bip_attribute_brands' );
