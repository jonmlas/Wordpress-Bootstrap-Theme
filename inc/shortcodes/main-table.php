<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

/**
 * Lighten a hex color by a given percentage.
 *
 * @param string $hex  The hex color (e.g., "#7fb0bf").
 * @param float  $percent  The percentage to lighten (e.g., 0.3 for 30% lighter).
 * @return string  The lightened hex color.
 */
function lighten_hex_color($hex, $percent = 0.3) {
    $hex = str_replace('#', '', $hex);

    // Convert short hex to full hex if needed
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0] . $hex[1].$hex[1] . $hex[2].$hex[2];
    }

    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Lighten each channel
    $r = min(255, intval($r + (255 - $r) * $percent));
    $g = min(255, intval($g + (255 - $g) * $percent));
    $b = min(255, intval($b + (255 - $b) * $percent));

    // Return hex format
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

/**
 * Main table shortcode function.
 */
function zg_main_table_shortcode($atts = []) {
    $atts = shortcode_atts([
        'slugs'  => '',
        'cat'    => '',
        'count'  => '', 
        'ctr'    => '',
        'offset' => '',
    ], $atts, 'main-table');

    $atts['count']   = $atts['count'] ?: -1; // Default to -1 if not set
    $atts['ctr']     = $atts['ctr'] ?: '1'; // Default to '1' if not set
    $atts['offset']  = $atts['offset'] ?: '0'; // Default to '0' if not set

    // Sanitize and parse attributes
    $slugs  = !empty($atts['slugs']) ? array_map('trim', explode(',', $atts['slugs'])) : [];
    $count  = (int) $atts['count'];
    $ctr    = absint($atts['ctr']);
    $offset = absint($atts['offset']);

    $args = [
        'post_status'        => 'publish',
        'posts_per_page'     => $count,
        'post_type'          => 'review',
        'ignore_sticky_posts'=> true,
        'offset'             => $offset,
    ];

    if (!empty($slugs)) {
        $args = array_merge($args, [
            'post_name__in' => $slugs,
            'orderby'       => 'post_name__in',
        ]);
    }

    if (!empty($atts['cat'])) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'review-category',
                'field'    => 'slug',
                'terms'    => $atts['cat'],
            ],
        ];
    }

    $wp_query = new WP_Query($args);

    $output = '<div class="reviews-table">';
    $output .= '<span class="affiliate-disclosure"><a href="' . esc_url(get_site_url() . '/affiliate-disclosure/') . '"><i class="fas fa-bell"></i> ' . __(' Affiliate Disclosure', 'zg') . '</a></span>';

    while ($wp_query->have_posts()) {
        $wp_query->the_post();
        $post_id = get_the_ID();

        // Carbon fields
        $rating                 = carbon_get_post_meta($post_id, 'rating');
        $affiliate_link         = carbon_get_post_meta($post_id, 'affiliate_link');
        $bonus                  = carbon_get_post_meta($post_id, 'bonus');
        $terms                  = carbon_get_post_meta($post_id, 'terms');
        $logo_bg_color          = carbon_get_post_meta($post_id, 'logo_bg_color');
        // Apply gradient background if color is set
        if ($logo_bg_color) {
            $lighter_color = lighten_hex_color($logo_bg_color, 0.3); // 30% lighter
            $logo_background_color = ' style="background: linear-gradient(to right, ' 
                . esc_attr($logo_bg_color) . ', ' 
                . esc_attr($lighter_color) . ');"';
        } else {
            $logo_background_color = '';
        }

        // UK badge HTML
        $uk_badge_html = '
            <div class="uk-badge mt-auto">
                <img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/united-kingdom.svg') . '" alt="UK Flag" width="24" height="16" />
                <span>' . __('UK Players Accepted', 'zg') . '</span>
            </div>';

        // Thumbnail or title fallback
        if (has_post_thumbnail()) {
            $review_thumbnail = sprintf(
                '<div class="logo-wrap hvr-grow" %s>
                    <span class="counter">%s</span>
                    <a href="%s" target="_blank" rel="noopener noreferrer">
                        %s
                    </a>
                    %s
                </div>',
                $logo_background_color,
                esc_html($ctr),
                esc_url($affiliate_link),
                get_the_post_thumbnail($post_id, 'main-table-logo', [
                    'alt'   => get_the_title(),
                    'class' => 'logo img-fluid hvr-grow box-gradient',
                ]),
                $uk_badge_html
            );
        } else {
            $review_thumbnail = sprintf(
                '<div class="logo-wrap"%s>
                    <span class="counter">%s</span>
                    <p class="entry-title"><a href="%s">%s</a></p>
                    %s
                </div>',
                $logo_background_color,
                esc_html($ctr),
                esc_url(get_the_permalink()),
                esc_html(get_the_title()),
                $uk_badge_html
            );
        }

        $bonus_html = '';
        if ($bonus) {
            $bonus_html  = '<div class="bonus-section"><small>' . __('Welcome Package', 'zg') . '</small><div class="bonus-text">' . esc_html($bonus) . '</div></div>';
        }

        $terms_html = $terms ? wp_kses_post($terms) : __('UK players accepted, terms apply, 18+', 'zg');

        $output .= '
        <div class="table-row review-card pb-0">
            <div class="info review-main row">
                <div class="col-md-3 logo-col">' 
                    . $review_thumbnail . '
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-8 bonus-col">
                            <div class="title mb-3">' . esc_html(get_the_title()) . '</div>' .
                            $bonus_html . '
                        </div>
                        <div class="col-md-4 action-col">
                            <div class="play-now">
                                <a class="btn btn-primary" href="' . esc_url($affiliate_link) . '" target="_blank" rel="noopener noreferrer">' . __('Visit Site', 'zg') . '</a>
                            </div>
                            <div class="payment-methods d-flex justify-content-center align-items-center">
                                <img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/payment-logo-paypal.svg') . '" class="aligncenter" alt="payment-logo-paypal" width="30" height="24"> 
                                <img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/payment-logo-neosurf.svg') . '" class="aligncenter" alt="payment-logo-paypal" width="30" height="24">
                                <img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/payment-logo-mastercard.svg') . '" class="aligncenter" alt="payment-logo-paypal" width="30" height="24">
                                <img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/payment-logo-visa.svg') . '" class="aligncenter" alt="payment-logo-paypal" width="30" height="24">
                                <img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/payment-logo-trustly.svg') . '" class="aligncenter" alt="payment-logo-paypal" width="30" height="24">
                                <img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/payment-logo-skrill.svg') . '" class="aligncenter" alt="payment-logo-paypal" width="30" height="24">
                            </div>
                            <div class="mt-2 text-center">
                                <a href="' . esc_url(get_the_permalink()) . '" class="read-review">' . __('Read Review', 'zg') . '</a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr />
                            <div class="terms-conditions">' . $terms_html . '</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        $ctr++;
    }

    $output .= '</div>';
    wp_reset_postdata();

    return $output;
}
add_shortcode('main-table', 'zg_main_table_shortcode');

// Register this shortcode config for the toolbar
$zg_shortcodes_registry[] = [
    'tag'        => 'main-table',
    'name'       => 'Main Table',
    'self_closing' => true,
    'attributes' => [
        [
            'type'        => 'text',
            'label'       => 'Slugs',
            'attr'        => 'slugs',
            'default'     => '',
            'description' => 'Comma separated post slugs',
        ],
        [
            'type'        => 'text',
            'label'       => 'Category',
            'attr'        => 'cat',
            'default'     => '',
            'description' => 'Review category slug',
        ],
        [
            'type'        => 'text',
            'label'       => 'Count',
            'attr'        => 'count',
            'default'     => '',
        ],
        [
            'type'        => 'text',
            'label'       => 'Counter Start',
            'attr'        => 'ctr',
            'description' => 'Starting number for the counter',
            'default'     => '',
        ],
        [
            'type'        => 'text',
            'label'       => 'Offset',
            'attr'        => 'offset',
            'default'     => '',
        ],
    ],
];
