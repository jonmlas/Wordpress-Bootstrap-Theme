<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

// Register shortcode PHP logic
function zg_row_shortcode($atts, $content = null) {
    $atts = shortcode_atts([
        'id'     => '',
        'class'  => '',
        'gutter' => 'gx-1', // Default gutter class
        'align'  => '',     // e.g. "center start"
    ], $atts, 'row');

    $id_attr = $atts['id'] ? ' id="' . esc_attr($atts['id']) . '"' : '';

    // Handle alignment classes
    $align_classes = [];
    if (!empty($atts['align'])) {
        $align_parts = preg_split('/\s+/', trim($atts['align']));
        foreach ($align_parts as $part) {
            $part = strtolower($part);
            if (in_array($part, ['start', 'center', 'end'])) {
                $align_classes[] = 'align-items-' . $part;
            }
            if (in_array($part, ['between', 'around', 'evenly', 'start', 'center', 'end'])) {
                $align_classes[] = 'justify-content-' . $part;
            }
        }
    }

    $class_attr = trim('row ' . $atts['gutter'] . ' ' . $atts['class'] . ' ' . implode(' ', $align_classes));

    return '<div' . $id_attr . ' class="' . esc_attr($class_attr) . '">' 
        . do_shortcode(zg_clean_shortcode_content($content)) 
        . '</div>';
}
add_shortcode('row', 'zg_row_shortcode');

// Register this shortcode config for the toolbar
$zg_shortcodes_registry[] = [
    'tag' => 'row',
    'name' => 'Row',
    'priority' => 20,
    'attributes' => [
        [
            'type' => 'text',
            'label' => 'ID',
            'attr' => 'id',
            'default' => '',
        ],
        [
            'type' => 'text',
            'label' => 'Class',
            'attr' => 'class',
            'default' => '',
        ],
        [
            'type' => 'text',
            'label' => 'Gutter Class',
            'attr' => 'gutter',
            'default' => 'gx-1',
        ],
        [
            'type' => 'text',
            'label' => 'Align',
            'attr' => 'align',
            'default' => '',
            'description' => 'Alignment options: start, center, end, between, around, evenly',
        ],
    ],
];
