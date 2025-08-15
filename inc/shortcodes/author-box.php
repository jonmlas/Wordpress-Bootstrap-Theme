<?php
// Ensure the registry exists
global $zg_shortcodes_registry;
if (!isset($zg_shortcodes_registry)) {
    $zg_shortcodes_registry = [];
}

function zg_author_box_shortcode($atts = [], $content = null) {
    $atts = shortcode_atts([
        'username' => '',
        'reviewer' => '',
    ], $atts, 'author-box');

    global $post;

    $the_user = !empty($atts['username']) ? get_user_by('login', $atts['username']) : false;
    $the_reviewer = !empty($atts['reviewer']) ? get_user_by('login', $atts['reviewer']) : false;

    $default = '';

    $the_user_id = $the_user ? $the_user->ID : get_the_author_meta('ID');
    $the_reviewer_id = $the_reviewer ? $the_reviewer->ID : get_the_author_meta('ID');

    // User data
    $authorname   = get_the_author_meta('display_name', $the_user_id);
    $gravatar     = get_avatar(get_the_author_meta('user_email', $the_user_id), 60, $default, $authorname, ['class' => ['alignleft']]);
    $email        = get_the_author_meta('email', $the_user_id);
    $title        = get_the_author_meta('title', $the_user_id);
    $description  = wpautop(get_the_author_meta('full_bio', $the_user_id));
    $url          = get_the_author_meta('url', $the_user_id);
    $twitter      = get_the_author_meta('twitter', $the_user_id);
    $linkedin     = get_the_author_meta('linkedin', $the_user_id);

    $icon_email    = '<a href="mailto:' . esc_attr($email) . '" target="_blank"><i class="fa fa-envelope"></i></a>';
    $icon_url      = $url ? '<a href="' . esc_url($url) . '" target="_blank"><i class="fa fa-chrome" aria-hidden="true"></i></a> ' : '';
    $icon_twitter  = $twitter ? '<a href="' . esc_url($twitter) . '" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> ' : '';
    $icon_linkedin = $linkedin ? '<a href="' . esc_url($linkedin) . '" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>' : '';

    if ($the_reviewer) {
        // Reviewer data
        $reviewer_authorname  = get_the_author_meta('display_name', $the_reviewer_id);
        $reviewer_gravatar    = get_avatar(get_the_author_meta('user_email', $the_reviewer_id), 60, $default, $reviewer_authorname, ['class' => ['alignleft']]);
        $reviewer_email       = get_the_author_meta('email', $the_reviewer_id);
        $reviewer_title       = get_the_author_meta('title', $the_reviewer_id);
        $reviewer_description = wpautop(get_the_author_meta('full_bio', $the_reviewer_id));
        $reviewer_url         = get_the_author_meta('url', $the_reviewer_id);
        $reviewer_twitter     = get_the_author_meta('twitter', $the_reviewer_id);
        $reviewer_linkedin    = get_the_author_meta('linkedin', $the_reviewer_id);

        $reviewer_icon_email    = '<a href="mailto:' . esc_attr($reviewer_email) . '" target="_blank"><i class="fa fa-envelope"></i></a>';
        $reviewer_icon_url      = $reviewer_url ? '<a href="' . esc_url($reviewer_url) . '" target="_blank"><i class="fa fa-chrome" aria-hidden="true"></i></a> ' : '';
        $reviewer_icon_twitter  = $reviewer_twitter ? '<a href="' . esc_url($reviewer_twitter) . '" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> ' : '';
        $reviewer_icon_linkedin = $reviewer_linkedin ? '<a href="' . esc_url($reviewer_linkedin) . '" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>' : '';

        $output = '<div class="authors-box">';
        // User box
        $output .= '
        <div class="author-box">
            <div class="author-box-wrap">
                <div class="author-box-header">
                    <div class="info-top">'
                        . $gravatar .
                        '<div class="authorname">
                            <a class="vcard author" href="' . esc_url(get_author_posts_url($the_user_id)) . '">' . esc_html($authorname) . '</a>
                        </div>
                        <div class="title text-uppercase">' . esc_html($title) . '</div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="description">
                    <div>' . wp_kses_post($description) . '</div>
                </div>
            </div>
        </div>';

        // Reviewer box
        $output .= '
        <div class="author-box">
            <div class="author-box-wrap">
                <div class="author-box-header">
                    <div class="info-top">'
                        . $reviewer_gravatar .
                        '<div class="authorname">
                            <a class="vcard author" href="' . esc_url(get_author_posts_url($the_reviewer_id)) . '">' . esc_html($reviewer_authorname) . '</a>
                        </div>
                        <div class="title text-uppercase">' . esc_html($reviewer_title) . '</div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="description">
                    <div>' . wp_kses_post($reviewer_description) . '</div>
                </div>
            </div>
        </div>';
        $output .= '</div>';
    } else {
        // Only user box
        $output = '
        <div class="author-box">
            <div class="author-box-wrap">
                <div class="author-box-header">
                    <div class="info-top">'
                        . $gravatar .
                        '<div class="authorname">
                            <a class="vcard author" href="' . esc_url(get_author_posts_url($the_user_id)) . '">' . esc_html($authorname) . '</a>
                        </div>
                        <div class="title text-uppercase">' . esc_html($title) . '</div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="description">
                    <div>' . wp_kses_post($description) . '</div>
                </div>
            </div>
        </div>';
    }

    return $output;
}
add_shortcode('author-box', 'zg_author_box_shortcode');

// Register this shortcode config for the toolbar
$zg_shortcodes_registry[] = [
    'tag' => 'author-box',
    'name' => 'Author Box',
    'self_closing' => true,
    'attributes' => [
        [
            'type' => 'text',
            'label' => 'Username',
            'attr' => 'username',
            'default' => '',
            'description' => 'WordPress username of the author',
        ],
        [
            'type' => 'text',
            'label' => 'Reviewer',
            'attr' => 'reviewer',
            'default' => '',
            'description' => 'WordPress username of the reviewer (optional)',
        ],
    ],
];
