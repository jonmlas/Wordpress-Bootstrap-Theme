<?php

defined( 'ABSPATH' ) || exit;

/**
 * Implement Theme Customizer additions and adjustments.
 * https://codex.wordpress.org/Theme_Customization_API
 *
 * How do I "output" custom theme modification settings? https://developer.wordpress.org/reference/functions/get_theme_mod
 * echo get_theme_mod( 'copyright_info' );
 * or: echo get_theme_mod( 'copyright_info', 'Default (c) Copyright Info if nothing provided' );
 *
 * "sanitize_callback": https://codex.wordpress.org/Data_Validation
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 *
 * @return void
 */
function zg_customize( $wp_customize ) {
	/**
	 * Initialize sections
	 */
	$wp_customize->add_section(
		'theme_header_section',
		array(
			'title'    => __( 'Header', 'zg' ),
			'priority' => 1000,
		)
	);

	/**
	 * Section: Page Layout
	 */
	// Header Logo.
	$wp_customize->add_setting(
		'header_logo',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'header_logo',
			array(
				'label'       => __( 'Upload Header Logo', 'zg' ),
				'description' => __( 'Height: &gt;80px', 'zg' ),
				'section'     => 'theme_header_section',
			)
		)
	);

	// Predefined Navbar scheme.
	$wp_customize->add_setting(
		'navbar_scheme',
		array(
			'default'           => 'default',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'navbar_scheme',
		array(
			'type'    => 'radio',
			'label'   => __( 'Navbar Scheme', 'zg' ),
			'section' => 'theme_header_section',
			'choices' => array(
				'navbar-light bg-light'  => __( 'Default', 'zg' ),
				'navbar-dark bg-dark'    => __( 'Dark', 'zg' ),
				'navbar-dark bg-primary' => __( 'Primary', 'zg' ),
			),
		)
	);

	// Fixed Header?
	$wp_customize->add_setting(
		'navbar_position',
		array(
			'default'           => 'static',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'navbar_position',
		array(
			'type'    => 'radio',
			'label'   => __( 'Navbar', 'zg' ),
			'section' => 'theme_header_section',
			'choices' => array(
				'static'       => __( 'Static', 'zg' ),
				'fixed_top'    => __( 'Fixed to top', 'zg' ),
				'fixed_bottom' => __( 'Fixed to bottom', 'zg' ),
			),
		)
	);

	// Search?
	$wp_customize->add_setting(
		'search_enabled',
		array(
			'default'           => '1',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'search_enabled',
		array(
			'type'    => 'checkbox',
			'label'   => __( 'Show Searchfield?', 'zg' ),
			'section' => 'theme_header_section',
		)
	);
	
	
	 // Add Social Media Section
    $wp_customize->add_section('social_media_section', array(
        'title'    => __('Social Media', 'mytheme'),
        'priority' => 30,
    ));

    // Follow Us Heading Setting
    $wp_customize->add_setting('social_heading_text', array(
        'default'           => 'Follow Us',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('social_heading_text', array(
        'label'   => __('Social Media Heading', 'mytheme'),
        'section' => 'social_media_section',
        'type'    => 'text',
        'input_attrs' => array(
            'placeholder' => __('Follow Us', 'mytheme'),
        ),
    ));

    // Social Platforms
    $social_networks = array(
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin'  => 'LinkedIn',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    );

    foreach ($social_networks as $key => $label) {
        $wp_customize->add_setting("social_{$key}_url", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control("social_{$key}_url", array(
            'label'   => __("$label URL", 'mytheme'),
            'section' => 'social_media_section',
            'type'    => 'url',
        ));
    }
	
	
	
	
	// Add section for footer
    $wp_customize->add_section( 'zg_footer_section', array(
        'title'       => __( 'Footer Settings', 'mytheme' ),
        'priority'    => 160,
    ) );

    // Add setting for copyright text
    $wp_customize->add_setting( 'zg_footer_copyright', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    // Add control (input field)
    $wp_customize->add_control( 'zg_footer_copyright', array(
        'label'    => __( 'Footer Copyright Text', 'mytheme' ),
        'section'  => 'zg_footer_section',
        'type'     => 'text',
    ) );
	
 
	
	
	
}
add_action( 'customize_register', 'zg_customize' );

/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @return void
 */
function zg_customize_preview_js() {
	wp_enqueue_script( 'customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'jquery' ), null, true );
}
add_action( 'customize_preview_init', 'zg_customize_preview_js' );