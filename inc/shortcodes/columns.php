<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

// Register shortcode PHP logic
function zg_column_shortcode($atts, $content = null) {
    $atts = shortcode_atts([
        'col'    => '12',  // e.g. "12 sm-6 md-4 lg-3"
        'offset' => '',    // e.g. "md-2 lg-1"
        'class'  => '',
    ], $atts, 'column');

    $classes = [];

    // Handle col classes
    $col_parts = preg_split('/\s+/', trim($atts['col']));
    foreach ($col_parts as $part) {
        if (preg_match('/^\d+$/', $part)) {
            $classes[] = 'col-' . $part;
        } elseif (preg_match('/^[a-z]+-\d+$/', $part)) {
            $classes[] = 'col-' . $part;
        }
    }

    // Handle offset classes
    if (!empty($atts['offset'])) {
        $offset_parts = preg_split('/\s+/', trim($atts['offset']));
        foreach ($offset_parts as $part) {
            if (preg_match('/^\d+$/', $part)) {
                $classes[] = 'offset-' . $part;
            } elseif (preg_match('/^[a-z]+-\d+$/', $part)) {
                $classes[] = 'offset-' . $part;
            }
        }
    }

    if (!empty($atts['class'])) {
        $classes[] = $atts['class'];
    }

    $class_attr = trim(implode(' ', $classes));

    return '<div class="' . esc_attr($class_attr) . '">' 
        . do_shortcode(zg_clean_shortcode_content($content)) 
        . '</div>';
}
add_shortcode('column', 'zg_column_shortcode');

// Register this shortcode config for the toolbar
$zg_shortcodes_registry[] = [
    'tag' => 'column',
    'name' => 'Column',
    'priority' => 30,
    'attributes' => [
        [
            'type' => 'text',
            'label' => 'Column Classes',
            'attr' => 'col',
            'default' => '12',
            'description' => 'Examples: "12", "sm-6 md-4 lg-3"',
        ],
        [
            'type' => 'text',
            'label' => 'Offset Classes',
            'attr' => 'offset',
            'default' => '',
            'description' => 'Examples: "md-2 lg-1"',
        ],
        [
            'type' => 'text',
            'label' => 'Additional Classes',
            'attr' => 'class',
            'default' => '',
        ],
    ],
];
