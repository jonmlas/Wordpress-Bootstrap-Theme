<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

function zg_pros_cons_shortcode($atts = []) {
    $atts = shortcode_atts([
        // Add attributes here if needed later
    ], $atts, 'pros-cons');

    $review = lastimosa_get_post_option(get_the_ID(), 'review_options');
    if (empty($review['pros']) && empty($review['cons'])) {
        return '';
    }

    ob_start();
    ?>
    <div class="pros-cons clearfix box">
        <?php if (!empty($review['pros'])) : ?>
            <div class="pros card">
                <h3><i class="fa fa-thumbs-up"></i> <?php echo esc_html__('Pros', 'zg'); ?></h3>
                <div class="card-body"><?php echo wp_kses_post($review['pros']); ?></div>
            </div>
        <?php endif; ?>
        <?php if (!empty($review['cons'])) : ?>
            <div class="cons card">
                <h3><i class="fa fa-thumbs-down"></i> <?php echo esc_html__('Cons', 'zg'); ?></h3>
                <div class="card-body"><?php echo wp_kses_post($review['cons']); ?></div>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pros-cons', 'zg_pros_cons_shortcode');

// Register this shortcode config for the toolbar
$zg_shortcodes_registry[] = [
    'tag' => 'pros-cons',
    'name' => 'Pros & Cons',
    'attributes' => [
        // Add config attributes here if needed in the future
    ],
];
