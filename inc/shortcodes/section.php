<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

// Register shortcode PHP logic
function zg_section_shortcode($atts, $content = null) {
    $atts = shortcode_atts([
        'title'      => 'Section',
        'id'         => '',
        'class'      => '',
        'container'  => 'false', // boolean-ish string
        'gutter'     => 'gx-1',
        'row_class'  => '',
    ], $atts, 'section');

    $id_attr       = $atts['id'] ? ' id="' . esc_attr($atts['id']) . '"' : '';
    $class_attr    = $atts['class'] ? ' ' . esc_attr($atts['class']) : '';
    $gutter_class  = $atts['gutter'] ? ' ' . esc_attr($atts['gutter']) : '';
    $row_class     = $atts['row_class'] ? ' ' . esc_attr($atts['row_class']) : '';

    $container_enabled = filter_var($atts['container'], FILTER_VALIDATE_BOOLEAN);

    ob_start();
    ?>
    <section<?php echo $id_attr; ?> class="<?php echo ltrim($class_attr); ?>">
        <?php if ($container_enabled): ?>
            <div class="container">
                <div class="row<?php echo $row_class . $gutter_class; ?>">
                    <?php echo do_shortcode($content); ?>
                </div>
            </div>
        <?php else: ?>
            <?php echo do_shortcode($content); ?>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('section', 'zg_section_shortcode');

// Register this shortcode config for the toolbar
$zg_shortcodes_registry[] = [
    'tag' => 'section',
    'name' => 'Section',
    'priority' => 10,
    'attributes' => [
        [
            'type' => 'text',
            'label' => 'Title',
            'attr' => 'title',
            'default' => 'Section',
        ],
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
            'type' => 'select',
            'label' => 'Container',
            'attr' => 'container',
            'options' => [
                'false' => 'False',
                'true' => 'True',
            ],
            'default' => 'false',
        ],
        [
            'type' => 'text',
            'label' => 'Gutter Class',
            'attr' => 'gutter',
            'default' => 'gx-1',
        ],
        [
            'type' => 'text',
            'label' => 'Row Class',
            'attr' => 'row_class',
            'default' => '',
        ],
    ],
];
