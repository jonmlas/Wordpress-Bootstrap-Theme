<?php

/**
 * Register a Casino Reviews post type.
 *
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 */

 $labels = array(
	'name'               => __( 'Reviews', 'zg' ),
	'singular_name'      => __( 'Review', 'zg' ),
	'menu_name'          => __( 'Reviews', 'zg' ),
	'name_admin_bar'     => __( 'Review', 'zg' ),
	'add_new'            => __( 'Add New', 'zg' ),
	'add_new_item'       => __( 'Add New Review', 'zg' ),
	'new_item'           => __( 'New Review', 'zg' ),
	'edit_item'          => __( 'Edit Review', 'zg' ),
	'view_item'          => __( 'View Review', 'zg' ),
	'all_items'          => __( 'All Reviews', 'zg' ),
	'search_items'       => __( 'Search Reviews', 'zg' ),
	'parent_item_colon'  => __( 'Parent Reviews:', 'zg' ),
	'not_found'          => __( 'No review found.', 'zg' ),
	'not_found_in_trash' => __( 'No review found in Trash.', 'zg' )
);

$args = array(
	'labels'             => $labels,
	'public'             => true,
	'publicly_queryable' => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'query_var'          => true,
	'rewrite'            => array( 'slug' => 'sites' ),
	'capability_type'    => 'post',
	'has_archive'        => false,
	'hierarchical'       => false,
	'menu_position'      => 4,
	'menu_icon'			 => 'dashicons-tablet',
	'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions', 'page-attributes' )
);

register_post_type( 'review', $args );

/**
 * Register a genre taxonomy.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
 */

$labels = array(
	'name'              => __( 'Review Categories', 'zg' ),
	'singular_name'     => __( 'Review Category', 'zg' ),
	'search_items'      => __( 'Search Categories', 'zg' ),
	'all_items'         => __( 'All Categories', 'zg' ),
	'parent_item'       => __( 'Parent Category', 'zg' ),
	'parent_item_colon' => __( 'Parent Category', 'zg' ) . ':',
	'edit_item'         => __( 'Edit Category', 'zg' ),
	'update_item'       => __( 'Update Category', 'zg' ),
	'add_new_item'      => __( 'Add New Category', 'zg' ),
	'new_item_name'     => __( 'New Category Name', 'zg' ),
	'menu_name'         => __( 'Category', 'zg' ),
);

$args = array(
	'hierarchical'      => true,
	'labels'            => $labels,
	'show_ui'           => true,
	'show_admin_column' => true,
	'query_var'         => true,
	'rewrite'           => array( 'slug' => 'review-category' ),
);

register_taxonomy( 'review-category', array( 'review' ), $args );