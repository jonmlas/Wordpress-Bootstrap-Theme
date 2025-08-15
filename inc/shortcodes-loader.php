<?php
/**
 * Shortcodes Loader
 */

// Load all shortcode files automatically
foreach (glob(__DIR__ . '/shortcodes/*.php') as $shortcode_file) {
    include_once $shortcode_file;
}

// Collect shortcode configs from all shortcodes
function zg_get_all_shortcodes() {
    global $zg_shortcodes_registry;
    return $zg_shortcodes_registry ?? [];
}

// Enqueue JS for Insert Shortcode button
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style(
            'jquery-ui-datepicker-css',
            'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css'
        );
        wp_enqueue_script(
            'insert-shortcode',
            get_template_directory_uri() . '/assets/js/insert-shortcode.js',
            ['jquery'],
            '1.0',
            true
        );
        wp_localize_script('insert-shortcode', 'shortcodes', zg_get_all_shortcodes());
    }
});

