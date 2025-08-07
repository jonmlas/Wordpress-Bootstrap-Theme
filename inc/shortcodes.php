<?php
/*
// https://stackoverflow.com/questions/13184085/is-it-possible-to-nest-wordpress-shortcodes-that-are-the-same-shortcode
function counter_suffix_to_nested_shortcodes($content, $nested_shortcodes = []) {
	// Define the regular expression pattern for the shortcodes
	$pattern = '/(\[\/?[a-zA-Z0-9\-\_]+(?: [^\]]+)?(?:_\d+)?\])/is';

	// Define a function to handle the replacements
	$callback = function ($matches) use (&$suffixStack, &$nested_shortcodes) {

		// get tag name
		$tag = $matches[0];
		$pattern = '/\[\/?([A-Za-z0-9\-\_]+)/';
		preg_match($pattern, $tag, $tagNameMatches);
		$tag_name = $tagNameMatches[1];
		$last_tag='';
		//$suffixStack=[];
		$suffixCounter = 0;

		// not in array of shortcode type: return shortcode as it is
		if (!in_array($tag_name, $nested_shortcodes)) {
			return $tag;
		}

		// Extract the shortcode name 
		preg_match($pattern, $tag, $tagNameMatches);
		$shortcode_name = $tagNameMatches[1];


		// Check if it's a closing tag
		if (strpos($tag, '/' . $tag_name) !== false) {
			$suffix = array_pop($suffixStack);
			// Ensure the suffix is correctly placed in the closing tag
			if ($suffix > 0) {
				$tag = str_replace(['[/', ']'], ['[/', '_' . $suffix . ']'], $tag);
				$last_tag = "[$tag_name]";
			}
		} else {
			// Only increment suffixCounter if it's not previous tag and suffixStack is not empty/reset
			if ( !empty($suffixStack) && $tag_name != $last_tag) {
				$suffixCounter = count($suffixStack);
			} else {
				//reset counter
				$suffixCounter = 0;
				$suffixStack = [];
			}

			$suffixStack[] = $suffixCounter;

			// get new shortcode retaining all attributes
			$suffix = $suffixCounter > 0 ? '_' . $suffixCounter : '';
			$tag = str_replace('[' . $shortcode_name, '[' . $shortcode_name . $suffix, $tag);

		}

		return $tag;
	};

	// Initialize the suffix stack and counter
	$suffixStack = [];

	// Perform the replacement using the callback function
	$content = preg_replace_callback($pattern, $callback, $content);

	return $content;
}

// pereprocess content: don't wrap shortcodes
function sanitize_nested_shortcodes($content)  {
	global $nested_shortcodes;

	// unwrap shortcodes in p tags
	$replace = [
		'<p>[' => '[',
		']</p>' => ']',
		'</p>[' => '[',
		']<br />' => ']',
		'&#8220;' => '"',
		'&#8217;' => '"',
		'&#8216;' => '"',
		'&#8243;' => '"'
	];
	// add index suffixes to nested shortcodes
	$content = counter_suffix_to_nested_shortcodes(strtr($content, $replace), $nested_shortcodes);

	return $content;
}
add_filter('the_content', 'sanitize_nested_shortcodes'); 
*/

function zg_section($atts, $content = null) {
    $atts = shortcode_atts(
        array(
            'id'    => '',
            'class' => '',
            'gutter' => 'gx-1',
            'row_class' => '',
        ),
        $atts,
        'bs_section'
    );

    $id = !empty(esc_html($atts['id'])) ? ' id="' . esc_html($atts['id']) . '"' : '';
    $class = !empty(esc_html($atts['class'])) ? ' class="' . esc_html($atts['class'])  . '"' : '';
    $gutter = ' ' . esc_html($atts['gutter']);
    $row_class = !empty(esc_html($atts['row_class'])) ? ' ' . esc_html($atts['row_class']) : '';

    // Check if 'container' is in the class list
    $has_container = strpos($class, 'container') !== false;

    // Use output buffering to capture the content
    ob_start();
    ?>
    <section<?php echo $id; ?><?php echo $class; ?>>
        <?php if ($has_container) : ?>
            <div class="row<?php echo $row_class . $gutter; ?>">
                <?php echo do_shortcode($content); ?>
            </div>
        <?php else : ?>
            <?php echo do_shortcode($content); ?>
        <?php endif; ?>
    </section>
    <?php
    // Get the buffered content and clean the buffer
    $output = ob_get_clean();

    return $output;
}
add_shortcode('bs_section', 'zg_section');



function render_row($atts, $content) {
    $atts = shortcode_atts(
        array(
            'id'        => '',
            'class'     => '',
            'gutter'    => 'gx-1',
            'class' => '',
        ),
        $atts
    );
    $id        = !empty(esc_html($atts['id'])) ? 'id="'.esc_html($atts['id']).'"' : '';
    $class     = !empty(esc_html($atts['class'])) ? ' '.esc_html($atts['class']) : '';
    $gutter    = ' '.esc_html($atts['gutter']);
    $row_class = !empty(esc_html($atts['class'])) ? ' '.esc_html($atts['class']) : '';

    return '<div class="row' . $gutter . $row_class . '">' . do_shortcode($content) . '</div>';
}

function zg_row($atts, $content = null) {
    return render_row($atts, $content);
}
add_shortcode('bs_row', 'zg_row');

function zg_row_child($atts, $content = null) {
    return render_row($atts, $content);
}
add_shortcode('bs_row_child', 'zg_row_child');



function render_column($atts, $content) {
    $atts = shortcode_atts(
        array(
            'col' => '12',
            'class'=> '',
        ),
        $atts
    );
    $class = !empty(esc_html($atts['class'])) ? ' '.esc_html($atts['class']) : '';

    return '<div class="col-md-' . esc_html($atts['col']) . $class . '">' . do_shortcode($content) . '</div>';
}

function zg_column($atts, $content = null) {
    return render_column($atts, $content);
}
add_shortcode('bs_column', 'zg_column');

function zg_column_child($atts, $content = null) {
    return render_column($atts, $content);
}
add_shortcode('bs_column_child', 'zg_column_child');



/**
 * define nested shortcode 
 * names/identifieers: 
 * this prevents to break existing shortcodes
 * to allow 20 nesting levels 
 */
/*
$nested_shortcodes = ['bs_column'];
$nesting_max = 20;

// duplicate shortcodes
foreach($nested_shortcodes as $shortcode){
	for ($i = 0; $i < $nesting_max; $i++) {
		$suffix = $i === 0 ? '' : '_' . $i;
		add_shortcode( $shortcode.$suffix, $shortcode.'_generate');
	}
} */

// Table of Contents TOC
function generate_toc_shortcode() {
    // Enqueue the JavaScript file in the footer
   // wp_enqueue_script('toc-script', get_template_directory_uri() . '/js/toc-script.js', array('jquery'), null, true);

    ob_start();
    ?>
    <ul id="toc"></ul>
    <?php
    return ob_get_clean();
}

add_shortcode('toc', 'generate_toc_shortcode');



// Blog Posts
function custom_blog_posts($atts) {
    // Extract shortcode attributes with default values
    $atts = shortcode_atts(array(
        'posts_per_page' => 5,
        'columns' => 2,
        'column_sizes' => 'md-6,md-6', // Default Bootstrap column sizes for 2 columns
    ), $atts, 'custom_blog_posts');

    // Parse column sizes
    $column_sizes = explode(',', $atts['column_sizes']);
    $columns = count($column_sizes);

    // Ensure the number of columns matches the number of column sizes
    if ($columns != $atts['columns']) {
        $atts['columns'] = $columns;
    }

    // Query for the latest posts
    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $atts['posts_per_page'],
    ));

    // Start output buffering
    ob_start();
	echo '<div class="container blog-posts">';
    if ($query->have_posts()) {
        echo '<div class="row blog">';
        $counter = 0;

        while ($query->have_posts()) {
            $query->the_post();

            if ($counter % $atts['columns'] == 0 && $counter != 0) {
                echo '</div><div class="row blog">';
            }

            // Determine the column size for the current post
            $column_size = $column_sizes[$counter % $atts['columns']];

            echo '<div class="col-' . esc_attr($column_size) . '">';
			echo '<div class="box post">';
            if (has_post_thumbnail()) {
                echo '<div class="post-thumbnail"><a href="' . get_permalink() . '" aria-label="' . get_the_title() . '">';
                the_post_thumbnail('full', array('class' => 'img-fluid'));
                echo '</a></div>';
            }
            echo '<div class="post-content">';
            echo '<h3 class="post-title"><a href="' . get_permalink() . '" aria-label="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
            echo '<p class="post-date">' . get_the_date() . '</p>';
            // Customizing the excerpt
            $excerpt = get_the_excerpt();
            $excerpt = str_replace('[&hellip;]', '…', $excerpt);
            echo '<div class="post-excerpt">' . $excerpt . '</div>';
            echo '</div>'; // .box
			echo '</div>'; // .post-content
            echo '</div>'; // .col-*

            $counter++;
        }

        echo '</div>'; // .row
        wp_reset_postdata();
    } else {
        echo '<p>No posts found.</p>';
    }
	echo '</div>';
    // Get the buffered content
    $output = ob_get_clean();

    return $output;
}
add_shortcode('blog_posts', 'custom_blog_posts');


// Related Posts
function display_related_posts($atts) {
    global $post;
    
    // Set default attributes and combine with user-provided attributes
    $atts = shortcode_atts(array(
        'posts_per_page' => 3,
        'columns' => 3
    ), $atts, 'related_posts');
    
    // Get the categories of the current post
    $categories = wp_get_post_categories($post->ID);
    
    if (empty($categories)) {
        return 'No related posts found.';
    }
    
    // Query for related posts
    $query_args = array(
        'category__in' => $categories,
        'post__not_in' => array($post->ID),
        'posts_per_page' => $atts['posts_per_page']
    );
    
    $related_posts = new WP_Query($query_args);
    
    if (!$related_posts->have_posts()) {
        return 'No related posts found.';
    }
    
    // Start output buffering
    ob_start();
    
	printf('<h2>%s</h2>', __('Related Posts', 'zg'));
	
    echo '<div class="row">';

	$excerpt = get_the_excerpt();
    $excerpt = str_replace('[&hellip;]', '…', $excerpt);

    // Loop through related posts
    while ($related_posts->have_posts()) : $related_posts->the_post();
        ?>
        <div class="col-md-<?php echo intval(12 / $atts['columns']); ?>">
            <div class="box card p-0 mb-4">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium', array('class' => 'card-img-top')); ?></a>
                <?php endif; ?>
                <div class="card-body">
                    <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p class="card-text"><small class="text-muted"><?php echo get_the_date(); ?></small></p>
                    <p class="card-text"><?php echo $excerpt; ?></p>
                </div>
            </div>
        </div>
        <?php
    endwhile;
    
    echo '</div>';
    
    // Reset post data
    wp_reset_postdata();
    
    // Return the content
    return ob_get_clean();
}
add_shortcode('related_posts', 'display_related_posts');


// Post Navigation
function post_navigation_shortcode() {
    ob_start(); // Start output buffering

    wp_reset_postdata();

    $count_posts = wp_count_posts();

    if ( $count_posts->publish > '1' ) :
        $next_post = get_next_post();
        $prev_post = get_previous_post();
        ?>
        <hr class="mt-5">
        <div class="post-navigation d-flex justify-content-between">
            <?php if ( $prev_post ) : ?>
                <div class="pr-3">
                    <a class="previous-post btn btn-lg btn-outline-secondary" href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" title="<?php echo esc_attr( get_the_title( $prev_post->ID ) ); ?>">
                        <span class="arrow">&larr;</span>
                        <span class="title"><?php echo wp_kses_post( get_the_title( $prev_post->ID ) ); ?></span>
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if ( $next_post ) : ?>
                <div class="pl-3">
                    <a class="next-post btn btn-lg btn-outline-secondary" href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" title="<?php echo esc_attr( get_the_title( $next_post->ID ) ); ?>">
                        <span class="title"><?php echo wp_kses_post( get_the_title( $next_post->ID ) ); ?></span>
                        <span class="arrow">&rarr;</span>
                    </a>
                </div>
            <?php endif; ?>
        </div><!-- /.post-navigation -->
        <?php
    endif;

    return ob_get_clean(); // Return the buffered content
}
add_shortcode('post_navigation', 'post_navigation_shortcode');


// Bootstrap Buttons
function bootstrap_button($atts) {
	// Default attributes
	$atts = shortcode_atts(
		array(
			'text' => 'Click me',          // Button text
			'color' => '',          		// Button color (primary, secondary, success, etc.)
			'size' => 'sm',                  // Button size (sm, lg)
			'outline' => 'false',          // Outline button (true, false)
			'tags' => '',                  // Additional tags (e.g., data-toggle="modal" data-target="#myModal")
			'disabled' => 'false',         // Disabled state (true, false)
			'width' => '',                 // Custom width (e.g., 100px, 50%)
			'toggle' => '',                // Toggle states (e.g., data-bs-toggle="button")
			'id' => '',                    // Button ID
			'class' => '',                 // Additional classes
			'url' => '',                   // Button URL
			'target' => '',                // Link target (_blank, _self, etc.)
		), $atts, 'bootstrap_button'
	);

	// Build the button class
	$button_class = 'btn';
	$button_class .= $atts['outline'] === 'true' ? ' btn-outline-' . $atts['color'] : ' btn-' . $atts['color'];
	$button_class .= !empty($atts['size']) ? ' btn-' . $atts['size'] : '';
	$button_class .= !empty($atts['class']) ? ' ' . $atts['class'] : '';

	// Build the disabled attribute
	$disabled_attr = $atts['disabled'] === 'true' ? ' disabled' : '';

	// Build the width style
	$width_style = !empty($atts['width']) ? ' style="width:' . esc_attr($atts['width']) . ';"' : '';

	// Build the ID attribute
	$id_attr = !empty($atts['id']) ? ' id="' . esc_attr($atts['id']) . '"' : '';

	// Build the target attribute
	$target_attr = !empty($atts['target']) ? ' target="' . esc_attr($atts['target']) . '"' : '';

	// Determine the tag to use
	$tag = !empty($atts['url']) ? 'a' : 'button';

	// Build the button HTML
	$button_html = '<' . $tag . ' href="' . esc_url($atts['url']) . '" class="' . esc_attr($button_class) . '"' . $id_attr . $disabled_attr . $width_style . $target_attr;

	// Add any additional tags
	if (!empty($atts['tags'])) {
		$button_html .= ' ' . $atts['tags'];
	}

	// Add toggle attributes
	if (!empty($atts['toggle'])) {
		$button_html .= ' ' . $atts['toggle'];
	}

	// Close the opening tag and add the button text
	$button_html .= '>' . esc_html($atts['text']) . '</' . $tag . '>';

	return $button_html;
}
	
add_shortcode('bs_button', 'bootstrap_button');



// Main Table shortcode
function main_table($atts = array()) {

	extract(shortcode_atts(array(
		'slugs' => '',
		'cat' 	=> '',
		'count' => '-1',
		'ctr'	=> '0',
		'offset' => '0'
	), $atts));

	// Convert comma-separated slugs to an array
	$slugs = isset($atts['slugs']) ? explode(',', $atts['slugs']) : '';
	$count = isset($atts['count']) ? absint($atts['count']) : -1;
	$ctr = isset($atts['ctr']) ? absint($atts['ctr']) : 1;
	
	global $post;
	$args = array(
		'post_status' => 'publish',
		'posts_per_page' => $count,
		'post_type' => 'review',
		'ignore_sticky_posts' => true,
		'offset' => $offset,
	);
	if(!empty($slugs)) {
		$args = array(
			'post_name__in' => $slugs,
			'orderby' => 'post_name__in',  // Maintain the order of IDs specified
		);
	}
	if(isset($atts['cat'])) {

			$cat_array = array(
				'tax_query' => array(
					array(
						'taxonomy' => 'review-category',   // taxonomy name
						'field' => 'slug',           // term_id, slug or name
						'terms' => $atts['cat'],         // term id, term slug or term name
					)
				)
			);
			$args = array_merge($args,$cat_array); 

	}
    $wp_query = new WP_Query( $args );

	/*
	$output = '
	
	<div>
		<ul class="table-icon">
		<li class="uk-provided">UK Licensed</li>
		<li class="safe-secure">SAFE & SECURE</li>
		<li class="trusted-reviews">Trusted Reviews</li>
		</ul>
	</div>'; */
	
	$output = '	
	<span class="affiliate-disclosure"><a href="'.get_site_url().'/affiliate-disclosure/"><i class="fas fa-bell"></i> ' . __(' Affiliate Discloure', 'zg') . '</a></span>
	<div class="reviews-table">';
	
    while ($wp_query->have_posts()) {
        $wp_query->the_post();
		$review = lastimosa_get_post_option( get_the_ID(), 'review_options' );
		if(!empty($review['logo_bg_color'])) {
			$logo_background_color = ' style="background-color:'.$review['logo_bg_color'].'"';
		}else{
			$logo_background_color = '';
		}
		if ( has_post_thumbnail() ) { 
			$review_thumbnail = '<a href="'.$review['affiliate_link'].'" class="logo-wrap hvr-grow" target="_blank"'.$logo_background_color.'>
				<span>'. get_the_title(get_the_ID()).'</span>'
				.get_the_post_thumbnail( get_the_ID(), "main-table-logo", array( "alt" => get_the_title(), "class" => "logo img-fluid hvr-grow" ) ).
			'</a>';
		}else{ 
			$review_thumbnail = '<p class="entry-title"><a href="'.get_the_permalink().'"'.$logo_background_color.'>'.get_the_title().'</a></p>';
		} 
		$bonus = '';
		if(!empty($review['bonus'])) {
			$bonus = '<div class="bonus">' . $review['bonus'] . '</div>';
		}
		
		if(!empty($review['terms'])) { 
		/*	
		$terms = '<p class="terms-conditions"> <a href="#" class="tooltip" data-tooltip-content="#tooltip_content_' . get_the_ID() . '">' . __('Terms Apply','bestratedslots') . '</a>'.'</p>
		<!--googleoff: index-->
		<div class="tooltip_templates">
			<div id="tooltip_content_'.get_the_ID().'">
				<div class="m-b-10">' . $review['terms'] . '</div>
				<a class="btn btn-secondary btn-xs btn-icon font-weight-normal" href="'.$review['affiliate_link'].'" role="button" target="_blank">Claim Bonus <span class="ico-right"><i class="ico ico-caret-right"></i></span></a>
			</div>
		</div>
		<!--googleon: index-->';  */
			$terms = $review['terms'];
		} else {
			$terms = __('UK players accepted, terms apply, 18+','zg');
		} 
		if(!empty($review['attributes'])){
			$list = explode(", ", $review['attributes']);
			ob_start(); // Start output buffering
			?>
			<ul>
			<?php foreach ($list as $item) { ?>
				<li><?php echo $item; ?></li>
			<?php } ?>
			</ul>
			<?php
			$attributes = ob_get_clean(); // Store the buffered output in a variable
		}else{
			$attributes = '';
		}
		$output.= '
		<div class="table-row">
			<div class="info">
				<div class="col"><span class="counter">'.$ctr.'</span>'
					.$review_thumbnail.
					'<div class="rating"><i class="fa fa-star" aria-hidden="true"></i> '.$review['rating'] . '/5'.'</div>
					
				</div>
				<div class="col">
					<div class="expert-info"><span class="expert-reviewed"><i></i> '.__('Expert Reviewed','zg').'</span> <span class="fact-checked"><i></i> '.__('Fact Checked','zg').'</span></div>
					<div class="title">'.get_the_title(get_the_ID()).'</div>'
					.$bonus.
				'<div class="attributes">'.
						$attributes.
					'		
					</div>
					
				</div>
				<div class="col">				
					<div class="play-now">
						<a class="btn" href="'.$review['affiliate_link'].'" target="_blank">
							'.__('Visit Site','lastimosa').'
						</a>
					</div>
					<div><a href="'.get_the_permalink(get_the_ID()).'" class="read-review">'.__('Review','zg').'</a></div>
					
				</div>
			</div>
			<hr />
			<div class="terms-conditions">
				<img src="'. get_stylesheet_directory_uri() . '/images/united-kingdom.svg" class="flag alignleft" alt="United Kingdom flag" width="35" height="30">'.
				$terms.
			'</div>
			
		</div>';
		$ctr++;
    }
	
	$output .= '</div>';
    wp_reset_postdata();
    return $output;
}
add_shortcode( 'main-table', 'main_table' );

// Reviews Table Heading shortcode
function reviews_table_heading() {
$output = '	
	<div class="reviews-table">';
		$review = lastimosa_get_post_option( get_the_ID(), 'review_options' );
		if(!empty($review['logo_bg_color'])) {
			$logo_background_color = ' style="background-color:'.$review['logo_bg_color'].'"';
		}else{
			$logo_background_color = '';
		}
		if ( has_post_thumbnail() ) { 
			$review_thumbnail = '<a href="'.$review['affiliate_link'].'" class="logo-wrap hvr-grow" target="_blank"'.$logo_background_color.'>
				<span>'. get_the_title(get_the_ID()).'</span>'
				.get_the_post_thumbnail( get_the_ID(), "main-table-logo", array( "alt" => get_the_title(), "class" => "logo img-fluid hvr-grow" ) ).
			'</a>';
		}else{ 
			$review_thumbnail = '<p class="entry-title"><a href="'.get_the_permalink().'"'.$logo_background_color.'>'.get_the_title().'</a></p>';
		} 
		$bonus = '';
		if(!empty($review['bonus'])) {
			$bonus = '<div class="bonus">' . $review['bonus'] . '</div>';
		}
		
		if(!empty($review['terms'])) { 
			$terms = $review['terms'];
		} else {
			$terms = __('UK players accepted, terms apply, 18+','zg');
		} 
		if(!empty($review['attributes'])){
			$list = explode(", ", $review['attributes']);
			ob_start(); // Start output buffering
			?>
			<ul>
			<?php foreach ($list as $item) { ?>
				<li><?php echo $item; ?></li>
			<?php } ?>
			</ul>
			<?php
			$attributes = ob_get_clean(); // Store the buffered output in a variable
		}else{
			$attributes = '';
		}
		$output.= '
		<div class="table-row">
			<div class="info">
				<div class="col">'
					.$review_thumbnail.
					'<div class="rating"><i class="fa fa-star" aria-hidden="true"></i> '.$review['rating'] . '/5'.'</div>
					
				</div>
				<div class="col">
					<div class="expert-info"><span class="expert-reviewed"><i></i> '.__('Expert Reviewed','zg').'</span> <span class="fact-checked"><i></i> '.__('Fact Checked','zg').'</span></div>
					'
					.$bonus.
				'<div class="attributes">'.
						$attributes.
					'		
					</div>
					
				</div>
				<div class="col">				
					<div class="play-now">
						<a class="btn" href="'.$review['affiliate_link'].'" target="_blank">
							'.__('Visit Site','lastimosa').'
						</a>
					</div>
				
					
				</div>
			</div>
			<hr />
			<div class="terms-conditions">
				<img src="'. get_stylesheet_directory_uri() . '/images/united-kingdom.svg" class="flag alignleft" alt="United Kingdom flag" width="35" height="30">'.
				$terms.
			'</div>
			
		</div>
	</div>';
    return $output;
}
add_shortcode( 'reviews-table-heading', 'reviews_table_heading' );

// Register the shortcode with a search form
function compare_reviews_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(
        array(),
        $atts,
        'compare_reviews'
    );

    // Get the value submitted through the form
    $search_query = isset($_GET['search_query']) ? sanitize_text_field($_GET['search_query']) : '';

    // Query posts based on the search query
    $query_args = array(
        'post_type'      => 'review',  // Adjust post type as needed
        'posts_per_page' => 3,
        's'              => $search_query,
    );
    $posts = get_posts($query_args);

    // Create a comparison table
    $output = '<form method="get" action="' . esc_url(home_url()) . '">';
    $output .= '<label for="search_query">Search by Title:</label>';
    $output .= '<input type="text" name="search_query" value="' . esc_attr($search_query) . '" />';
    $output .= '<input type="submit" value="Search" />';
    $output .= '</form>';

    $output .= '<table>';
    $output .= '<tr><th>Attribute</th>';
    
    // Display post titles in the table header
    foreach ($posts as $post) {
        $output .= '<th>' . esc_html($post->post_title) . '</th>';
    }
    $output .= '</tr>';
    
    // Compare post titles, excerpts, and thumbnails
    $output .= '<tr><td>Title</td>';
    foreach ($posts as $post) {
        $output .= '<td>' . esc_html($post->post_title) . '</td>';
    }
    $output .= '</tr>';
    
    $output .= '<tr><td>Excerpt</td>';
    foreach ($posts as $post) {
        $output .= '<td>' . esc_html($post->post_excerpt) . '</td>';
    }
    $output .= '</tr>';
    
    $output .= '<tr><td>Thumbnail</td>';
    foreach ($posts as $post) {
        $output .= '<td>' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</td>';
    }
    $output .= '</tr>';
    
    $output .= '</table>';

    return $output;
}
add_shortcode('compare_reviews', 'compare_reviews_shortcode');

// Unyson Option
function unyson_option($atts) {
	$review = lastimosa_get_post_option( get_the_ID(), 'review_options' );
	return $review[$atts['field']];
}
add_shortcode( 'unyson-option', 'unyson_option');

// Play Now Button
function play_now_shortcode() {
	$review = lastimosa_get_post_option( get_the_ID(), 'review_options' );
	ob_start(); ?>
	<div class="play-now">
		<a class="btn" href="<?php echo $review['affiliate_link']; ?>" target="_blank">
			Visit Site
		</a>
	</div>
	<?php 
	$output = ob_get_clean(); 
	return $output;
}
add_shortcode( 'play-now', 'play_now_shortcode');

add_shortcode( 'footag', 'wpdocs_footag_func' );
function wpdocs_footag_func( $atts ) {
	return ;
}

// function that runs when shortcode is called
function author_box($atts = array(), $content = null) { 
	
	extract(shortcode_atts(array(
		'username' => '',
		'reviewer' => '',
    ), $atts));
 
	global $post;
	$the_user = get_user_by('login', $username);
	$the_reviewer = get_user_by('login', $reviewer);
	$default = '';
	if(!empty($the_user)) {
		$the_user_id = $the_user->ID;
	}else{
		$the_user_id = get_the_author_meta( 'ID' );
	}
	
	if(!empty($the_reviewer)) {
		$the_reviewer_id = $the_reviewer->ID;
	}else{
		$the_reviewer_id = get_the_author_meta( 'ID' );
	}
		
	$authorname = get_the_author_meta( 'display_name', $the_user_id );
	$gravatar = get_avatar( get_the_author_meta( 'user_email', $the_user_id ), '60', $default, $authorname, array( 'class' => array( 'alignleft' ) ) );
	$email = get_the_author_meta( 'email', $the_user_id );
	$title = get_the_author_meta( 'title', $the_user_id );
	$description = wpautop(get_the_author_meta('full_bio', $the_user_id ));
	$icon_email = '<a href="mailto:'.$email.'" target="_blank"><i class="fa fa-envelope"></i></a>';
	$url = get_the_author_meta('url', $the_user_id);
	$title = get_the_author_meta('title', $the_user_id);
	$twitter = get_the_author_meta('twitter', $the_user_id);
	$linkedin = get_the_author_meta('linkedin', $the_user_id);
	
	$icon_url = '';
	if(!empty($url)) {
		$icon_url = '<a href="'.$url.'" target="_blank"><i class="fa fa-chrome" aria-hidden="true"></i></a> ';
	}

	$icon_twitter = '';
	if(!empty($twitter)) {
		$icon_twitter = '<a href="'.$twitter.'" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> ';
	} 

	$icon_linkedin = '';
	if(!empty($linkedin)) {
		$icon_linkedin = '<a href="'.$linkedin.'" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>';
	}
	
	if(!empty($the_reviewer)) {
		
		$reviewer_authorname = get_the_author_meta( 'display_name', $the_reviewer_id );
		$reviewer_gravatar = get_avatar( get_the_author_meta( 'user_email', $the_reviewer_id ), '60', $default, $authorname, array( 'class' => array( 'alignleft' ) ) );
		$reviewer_email = get_the_author_meta( 'email', $the_reviewer_id );
		$reviewer_title = get_the_author_meta( 'title', $the_reviewer_id );
		$reviewer_description = get_the_author_meta('full_bio', $the_reviewer_id );
		$reviewer_icon_email = '<a href="mailto:'.$email.'" target="_blank"><i class="fa fa-envelope"></i></a>';
		$reviewer_url = get_the_author_meta('url', $the_reviewer_id);
		$reviewer_title = get_the_author_meta('title', $the_reviewer_id);
		$reviewer_twitter = get_the_author_meta('twitter', $the_reviewer_id);
		$reviewer_linkedin = get_the_author_meta('linkedin', $the_reviewer_id);

		$reviewer_icon_url = '';
		if(!empty($url)) {
			$icon_url = '<a href="'.$url.'" target="_blank"><i class="fa fa-chrome" aria-hidden="true"></i></a> ';
		}

		$reviewer_icon_twitter = '';
		if(!empty($twitter)) {
			$icon_twitter = '<a href="'.$twitter.'" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> ';
		} 

		$reviewer_icon_linkedin = '';
		if(!empty($linkedin)) {
			$icon_linkedin = '<a href="'.$linkedin.'" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>';
		}
	
		$output = '<div class="authors-box">';
		
		$output .= '
		<div class="author-box">
			<div class="author-box-wrap">
				<div class="author-box-header">
					<div class="info-top">'
						.$gravatar.'
						<div class="authorname">
							<a class="vcard author" href="'.get_author_posts_url( $the_user_id ).'" >'.$authorname.'</a>
						</div>
						<div class="title text-uppercase">'.$title.'</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="description">
				<div>'.$description.'</div>
				</div>
			</div> 		
		</div>'; 
		/*			<p class="feedback">'.__("Feedback - ","zg"). '<a href="mailto:'.$email.'">'.$email.'</a></p>
		 <p class="social-media">'.$reviewer_icon_url.$reviewer_icon_twitter.$reviewer_icon_linkedin.'</p> */
		$output .= '
		<div class="author-box">
			<div class="author-box-wrap">
				<div class="author-box-header">
					<div class="info-top">'
						.$reviewer_gravatar.'
						<div class="authorname">
							<a class="vcard author" href="'.get_author_posts_url( $the_reviewer_id ).'" >'.$reviewer_authorname.'</a>
						</div>
						<div class="title text-uppercase">'.$reviewer_title.'</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="description">
				<div>'.$reviewer_description.'</div>
				</div>
			</div> 		
		</div>'; 

		$output .= '</div>';
		
	}else{
		$output = '
		<div class="author-box">
			<div class="author-box-wrap">
				<div class="author-box-header">
					<div class="info-top">'
						.$gravatar.'
						<div class="authorname">
							<a class="vcard author" href="'.get_author_posts_url( $the_user_id ).'" >'.$authorname.'</a>
						</div>
						<div class="title text-uppercase">'.$title.'</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="description">
				<div>'.$description.'</div>
				</div>
			</div> 		
		</div>'; 
	}

	// Output needs to be return
	return $output;
} 
// register shortcode
add_shortcode('author-box', 'author_box');


function author_avatar() {
	$default = '';
	$authorname = '';
	$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
	$the_user_id = $author->ID;	
	return get_avatar( get_the_author_meta( 'user_email', $the_user_id ), '245', $default, $authorname, array( 'class' => array( 'alignleft' ) ) );
}
add_shortcode('author-avatar', 'author_avatar'); 


// Author Bio
function author_bio() { 
	$default = '';
	$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
	$the_user_id = $author->ID;	
	$authorname = get_the_author_meta( 'display_name', $the_user_id );
	$gravatar = get_avatar( get_the_author_meta( 'user_email', $the_user_id ), '105', $default, $authorname, array( 'class' => array( 'alignleft' ) ) );
	$email = get_the_author_meta( 'email', $the_user_id );
	$title = get_the_author_meta( 'title', $the_user_id );
	$description = wpautop(get_the_author_meta('full_bio', $the_user_id ));
	$icon_email = '<a href="mailto:'.$email.'" target="_blank"><i class="fa fa-envelope"></i></a>';
	$url = get_the_author_meta('url', $the_user_id);

	$user_info = '
			<div class="info-top">
				<p class="text-uppercase">'. __('Author', 'zg').'</p>
				<div class="authorname">
					<a class="vcard author" href="'.get_author_posts_url( $the_user_id ).'" >'.$authorname.'</a>
				</div>
				<div class="title text-uppercase">'.$title.'</div>
			</div>
			<div class="description">
			<div>'.$description.'</div>
			<p class="feedback">'.__("Feedback - ","zg"). '<a href="mailto:'.$email.'">'.$email.'</a></p>
			<div class="clearfix"></div>
			'; 

	// Output needs to be return
	return $user_info;
} 
// register shortcode
add_shortcode('author-bio', 'author_bio');


// function that runs when shortcode is called
function author_social($atts = array(), $content = null) { 
	
	$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
	$the_user_id = $author->ID;	
	
	$url = get_the_author_meta('url', $the_user_id);
	$twitter = get_the_author_meta('twitter', $the_user_id);
	$linkedin = get_the_author_meta('linkedin', $the_user_id);
	
	$icon_url = '';
	if(!empty($url)) {
		$icon_url = '<a href="'.$url.'" target="_blank"><i class="fa fa-chrome" aria-hidden="true"></i></a> ';
	}

	$icon_twitter = '';
	if(!empty($twitter)) {
		$icon_twitter = '<a href="'.$twitter.'" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a> ';
	} 

	$icon_linkedin = '';
	if(!empty($linkedin)) {
		$icon_linkedin = '<a href="'.$linkedin.'" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>';
	}

	return '<p class="social-media">'.$icon_url.$icon_twitter.$icon_linkedin.'</p>';
} 
// register shortcode
add_shortcode('author-social', 'author_social');


// Add shortcode to display user's comments with date, time, and post title
function user_comments_shortcode($atts) {
    // Check if user is logged in
    if (is_user_logged_in()) {
        // Get the current user ID
        $current_user_id = get_current_user_id();

        // Query user's comments
        $comments = get_comments(array(
            'user_id' => $current_user_id,
            'status' => 'approve', // You can change this to 'all' if you want to include pending and spam comments
        ));

        // Output the comments
        if ($comments) {
            $output = '<ul>';
            foreach ($comments as $comment) {
                $post_title = get_the_title($comment->comment_post_ID);
                $post_permalink = get_permalink($comment->comment_post_ID);
                $comment_date = get_comment_date('F j, Y \a\t g:i a', $comment->comment_ID);

                $output .= '<li>';
                $output .= '<strong>' . esc_html($post_title) . '</strong> - ';
                $output .= '<a href="' . esc_url($post_permalink) . '#comment-' . $comment->comment_ID . '">';
                $output .= esc_html($comment_date) . '</a><br>';
                $output .= esc_html($comment->comment_content);
                $output .= '</li>';
            }
            $output .= '</ul>';
        } else {
            $output = 'No comments found.';
        }

        return $output;
    } else {
        return 'Please log in to view your comments.';
    }
}

// Register the shortcode
add_shortcode('user_comments', 'user_comments_shortcode');


function pros_cons_shortcode( $atts ) {
    // Parse the shortcode attributes
    $atts = shortcode_atts( array(
    //    'category' => 'default_category', // Default category if not provided
    ), $atts, 'pros_cons_shortcode' );
	$review = lastimosa_get_post_option( get_the_ID(), 'review_options' );
	$output = '';
	ob_start(); // Start output buffering ?>
	<?php if(!empty($review['pros'] || $review['cons'])) { ?>
	<div class="pros-cons clearfix box">
		<?php if(!empty($review['pros'])) { ?>
		<div class="pros card">
			<h3><i class="fa fa-thumbs-up"></i> Pros</h3>
			<div class="card-body"><?php echo $review['pros']; ?></div>
		</div>	
		<?php } ?>
		<?php if(!empty($review['cons'])) { ?>
		<div class="cons card">
			<h3><i class="fa fa-thumbs-down"></i> Cons</h3>
			<div class="card-body"><?php echo $review['cons']; ?></div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<?php
	$output = ob_get_clean();
	return $output;
}
add_shortcode( 'pros-cons', 'pros_cons_shortcode' );

/*
function key_features() {
	$review = lastimosa_get_post_option( get_the_ID(), 'review_options' );
	if(!empty($review['key_features'])) {
		return $review['key_features'];
	} else {
		return;
	}
}

add_shortcode( 'key-features', 'key_features' ); */


function accordion_shortcode($atts, $content = null) {
    $atts = shortcode_atts(
        array(
            'title' => 'Accordion Section',
        ),
        $atts,
        'accordion'
    );
	// Remove empty paragraphs and trim the content
    $content = trim($content);
    $content = preg_replace('/<p[^>]*><\\/p[^>]*>/', '', $content);

    // Remove line breaks
    $content = preg_replace('/[\r\n]+/', '', $content);

    // Wrap each section of text in <h3> tags
    //$content = preg_replace('/(?:<p>)?([^<]+)(?:<\/p>)?/', '<h3>$1</h3>', $content);

    // Use output buffering to capture the content
    ob_start();
    ?>
	<div class="accordion-set">
		<h3><?php echo esc_html($atts['title']); ?> <i class="fa fa-plus-circle"></i></h3>
		<div class="accordion-content">
		   <?php echo do_shortcode($content); ?>
		</div>
  	</div>
    <?php
    // Get the buffered content and clean the buffer
    $output = ob_get_clean();

    return $output;
}

add_shortcode('faqs-accordion', 'accordion_shortcode');


function simple_accordion_shortcode($atts, $content = null) {
    $atts = shortcode_atts(
        array(
            'title' => 'Accordion Section',
        ),
        $atts,
        'accordion'
    );

    // Use output buffering to capture the content
    ob_start();
    ?>
	<div class="simple-accordion-set">
		<h3><?php echo esc_html($atts['title']); ?> <i class="fa fa-chevron-down"></i></h3>
		<div class="simple-accordion-content">
		   <?php echo do_shortcode($content); ?>
		</div>
  	</div>
    <?php
    // Get the buffered content and clean the buffer
    $output = ob_get_clean();

    return $output;
}

add_shortcode('simple-accordion', 'simple_accordion_shortcode');


function star_rating($atts) {
	$review = lastimosa_get_post_option( get_the_ID(), 'review_options' );

    // Get the rating value from the $review array
    $rating = floatval($atts);
    $rating = max(0, min(5, $rating)); // Ensure rating is between 0 and 5

    // Calculate the number of full and half stars based on specific ranges
    if ($rating >= 0 && $rating <= 0.5) {
        $full_stars = 0;
        $half_star = 1;
    } elseif ($rating > 0.5 && $rating <= 1) {
        $full_stars = 1;
        $half_star = 0;
    } else {
        $full_stars = floor($rating);
        $half_star = ceil(($rating - $full_stars) * 2);
    }

    // Output the star rating using Font Awesome
    $output = '<div class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $full_stars) {
            $output .= '<i class="fa fa-star"></i>';
        } elseif ($half_star > 0) {
            $output .= '<i class="fa fa-star-half-alt"></i>';
            $half_star = 0; // To make sure only one half star is added
        } else {
            $output .= '<i class="fa fa-star"></i>';
        }
    }
    $output .= '<span class="rating-score"> '.$atts.'/5</span></div>';

    return $output;
}

// Register the shortcode
add_shortcode('star-rating', 'star_rating');


function expand_text_shortcode($atts, $content = null) {
    // Parse attributes
    $atts = shortcode_atts(array(), $atts);

    // Generate unique ID for each expandable text
    $unique_id = uniqid('expand_text_');

    // Output HTML for expandable text
    $output = '<span id="' . esc_attr($unique_id) . '_toggle" class="expand-toggle">»</span>';
    $output .= '<div id="' . esc_attr($unique_id) . '_content" class="expand-content" style="display: none;">';
    $output .= do_shortcode($content);
    $output .= '</div>';

    // Output jQuery script to smoothly toggle visibility and hide toggle span after click
    $output .= '<script>';
    $output .= 'jQuery(document).ready(function($) {';
    $output .= '    $("#' . esc_js($unique_id) . '_toggle").click(function() {';
    $output .= '        $("#' . esc_js($unique_id) . '_content").slideToggle("slow");';
    $output .= '        $(this).hide();'; // Hide the toggle span after click
    $output .= '    });';
    $output .= '});';
    $output .= '</script>';

    return $output;
}
add_shortcode('expand_text', 'expand_text_shortcode');


function expand_text_shortcode_mobile($atts, $content = null) {
    // Parse attributes
    $atts = shortcode_atts(array(), $atts);

    // Generate unique ID for each expandable text
    $unique_id = uniqid('expand_text_');

    // Only render content and script for mobile devices
    if (wp_is_mobile()) {
        // Output HTML for expandable text with toggle displayed for mobile
        $output = '<span id="' . esc_attr($unique_id) . '_toggle" class="expand-toggle">»</span>';
        $output .= '<div id="' . esc_attr($unique_id) . '_content" class="expand-content" style="display: none;">';
        $output .= do_shortcode($content); // Process the nested content
        $output .= '</div>';

        // Output jQuery script to toggle visibility for mobile
        $output .= '<script>';
        $output .= 'jQuery(document).ready(function($) {';
        $output .= '    $("#' . esc_js($unique_id) . '_toggle").click(function() {';
        $output .= '        $("#' . esc_js($unique_id) . '_content").slideToggle("slow");';
        $output .= '        $(this).hide();'; // Hide the toggle span after click
        $output .= '    });';
        $output .= '});';
        $output .= '</script>';

        return $output;
    } else {
        // On desktop, display all content without any toggle functionality
        return do_shortcode($content);
    }
}
add_shortcode('expand_text_mobile', 'expand_text_shortcode_mobile');


function expand_text_shortcode_desktop($atts, $content = null) {
    // Parse attributes
    $atts = shortcode_atts(array(), $atts);

    // Generate unique ID for each expandable text
    $unique_id = uniqid('expand_text_');

    // Only render content and script for desktop devices
    if (!wp_is_mobile()) {
        // Output HTML for expandable text with toggle displayed for desktop
        $output = '<span id="' . esc_attr($unique_id) . '_toggle" class="expand-toggle">»</span>';
        $output .= '<div id="' . esc_attr($unique_id) . '_content" class="expand-content" style="display: none;">';
        $output .= do_shortcode($content); // Process the nested content
        $output .= '</div>';

        // Output jQuery script to toggle visibility for desktop
        $output .= '<script>';
        $output .= 'jQuery(document).ready(function($) {';
        $output .= '    $("#' . esc_js($unique_id) . '_toggle").click(function() {';
        $output .= '        $("#' . esc_js($unique_id) . '_content").slideToggle("slow");';
        $output .= '        $(this).hide();'; // Hide the toggle span after click
        $output .= '    });';
        $output .= '});';
        $output .= '</script>';

        return $output;
    } else {
        // On mobile, display all content without any toggle functionality
        return do_shortcode($content);
    }
}
add_shortcode('expand_text_desktop', 'expand_text_shortcode_desktop');



function review_box($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'slug' => '',
    ), $atts);

    // Get the slug from the shortcode attribute
    $review_slug = $atts['slug'];

    // Check if slug is provided
    if (!empty($review_slug)) {
        // Get the review post by slug
        $review_post = get_page_by_path($review_slug, OBJECT, 'review');
		$ID = $review_post->ID;
		$alt_text = get_post_meta($ID, '_wp_attachment_image_alt', true);
		echo $alt_text;
        // Check if the review post exists and is of the "reviews" post type
        if ($review_post && $review_post->post_type == 'review') {
			
			$review = lastimosa_get_post_option( $ID, 'review_options' );
			
			if(!empty($review['summary'])) { 
				$summary = $review['summary'];
			} 
			
            ob_start(); // Start output buffering ?>
			<div class="review-box">
				<div class="title"><h3><a href="<?php echo $review['affiliate_link']; ?>"><?php echo get_the_title($ID); ?></a></h3>
				</div> 
				<?php echo sharp_star_rating($review['rating']); ?>
				<?php $thumbnail_url = get_the_post_thumbnail_url($ID);
				if(!empty($review['square_logo'])) { ?>
					<div class="review-thumbnail">
						<a href="<?php echo $review['affiliate_link']; ?>" class="logo-wrap hvr-grow" target="_blank"><span> <?php get_the_title($ID); ?></span>
							<img src="<?php echo $review['square_logo']['url']; ?>" alt="<?php echo esc_attr(($review_post->post_title)); ?>" class="alignright img-fluid hvr-grow">
						</a>
				</div>
				<?php } else {
					if (!empty($thumbnail_url)) { ?>
						<div class="review-thumbnail">
							<a href="<?php echo $review['affiliate_link']; ?>" class="logo-wrap hvr-grow" target="_blank"><span> <?php get_the_title($ID); ?></span>
								<?php echo get_the_post_thumbnail( $ID, "review-box", array( 'alt' => esc_attr(($review_post->post_title)), 'class' => 'alignright img-fluid hvr-grow' ) ); ?>
							</a>
						</div>
					<?php } 
				}?>
				<?php echo $summary; ?>
				<?php if(!empty($review['pros_summary'] || $review['cons_summary'])) { ?>
				<div class="mini-pros-cons clearfix">
					<?php if(!empty($review['pros_summary'])) { ?>
					<div class="pros card">
						<h3><i class="fa fa-thumbs-up"></i> Pros</h3>
						<div class="card-body"><?php echo $review['pros_summary']; ?></div>
					</div>	
					<?php } ?>
					<?php if(!empty($review['cons_summary'])) { ?>
					<div class="cons card">
						<h3><i class="fa fa-thumbs-down"></i> Cons</h3>
						<div class="card-body"><?php echo $review['cons_summary']; ?></div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>					
				<div class="buttons mt-3">
						<a class="play-now btn" href="<?php echo $review['affiliate_link'] ?>" target="_blank"><?php echo __('Visit Site','zg'); ?></a>
						<a href="<?php echo get_the_permalink($ID); ?>" class="read-review"><?php echo __('Review','zg'); ?></a>
				</div>

			</div>
			<?php
			$output = ob_get_clean();
			return $output;
        } else {
            // Error message if the review is not found or not of the correct post type
            return 'Review not found';
        }
	}
}
add_shortcode('review-box', 'review_box');


function random_reviews_shortcode() {
	 global $post;
	
	// Get the previously displayed post IDs from the transient
    //$displayed_post_ids = get_transient('custom_related_reviews_displayed_ids');

    // If the transient is empty or not an array, initialize an empty array
   /* if (empty($displayed_post_ids) || !is_array($displayed_post_ids)) {
        $displayed_post_ids = array();
    } */

    // Add the current post ID to the displayed post IDs array
    $displayed_post_ids[] = $post->ID;
	$current_post_categories = wp_get_post_categories($post->ID, array('fields' => 'ids'));

    // Store the updated displayed post IDs in the transient for 24 hours
    set_transient('custom_related_reviews_displayed_ids', $displayed_post_ids, 24 * HOUR_IN_SECONDS);
	
	// Query three random posts from the "review" post type
    $args = array(
        'post_type'      => 'review',
        'posts_per_page' => 3,
        'orderby'        => 'rand',
		'post__not_in' 	=> array($post->ID),
		'category__in'  => $current_post_categories, 
    );
    $query = new WP_Query($args);

    // Start output buffering
    ob_start();

    // Check if there are posts in the query
    if ($query->have_posts()) {
        ?>
        <div class="alternatives">
            <?php
            // Loop through the posts
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <div class="alt-review">
                    <?php
					$review = lastimosa_get_post_option( $post->ID, 'review_options' );
                    // Display post thumbnail
                    if (has_post_thumbnail()) {
                        ?>
                        <div class="review-thumbnail">
                            <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid', 'alt' => get_the_title())); ?>
                        </div>
                        <?php
                    }
                    // Display post title
                    ?>
                    <h3><?php the_title(); ?></h3>
					<?php echo sharp_star_rating($review['rating']); ?>
					<?php echo '<p>'.$review['bonus'].'</p>'; ?>
                    <?php
                    // Display "Read More" button
                    ?>
                    <div class="buttons">
						<a class="play-now btn" href="<?php echo $review['affiliate_link'] ?>" target="_blank"><?php echo __('Visit Site','zg'); ?></a>
						<a href="<?php echo get_the_permalink($post->ID); ?>" class="read-review"><?php echo __('Review','zg'); ?></a>
					</div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php

        // Restore global post data
        wp_reset_postdata();
    } else {
        // No posts found
        ?>
        <p>No reviews found.</p>
        <?php
    }

    // Get the output buffer content and clean the buffer
    $output = ob_get_clean();

    // Return the captured output
    return $output;
}
add_shortcode('random-reviews', 'random_reviews_shortcode');


function post_info($atts = array()) {
	extract(shortcode_atts(array(
     'reviewer' => ''
    ), $atts));
	
	$the_user_id = get_the_author_meta( 'ID' );
	$authorname = get_the_author_meta( 'display_name', $the_user_id );
	$authorfirstname = get_the_author_meta( 'first_name', $the_user_id );
	$default = '';
	$count_user_posts = count_user_posts($the_user_id);
	$gravatar = get_avatar( get_the_author_meta( 'user_email', $the_user_id ), '20', $default, $authorname, array( 'class' => array( 'alignleft' ) ) );
	if(!empty(get_the_author_meta('title',$the_user_id))) { 
		$author_title = ' ('.get_the_author_meta('title',$the_user_id).')';
	}else{
		$author_title = '';
	}
	if(!empty(get_the_author_meta('expertise',$the_user_id))) { 
		$author_expertise = '('.get_the_author_meta('expertise',$the_user_id).')';
	}else{
		$author_expertise = '';
	}
	
	$the_reviewer = get_user_by('login', $reviewer);
	
	if(!empty($the_reviewer)) {
		$the_reviewer_id = $the_reviewer->ID;
		$reviewer_gravatar = get_avatar( get_the_author_meta( 'user_email', $the_reviewer_id ), '20', $default, $authorname, array( 'class' => array( 'alignleft' ) ) );
		$reviewer_authorname = '| ' . __('Reviewed by ','zg') . $reviewer_gravatar . '<a href="'.get_author_posts_url($the_reviewer_id).'">'.get_the_author_meta( 'display_name', $the_reviewer_id ) . '</a>';
	}else{
		$the_reviewer_id = get_the_author_meta( 'ID' );
		$reviewer_authorname = '';
	}

	return '<div class="entry-meta post-info">
				By &nbsp;'. $gravatar.'<span class="theauthor">'. get_the_author_posts_link() . ' '. $reviewer_authorname . ' | <span class="thetime">'. __( 'Updated on ', 'textdomain' ) . get_the_modified_date("M j, Y") .'</span>
			</div><!-- /.entry-meta -->';
}
add_shortcode('post-info', 'post_info'); 


// Enable shortcodes on titles
add_filter( 'the_title', 'do_shortcode' );

// Current year 
function year_shortcode () {
	$year = date_i18n ('Y');
return $year;
}
add_shortcode ('current_year', 'year_shortcode');

// Current month 
function month_shortcode () {
	$month = date_i18n ('F');
return $month;
}
add_shortcode ('current_month', 'month_shortcode');

// Function to generate sitemap for a specific post type
function generate_sitemap($atts) {
    // Extract the post type from shortcode attributes
    $atts = shortcode_atts(array(
        'type' => 'post', // Default post type is 'post'
    ), $atts, 'sitemap');

    // Query for posts of the specified post type
    $query = new WP_Query(array(
        'post_type' => $atts['type'],
        'posts_per_page' => -1, // Get all posts
        'orderby' => 'title',
        'order' => 'ASC'
    ));

    // Check the number of posts found
    $post_count = $query->found_posts;

    // Convert post type to sentence case
    $post_type_singular = ucfirst($atts['type']);

    // Determine the title
    $post_type_title = $post_count > 1 ? sophisticated_pluralize($post_type_singular) : $post_type_singular;

    // Initialize output
    $output = '<h2>' . $post_type_title . '</h2>';
    $output .= '<ul class="sitemap '.$atts['type'].'">';

    // Loop through posts and generate list items
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Apply shortcodes to the title
            $title = do_shortcode(get_the_title());
            $output .= '<li><a href="' . get_permalink() . '">' . $title . '</a></li>';
        }
    } else {
        $output .= '<li>No posts found.</li>';
    }

    // Reset post data
    wp_reset_postdata();

    // Close the list
    $output .= '</ul>';

    return $output;
}

// Register the shortcode
add_shortcode('sitemap', 'generate_sitemap');