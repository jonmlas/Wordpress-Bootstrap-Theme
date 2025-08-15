<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', __('Review Options', 'zg'))
        ->where('post_type', '=', 'review')
        ->add_fields([
            Field::make('select', 'rating', 'Rating')
            ->set_options(array(
                '0'   => '0',
                '0.5' => '0.5',
                '1'   => '1',
                '1.5' => '1.5',
                '2'   => '2',
                '2.5' => '2.5',
                '3'   => '3',
                '3.5' => '3.5',
                '4'   => '4',
                '4.5' => '4.5',
                '5'   => '5',
            ))
            ->set_default_value('4'),
            Field::make('text', 'affiliate_link', __('Affiliate Link')),
            Field::make('text', 'bonus', __('Bonus')),
            Field::make('rich_text', 'stats', __('Stats')),
            Field::make('rich_text', 'pros', __('Pros')),
            Field::make('rich_text', 'cons', __('Cons')),
            Field::make('rich_text', 'terms', __('Terms Apply')),
            Field::make('rich_text', 'summary', __('Summary')),
            Field::make('rich_text', 'pros_summary', __('Pros Summary')),
            Field::make('rich_text', 'cons_summary', __('Cons Summary')),
            Field::make('color', 'logo_bg_color', __('Logo Background Color')),
        ]);
});
