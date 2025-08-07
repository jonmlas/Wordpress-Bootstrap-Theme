<?php
function zg_theme_setup() {
    // Load text domain for translations
    // load_theme_textdomain('zg-theme', get_template_directory() . '/languages'); 

    // Theme Support.
    add_theme_support( 'title-tag' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
            'navigation-widgets',
        )
    );
    	
    // Add support for Block Styles.
    add_theme_support( 'wp-block-styles' );
    // Add support for full and wide alignment.
    add_theme_support( 'align-wide' );
    // Add support for Editor Styles.
    add_theme_support( 'editor-styles' );
    // Enqueue Editor Styles.
    //add_editor_style( 'style-editor.css' );

	// Disable Block Directory: https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/filters/editor-filters.md#block-directory
	remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
	remove_action( 'enqueue_block_editor_assets', 'gutenberg_enqueue_block_editor_assets_block_directory' );

    // Enqueue Editor Styles.
    if ( is_admin() ) {
        wp_enqueue_style( 'editor-style', get_theme_file_uri( 'style-editor.css' ) );
    }
    //add_action( 'enqueue_block_assets', 'zg_load_editor_styles' );

    add_image_size( 'main-table-logo', 320, 220, true ); // (cropped)
	add_image_size( 'review-box', 440, 440, true ); // (cropped)
}
add_action('after_setup_theme', 'zg_theme_setup');

function wpc_disable_all_image_cropping( $sizes ) {
	foreach ( $sizes as $key => $size ) {
		$sizes[$key]['crop'] = false;
	}
	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'wpc_disable_all_image_cropping' );
 
/**
 * Loading All CSS Stylesheets and Javascript Files.
 *
 * @since v1.0
 *
 * @return void
 */
function zg_theme_enqueue_scripts() {
    $theme_version = wp_get_theme()->get( 'Version' );

	// Enqueue Bootstrap CSS
    wp_enqueue_style(
        'bootstrap', 
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', 
        array(), 
        '5.3.3', 
        'all'
    );
    wp_style_add_data('bootstrap-css', 'integrity', 'sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC');
    wp_style_add_data('bootstrap-css', 'crossorigin', 'anonymous');

    // Enqueue Bootstrap JS Bundle
    wp_enqueue_script(
        'bootstrap', 
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', 
        array(), 
        '5.3.3', 
        true
    );
    wp_script_add_data('bootstrap-js', 'integrity', 'sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM');
    wp_script_add_data('bootstrap-js', 'crossorigin', 'anonymous');
	
	/*
	// Preconnect to the Google Fonts API
    wp_enqueue_script(
        'google-fonts-preconnect',
        'https://fonts.googleapis.com',
        array(),
        null,
        false
    );

    // Preconnect to the Google Fonts Static
    wp_enqueue_script(
        'google-fonts-gstatic-preconnect',
        'https://fonts.gstatic.com',
        array(),
        null,
        false
    );
	*/

	// Preconnect to Google Fonts domains
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

	wp_enqueue_style(
		'barlow-google-font',
		'https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,400;0,700;1,400;1,700&display=swap',
		[],
		null
	);
	
	// Enqueue the Font Awesome kit script
	wp_enqueue_script('font-awesome-kit', 'https://kit.fontawesome.com/9b0e88a93e.js', array(), null, false);

	// Add the crossorigin attribute to the script tag
	add_filter('script_loader_tag', function($tag, $handle) {
		if ('font-awesome-kit' !== $handle) {
			return $tag;
		}
		return str_replace(' src', ' crossorigin="anonymous" src', $tag);
	}, 10, 2);
	
	wp_enqueue_script( 'scripts', get_theme_file_uri( 'assets/scripts.js' ), array(), $theme_version, true );

	if ( is_rtl() ) {
		//wp_enqueue_style( 'rtl', get_theme_file_uri( 'build/rtl.css' ), array(), $theme_version, 'all' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action('wp_enqueue_scripts', 'zg_theme_enqueue_scripts');

define( 'FILTERPRIORITY', 10 );
add_filter( 'the_content', 'do_shortcode', FILTERPRIORITY );
add_filter( 'widget_text', 'do_shortcode', FILTERPRIORITY );


/**
 * Automatically load all PHP files in the /inc folder,
 * excluding any file that ends with '-temp.php'.
 *
 * This allows modular organization while avoiding temporary or dev-only files.
 */
foreach ( glob( __DIR__ . '/inc/*.php' ) as $file ) {
	if ( is_readable( $file ) && ! preg_match( '/-temp\.php$/', $file ) ) {
		require_once $file;
	}
}


/**
 * Use namespaced data attribute for Bootstrap's dropdown toggles.
 *
 * @param array    $atts HTML attributes applied to the item's `<a>` element.
 * @param WP_Post  $item The current menu item.
 * @param stdClass $args An object of wp_nav_menu() arguments.
 * @return array
 */
function prefix_bs5_dropdown_data_attribute( $atts, $item, $args ) {
    if ( is_a( $args->walker, 'WP_Bootstrap_Navwalker' ) ) {
        if ( array_key_exists( 'data-toggle', $atts ) ) {
            unset( $atts['data-toggle'] );
            $atts['data-bs-toggle'] = 'dropdown';
        }
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'prefix_bs5_dropdown_data_attribute', 20, 3 );


function tooltipster() {
	wp_enqueue_style( 'tooltipster', get_stylesheet_directory_uri() . '/css/tooltipster.bundle.min.css' );
	wp_enqueue_script( 'tooltipster', get_stylesheet_directory_uri() . '/js/tooltipster.bundle.min.js', array('jquery'), null, true );
	wp_add_inline_script( 'tooltipster', '
		jQuery(document).ready(function($) {
			$(\'.tooltip\').tooltipster({
				contentCloning: true,
				side: \'bottom\',
				interactive: true,
				trigger: \'hover\'
			});
		});
	');
}
add_action( 'wp_enqueue_scripts', 'tooltipster', 10 );



