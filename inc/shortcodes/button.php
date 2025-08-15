<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

// Register shortcode PHP logic
function zg_bootstrap_button_shortcode($atts) {
    $atts = shortcode_atts([
        'text'     => 'Click me',
        'color'    => '',
        'size'     => 'sm',
        'outline'  => 'false',
        'tags'     => '',
        'disabled' => 'false',
        'width'    => '',
        'toggle'   => '',
        'id'       => '',
        'class'    => '',
        'url'      => '',
        'target'   => '',
    ], $atts, 'button');

    $button_class = 'btn';
    $button_class .= ($atts['outline'] === 'true' && $atts['color']) 
        ? ' btn-outline-' . $atts['color'] 
        : ($atts['color'] ? ' btn-' . $atts['color'] : '');
    $button_class .= !empty($atts['size']) ? ' btn-' . $atts['size'] : '';
    $button_class .= !empty($atts['class']) ? ' ' . $atts['class'] : '';

    $disabled_attr = $atts['disabled'] === 'true' ? ' disabled' : '';
    $width_style = !empty($atts['width']) ? ' style="width:' . esc_attr($atts['width']) . ';"' : '';
    $id_attr = !empty($atts['id']) ? ' id="' . esc_attr($atts['id']) . '"' : '';
    $target_attr = !empty($atts['target']) ? ' target="' . esc_attr($atts['target']) . '"' : '';

    $tag = !empty($atts['url']) ? 'a' : 'button';

    // Build button opening tag with attributes
    $button_html = '<' . $tag;
    if ($tag === 'a') {
        $button_html .= ' href="' . esc_url($atts['url']) . '"';
    }
    $button_html .= ' class="' . esc_attr($button_class) . '"' . $id_attr . $disabled_attr . $width_style . $target_attr;

    if (!empty($atts['tags'])) {
        $button_html .= ' ' . $atts['tags'];
    }

    if (!empty($atts['toggle'])) {
        $button_html .= ' ' . $atts['toggle'];
    }

    $button_html .= '>' . esc_html($atts['text']) . '</' . $tag . '>';

    return $button_html;
}
add_shortcode('button', 'zg_bootstrap_button_shortcode');

// Register this shortcode config for the toolbar
$zg_shortcodes_registry[] = [
    'tag' => 'button',
    'name' => 'Button',
    'self_closing' => true,
    'priority' => 40,
    'attributes' => [
        ['type' => 'text', 'label' => 'Text', 'attr' => 'text', 'default' => 'Click me'],
        ['type' => 'text', 'label' => 'Color', 'attr' => 'color', 'default' => '', 'description' => 'primary, secondary, success, etc.'],
        ['type' => 'text', 'label' => 'Size', 'attr' => 'size', 'default' => 'sm', 'description' => 'sm, lg'],
        ['type' => 'select', 'label' => 'Outline', 'attr' => 'outline', 'options' => ['false' => 'False', 'true' => 'True'], 'default' => 'false'],
        ['type' => 'text', 'label' => 'Additional Tags', 'attr' => 'tags', 'default' => ''],
        ['type' => 'select', 'label' => 'Disabled', 'attr' => 'disabled', 'options' => ['false' => 'False', 'true' => 'True'], 'default' => 'false'],
        ['type' => 'text', 'label' => 'Width', 'attr' => 'width', 'default' => '', 'description' => 'e.g., 100px, 50%'],
        ['type' => 'text', 'label' => 'Toggle', 'attr' => 'toggle', 'default' => '', 'description' => 'e.g., data-bs-toggle="button"'],
        ['type' => 'text', 'label' => 'ID', 'attr' => 'id', 'default' => ''],
        ['type' => 'text', 'label' => 'Class', 'attr' => 'class', 'default' => ''],
        ['type' => 'text', 'label' => 'URL', 'attr' => 'url', 'default' => ''],
        ['type' => 'text', 'label' => 'Target', 'attr' => 'target', 'default' => '', 'description' => '_blank, _self, etc.'],
    ],
];
