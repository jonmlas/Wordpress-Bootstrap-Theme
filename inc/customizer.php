<?php
function my_bootstrap_theme_customize_register($wp_customize) {
    $wp_customize->add_section('footer_section', array(
        'title'    => __('Footer', 'zg-theme'),
        'priority' => 120,
    ));

    $wp_customize->add_setting('footer_text', array(
        'default'           => 'My Bootstrap Theme. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('footer_text', array(
        'label'   => __('Footer Text', 'zg-theme'),
        'section' => 'footer_section',
        'type'    => 'text',
    ));
}
add_action('customize_register', 'zg_theme_customize_register');