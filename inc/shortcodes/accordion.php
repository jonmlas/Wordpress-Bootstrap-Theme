<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

// Store temporary accordion items while processing
global $zg_accordion_items;
$zg_accordion_items = [];

// Child shortcode: accordion_item
function zg_bootstrap_accordion_item($atts, $content = null) {
    global $zg_accordion_items;

    $atts = shortcode_atts([
        'title' => '',
    ], $atts, 'accordion_item');

    $zg_accordion_items[] = [
        'title' => $atts['title'],
        'content' => do_shortcode($content),
    ];

    return ''; // Don't output immediately
}
add_shortcode('accordion_item', 'zg_bootstrap_accordion_item');

// Helper function for Roman numerals
function roman_numeral($num) {
    $map = [
        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
    ];
    $return = '';
    foreach ($map as $roman => $int) {
        while ($num >= $int) {
            $return .= $roman;
            $num -= $int;
        }
    }
    return $return;
}

// Parent shortcode: accordion
function zg_bootstrap_accordion($atts, $content = null) {
    global $zg_accordion_items;
    $zg_accordion_items = []; // Reset items before processing

    $atts = shortcode_atts([
        'id'          => 'accordion-' . rand(1000, 9999),
        'class'       => '',
        'behavior'    => '', // single | all | first | none
        'flush'       => '',     // yes | no
        'header_tag'  => '',     // h2, h3, h4 etc.
        'open_items'  => '',       // comma separated indexes to open by default (0-based)
        'icon_style'  => '',// chevron | plusminus | caret | none
        'list_style'  => '', // none | decimal | decimal-leading-zero | disc | circle | square | lower-alpha | upper-alpha | lower-roman | upper-roman
        'remove_period'  => '', // default stays "no"
    ], $atts, 'accordion');

    $atts['behavior']   = $atts['behavior'] ?: 'single';
    $atts['flush']      = $atts['flush'] ?: 'no';
    $atts['header_tag'] = $atts['header_tag'] ?: 'h3';
    $atts['icon_style'] = $atts['icon_style'] ?: 'chevron';
    $atts['list_style'] = $atts['list_style'] ?: 'none';
    $atts['remove_period'] = $atts['remove_period'] ?: 'no';

    // print_array($atts);

    // Normalize checkbox-style input (true/false) to yes/no
    if (isset($atts['remove_period'])) {
        if ($atts['remove_period'] === true || strtolower($atts['remove_period']) === 'true') {
            $atts['remove_period'] = 'yes';
        } elseif ($atts['remove_period'] === false || strtolower($atts['remove_period']) === 'false') {
            $atts['remove_period'] = 'no';
        }
    } else {
        $atts['remove_period'] = 'no'; // default if not set
    }

    do_shortcode($content);

    if (empty($zg_accordion_items)) {
        return '';
    }

    $open_indexes = array_map('intval', array_filter(array_map('trim', explode(',', $atts['open_items']))));

    $accordion_classes = 'accordion';
    if (strtolower($atts['flush']) === 'yes') {
        $accordion_classes .= ' accordion-flush';
    }
    if (!empty($atts['class'])) {
        $accordion_classes .= ' ' . esc_attr($atts['class']);
    }

    $use_data_parent = in_array($atts['behavior'], ['single', 'first']);

    $icon_style = strtolower($atts['icon_style']);
    $need_plusminus_css = ($icon_style === 'plusminus');
    $need_caret_css = ($icon_style === 'caret');
    $need_pluscross_css = ($icon_style === 'pluscross');

    // Validate list style
    $list_styles = ['none','decimal','decimal-leading-zero','disc','circle','square','lower-alpha','upper-alpha','lower-roman','upper-roman'];
    $list_style = in_array($atts['list_style'], $list_styles) ? $atts['list_style'] : 'none';

    ob_start();
    ?>
    <div class="<?php echo $accordion_classes; ?>" id="<?php echo esc_attr($atts['id']); ?>">
        <?php foreach ($zg_accordion_items as $i => $item):
            $collapse_id = $atts['id'] . '-collapse-' . $i;
            $heading_id = $atts['id'] . '-heading-' . $i;

            $is_open = in_array($i, $open_indexes, true);
            if (!$is_open) {
                switch ($atts['behavior']) {
                    case 'all': $is_open = true; break;
                    case 'first': $is_open = ($i === 0); break;
                    case 'single':
                    case 'none':
                    default: $is_open = false; break;
                }
            }

            $btn_class = $is_open ? '' : 'collapsed';
            if ($need_plusminus_css) {
                $btn_class .= ' plusminus';
            } elseif ($need_caret_css) {
                $btn_class .= ' caret-icon';
            } elseif ($need_pluscross_css) {
                $btn_class .= ' pluscross';
            }


            $collapse_class = $is_open ? 'accordion-collapse collapse show' : 'accordion-collapse collapse';
            $header_tag = in_array(strtolower($atts['header_tag']), ['h1','h2','h3','h4','h5','h6']) ? strtolower($atts['header_tag']) : 'h3';

            $icon_html = '';
            if ($icon_style === 'plusminus') {
                $icon_html = '<span class="accordion-icon ms-auto"></span>';
            } elseif ($icon_style === 'caret') {
                $icon_html = '<span class="caret-icon-span ms-auto"></span>';
            } elseif ($icon_style === 'pluscross') {
                $icon_html = '<span class="pluscross-icon ms-auto"></span>';
}
        ?>
        <div class="accordion-item">
            <<?php echo $header_tag; ?> class="accordion-header" id="<?php echo esc_attr($heading_id); ?>">
                <button class="accordion-button <?php echo esc_attr(trim($btn_class)); ?>" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#<?php echo esc_attr($collapse_id); ?>"
                    aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                    aria-controls="<?php echo esc_attr($collapse_id); ?>">

                    <?php if ($list_style !== 'none'): ?>
                        <span class="me-2">
                        <?php
                        $marker = '';
                        switch ($list_style) {
                            case 'decimal': $marker = $i+1; break;
                            case 'decimal-leading-zero': $marker = str_pad($i+1,2,'0',STR_PAD_LEFT); break;
                            case 'lower-alpha': $marker = chr(97 + $i % 26); break;
                            case 'upper-alpha': $marker = chr(65 + $i % 26); break;
                            case 'lower-roman': $marker = strtolower(roman_numeral($i+1)); break;
                            case 'upper-roman': $marker = strtoupper(roman_numeral($i+1)); break;
                            case 'disc': $marker = '•'; break;
                            case 'circle': $marker = '○'; break;
                            case 'square': $marker = '■'; break;
                        }

                        // Add period for numeric/alpha markers if enabled
                        if (strtolower($atts['remove_period']) === 'no' && in_array($list_style, [
                            'decimal', 'decimal-leading-zero', 'lower-alpha', 'upper-alpha', 'lower-roman', 'upper-roman'
                        ])) {
                            $marker .= '.';
                        }

                        echo $marker;
                        ?>
                        </span>
                    <?php endif; ?>

                    <?php echo esc_html($item['title']); ?>
                    <?php echo $icon_html; ?>
                </button>
            </<?php echo $header_tag; ?>>

            <div id="<?php echo esc_attr($collapse_id); ?>" class="<?php echo esc_attr($collapse_class); ?>"
                <?php if ($use_data_parent) : ?>data-bs-parent="#<?php echo esc_attr($atts['id']); ?>"<?php endif; ?>>
                <div class="accordion-body">
                    <?php echo wpautop(wp_kses_post($item['content'])); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($need_plusminus_css): ?>
    <style>
        .accordion-button.plusminus { position: relative; }
        .accordion-button.plusminus .accordion-icon { width: 1em; height: 1em; display: inline-block; position: relative; margin-left:auto; flex-shrink:0; }
        .accordion-button.plusminus::after { display: none; }
        .accordion-button.plusminus .accordion-icon::before,
        .accordion-button.plusminus .accordion-icon::after { content: ''; position: absolute; background-color: currentColor; transition: transform 0.2s ease; }
        .accordion-button.plusminus .accordion-icon::before { top:50%; left:0; right:0; height:2px; transform:translateY(-50%); }
        .accordion-button.plusminus .accordion-icon::after { top:0; bottom:0; left:50%; width:2px; transform:translateX(-50%); }
        .accordion-button.plusminus:not(.collapsed) .accordion-icon::after { transform:translateX(-50%) scaleY(0); opacity:0; }
    </style>
    <?php endif; ?>

    <?php if ($need_pluscross_css): ?>
    <style>
        .accordion-button.pluscross{position:relative;}
        .accordion-button.pluscross::after{display:none;}
        .accordion-button.pluscross .pluscross-icon{width:1em;height:1em;display:inline-block;position:relative;margin-left:auto;flex-shrink:0;}
        .accordion-button.pluscross .pluscross-icon::before,
        .accordion-button.pluscross .pluscross-icon::after{content:'';position:absolute;background-color:currentColor;top:50%;left:50%;width:2px;height:100%;transform:translate(-50%,-50%) rotate(0deg);transition:transform 0.2s ease;}
        .accordion-button.pluscross .pluscross-icon::after{transform:translate(-50%,-50%) rotate(90deg);}
        .accordion-button.pluscross:not(.collapsed) .pluscross-icon::before{transform:translate(-50%,-50%) rotate(45deg);}
        .accordion-button.pluscross:not(.collapsed) .pluscross-icon::after{transform:translate(-50%,-50%) rotate(-45deg);}
    </style>
    <?php endif; ?>

    <?php if ($need_caret_css): ?>
    <style>
        .accordion-button.caret-icon { position: relative; }
        .accordion-button.caret-icon::after { display: none; }
        .accordion-button.caret-icon .caret-icon-span { margin-left:auto; width:1em; height:1em; display:inline-block; position: relative; }
        .accordion-button.caret-icon .caret-icon-span::before { content: ''; display:inline-block; border-style: solid; border-width:0.25em 0.25em 0 0; border-color: currentColor; width:0.5em; height:0.5em; position:absolute; top:50%; left:50%; transform-origin:center; transform:translate(-50%,-50%) rotate(45deg); transition: transform 0.3s ease; }
        .accordion-button.caret-icon:not(.collapsed) .caret-icon-span::before { transform:translate(-50%,-50%) rotate(225deg); }
    </style>
    <?php endif; ?>

    <?php
    return ob_get_clean();
}
add_shortcode('accordion', 'zg_bootstrap_accordion');

// Toolbar registration for your repeater UI
$zg_shortcodes_registry[] = [
    'tag' => 'accordion',
    'name' => 'Accordion',
    'attributes' => [
        [
            'type' => 'repeater',
            'label' => 'Accordion Items',
            'attr'  => 'items',
            'fields' => [
                ['type' => 'text', 'label' => 'Title', 'attr' => 'title', 'default' => ''],
                ['type' => 'textarea', 'label' => 'Content', 'attr' => 'content', 'default' => '']
            ]
        ],
        [
            'type' => 'select',
            'label' => 'Behavior',
            'attr'  => 'behavior',
            'tooltip' => [
                'text' => 'Controls how accordion items open/close behavior.',
                'link' => 'https://getbootstrap.com/docs/5.3/components/accordion/#example'
            ],
            'options' => [
                ['value' => '', 'label' => 'Auto Close Others (One at a Time)'],
                ['value' => 'all', 'label' => 'Open All by Default'],
                ['value' => 'first', 'label' => 'Open First by Default'],
                ['value' => 'none', 'label' => 'All Closed by Default'],
            ],
            'default' => ''
        ],
        [
            'type' => 'select',
            'label' => 'Flush Style',
            'attr' => 'flush',
            'tooltip' => [
                'text' => 'Remove default background, borders, and rounded corners to render accordions edge-to-edge with parent container.',
                'link' => 'https://getbootstrap.com/docs/5.3/components/accordion/#flush'
            ],
            'options' => [
                ['value' => '', 'label' => 'No'],
                ['value' => 'yes', 'label' => 'Yes'],
            ],
            'default' => ''
        ],
        [
            'type' => 'select',
            'label' => 'Header Tag',
            'attr' => 'header_tag',
            'tooltip' => [
                'text' => 'HTML tag used for accordion header titles.',
                'link' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/Heading_Elements'
            ],
            'options' => [
                ['value' => '', 'label' => 'H3 (default)'],
                ['value' => 'h2', 'label' => 'H2'],
                ['value' => 'h4', 'label' => 'H4'],
                ['value' => 'h5', 'label' => 'H5'],
                ['value' => 'h6', 'label' => 'H6'],
            ],
            'default' => ''
        ],
        [
            'type' => 'select',
            'label' => 'Expand/Collapse Icon',
            'attr' => 'icon_style',
            'tooltip' => [
                'text' => 'Choose the style of the expand/collapse icon shown in accordion headers.',
                'link' => 'https://getbootstrap.com/docs/5.3/components/accordion/#example'
            ],
            'options' => [
                ['value' => '', 'label' => 'Chevron (default)'],
                ['value' => 'plusminus', 'label' => 'Plus / Minus'],
                ['value' => 'pluscross', 'label' => 'Plus / Cross'],
                ['value' => 'caret', 'label' => 'Solid Caret Up/Down'],
                ['value' => 'none', 'label' => 'No Icon'],
            ],
            'default' => ''
        ],
        [
            'type' => 'select',
            'label' => 'List Style',
            'attr' => 'list_style',
            'tooltip' => [
                'text' => 'Choose numbering or bullet style for the accordion items.',
                'link' => 'https://developer.mozilla.org/en-US/docs/Web/CSS/list-style-type'
            ],
            'options' => [
                ['value'=>'','label'=>'None'],
                ['value'=>'decimal','label'=>'Decimal (1,2,3)'],
                ['value'=>'decimal-leading-zero','label'=>'Decimal Leading Zero (01,02,03)'],
                ['value'=>'disc','label'=>'Disc (•)'],
                ['value'=>'circle','label'=>'Circle (○)'],
                ['value'=>'square','label'=>'Square (■)'],
                ['value'=>'lower-alpha','label'=>'Lower Alpha (a,b,c)'],
                ['value'=>'upper-alpha','label'=>'Upper Alpha (A,B,C)'],
                ['value'=>'lower-roman','label'=>'Lower Roman (i,ii,iii)'],
                ['value'=>'upper-roman','label'=>'Upper Roman (I,II,III)'],
            ],
            'default' => ''
        ],
        [
            'type' => 'checkbox',
            'label' => 'Remove period(.) after list marker',
            'attr'  => 'remove_period',
            'tooltip' => [
                'text' => 'Not applicable only to Disc, Circle and Square'
            ],
            'default' =>  '' // unchecked by default
        ],
        [
            'type' => 'text',
            'label' => 'Open Items (comma separated indexes, 0-based)',
            'attr' => 'open_items',
            'tooltip' => [
                'text' => 'Specify which accordion items open by default (indexes start at 0).',
                'link' => 'https://getbootstrap.com/docs/5.3/components/accordion/#example'
            ],
            'default' => ''
        ],
        [
            'type' => 'text',
            'label' => 'ID',
            'attr'  => 'id',
            'tooltip' => [
                'text' => 'Unique ID for the accordion container.',
                'link' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/id'
            ],
            'default' => ''
        ],
        [
            'type' => 'text',
            'label' => 'Class',
            'attr'  => 'class',
            'tooltip' => [
                'text' => 'Additional CSS classes for custom styling.',
                'link' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/class'
            ],
            'default' => ''
        ],
    ]
];