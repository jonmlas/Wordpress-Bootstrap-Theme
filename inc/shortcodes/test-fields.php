<?php
// Ensure registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

// Shortcode callback
function zg_sample_shortcode($atts, $content = null) {
    $atts = shortcode_atts([
        'title'         => 'Default Title',
        'description'   => 'Default description',
        'color'         => '#ff0000',
        'date'          => '',
        'datetime'      => '',
        'time'          => '',
        'checkbox'      => 'no',
        'radio'         => 'option1',
        'select'        => 'option1',
        'repeater'      => '',
    ], $atts, 'sample_shortcode');

    ob_start();
    ?>
    <div style="border:1px solid #ccc;padding:10px;margin:10px 0;">
        <h3 style="color: <?php echo esc_attr($atts['color']); ?>;"><?php echo esc_html($atts['title']); ?></h3>
        <p><?php echo esc_html($atts['description']); ?></p>
        <p><strong>Date:</strong> <?php echo esc_html($atts['date']); ?></p>
        <p><strong>DateTime:</strong> <?php echo esc_html($atts['datetime']); ?></p>
        <p><strong>Time:</strong> <?php echo esc_html($atts['time']); ?></p>
        <p><strong>Checkbox:</strong> <?php echo esc_html($atts['checkbox']); ?></p>
        <p><strong>Radio:</strong> <?php echo esc_html($atts['radio']); ?></p>
        <p><strong>Select:</strong> <?php echo esc_html($atts['select']); ?></p>
        <?php if (!empty($atts['repeater'])): ?>
            <div>
                <strong>Repeater Items:</strong>
                <ul>
                    <?php foreach ((array)$atts['repeater'] as $item): ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div><?php echo do_shortcode($content); ?></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('sample_shortcode', 'zg_sample_shortcode');

// Register in toolbar
$zg_shortcodes_registry[] = [
    'tag' => 'sample_shortcode',
    'name' => 'Sample Shortcode',
    'attributes' => [
        [
            'type'    => 'text',
            'label'   => 'Title',
            'attr'    => 'title',
            'default' => 'My Sample Title'
        ],
        [
            'type'    => 'textarea',
            'label'   => 'Description',
            'attr'    => 'description',
            'default' => 'This is a sample description.'
        ],
        [
            'type'    => 'color',
            'label'   => 'Color',
            'attr'    => 'color',
            'default' => '#ff0000'
        ],
        [
            'type'    => 'date',
            'label'   => 'Date',
            'attr'    => 'date',
            'default' => ''
        ],
        [
            'type'    => 'time',
            'label'   => 'Time',
            'attr'    => 'time',
            'default' => ''
        ],
        [
            'type'    => 'checkbox',
            'label'   => 'Enable Option',
            'attr'    => 'checkbox',
            'default' => 'no'
        ],
        [
            'type'    => 'checkbox',
            'label'   => 'Enable Option 2',
            'attr'    => 'checkbox',
            'default' => 'no'
        ],
        [
            'type'    => 'radio',
            'label'   => 'Choose Option',
            'attr'    => 'radio',
            'options' => [
                'option1' => 'Option 1',
                'option2' => 'Option 2',
                'option3' => 'Option 3'
            ],
            'default' => 'option1'
        ],
        [
            'type'    => 'select',
            'label'   => 'Select Option',
            'attr'    => 'select',
            'options' => [
                'option1' => 'Option 1',
                'option2' => 'Option 2',
                'option3' => 'Option 3'
            ],
            'default' => 'option1'
        ],
        [
            'type'    => 'repeater',
            'label'   => 'Repeater Items',
            'attr'    => 'repeater',
            'fields'  => [
                [
                    'type'    => 'text',
                    'label'   => 'Item Text',
                    'attr'    => 'item_text',
                    'default' => ''
                ]
            ]
        ]
    ]
];
