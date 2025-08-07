<?php

/**
 * Init Widget areas in Sidebar.
 *
 * @since v1.0
 *
 * @return void
 */
function zg_widgets_init() {
	register_sidebar(
		array(
			'name'          => 'Sidebar',
			'id'            => 'sidebar',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);

	register_sidebar(
		array(
			'name'          => 'Header Navigation',
			'id'            => 'header_navigation',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);

	register_sidebar(
		array(
			'name'          => 'Footer 1',
			'id'            => 'footer_1',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);
	
	register_sidebar(
		array(
			'name'          => 'Footer 2',
			'id'            => 'footer_2',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);
	
	register_sidebar(
		array(
			'name'          => 'Footer 3',
			'id'            => 'footer_3',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);
	
	register_sidebar(
		array(
			'name'          => 'Footer 4',
			'id'            => 'footer_4',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);
	
	register_sidebar(
		array(
			'name'          => 'Footer 5',
			'id'            => 'footer_5',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);
	
	register_sidebar(
		array(
			'name'          => 'Footer 6',
			'id'            => 'footer_6',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);
	
	register_sidebar(
		array(
			'name'          => 'Footer 7',
			'id'            => 'footer_7',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<div class="widget-title">',
			'after_title'   => '</div>',
		)
	);
}
add_action( 'widgets_init', 'zg_widgets_init' );