<?php
add_action('init', 'ashuwp_post_type');
function ashuwp_post_type() {
  //product
	register_post_type( 'product',
		array(
			'labels' => array(
				'name' => 'Product',
				'singular_name' => 'Product',
				'add_new' => 'Add',
				'add_new_item' => 'Add',
				'edit_item' => 'Edit',
				'new_item' => 'New',
      ),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array('slug'=>'products'),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 7,
		'supports' => array('title','editor','thumbnail'),
		'map_meta_cap' => true
		)
	);
  
  //product category
  register_taxonomy(
		'products',
		array('product'),
		array(
			'hierarchical' => true,
			'labels' => array(
        'name' => 'Products',
        'singular_name' => 'Category',
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => 'Edit',
        'add_new_item' => 'Add',
        'new_item_name' => 'Category',
        'menu_name' => 'Category'
      ),
			'show_ui' => true,
			'query_var' => true
		)
	);
  
}