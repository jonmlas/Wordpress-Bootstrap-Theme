<?php

// Remove unnecessary classes from widgets
function optimize_widget_classes($params) {
    $params[0]['before_widget'] = preg_replace('/ id=".*?"/', '', $params[0]['before_widget']); // Remove IDs
    $params[0]['before_widget'] = preg_replace('/ class=".*?"/', '', $params[0]['before_widget']); // Remove classes
    return $params;
}
add_filter('dynamic_sidebar_params', 'optimize_widget_classes');

// Remove WordPress version from the head
remove_action('wp_head', 'wp_generator');

// Remove unnecessary links from the head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');

// Disable emojis to reduce inline styles and scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );
function wps_deregister_styles() {
    wp_dequeue_style( 'wp-block-library' );
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Fire the wp_body_open action.
	 *
	 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
	 *
	 * @since v2.2
	 *
	 * @return void
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}


/**
 * Test if a page is a blog page.
 * if ( is_blog() ) { ... }
 *
 * @since v1.0
 *
 * @global WP_Post $post Global post object.
 *
 * @return bool
 */
function is_blog() {
	global $post;
	$posttype = get_post_type( $post );

	return ( ( is_archive() || is_author() || is_category() || is_home() || is_single() || ( is_tag() && ( 'post' === $posttype ) ) ) ? true : false );
}


/**
 * Disable comments for Media (Image-Post, Jetpack-Carousel, etc.)
 *
 * @since v1.0
 *
 * @param bool $open    Comments open/closed.
 * @param int  $post_id Post ID.
 *
 * @return bool
 */
function zg_filter_media_comment_status( $open, $post_id = null ) {
	$media_post = get_post( $post_id );

	if ( 'attachment' === $media_post->post_type ) {
		return false;
	}

	return $open;
}
add_filter( 'comments_open', 'zg_filter_media_comment_status', 10, 2 );


/**
 * Style Edit buttons as badges: https://getbootstrap.com/docs/5.0/components/badge
 *
 * @since v1.0
 *
 * @param string $link Post Edit Link.
 *
 * @return string
 */
function zg_custom_edit_post_link( $link ) {
	return str_replace( 'class="post-edit-link"', 'class="post-edit-link badge bg-secondary"', $link );
}
add_filter( 'edit_post_link', 'zg_custom_edit_post_link' );


/**
 * Style Edit buttons as badges: https://getbootstrap.com/docs/5.0/components/badge
 *
 * @since v1.0
 *
 * @param string $link Comment Edit Link.
 */
function zg_custom_edit_comment_link( $link ) {
	return str_replace( 'class="comment-edit-link"', 'class="comment-edit-link badge bg-secondary"', $link );
}
add_filter( 'edit_comment_link', 'zg_custom_edit_comment_link' );


/**
 * Responsive oEmbed filter: https://getbootstrap.com/docs/5.0/helpers/ratio
 *
 * @since v1.0
 *
 * @param string $html Inner HTML.
 *
 * @return string
 */
function zg_oembed_filter( $html ) {
	return '<div class="ratio ratio-16x9">' . $html . '</div>';
}
add_filter( 'embed_oembed_html', 'zg_oembed_filter', 10 );

if ( ! function_exists( 'zg_content_nav' ) ) {
	/**
	 * Display a navigation to next/previous pages when applicable.
	 *
	 * @since v1.0
	 *
	 * @param string $nav_id Navigation ID.
	 */
	function zg_content_nav( $nav_id ) {
		global $wp_query;

		if ( $wp_query->max_num_pages > 1 ) {
			?>
			<div id="<?php echo esc_attr( $nav_id ); ?>" class="d-flex mb-4 justify-content-between">
				<div><?php next_posts_link( '<span aria-hidden="true">&larr;</span> ' . esc_html__( 'Older posts', 'zg' ) ); ?></div>
				<div><?php previous_posts_link( esc_html__( 'Newer posts', 'zg' ) . ' <span aria-hidden="true">&rarr;</span>' ); ?></div>
			</div><!-- /.d-flex -->
			<?php
		} else {
			echo '<div class="clearfix"></div>';
		}
	}

	/**
	 * Add Class.
	 *
	 * @since v1.0
	 *
	 * @return string
	 */
	function posts_link_attributes() {
		return 'class="btn btn-secondary btn-lg"';
	}
	add_filter( 'next_posts_link_attributes', 'posts_link_attributes' );
	add_filter( 'previous_posts_link_attributes', 'posts_link_attributes' );
}


/**
 * Template for Password protected post form.
 *
 * @since v1.0
 *
 * @global WP_Post $post Global post object.
 *
 * @return string
 */
function zg_password_form() {
	global $post;
	$label = 'pwbox-' . ( empty( $post->ID ) ? wp_rand() : $post->ID );

	$output                  = '<div class="row">';
		$output             .= '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">';
		$output             .= '<h4 class="col-md-12 alert alert-warning">' . esc_html__( 'This content is password protected. To view it please enter your password below.', 'zg' ) . '</h4>';
			$output         .= '<div class="col-md-6">';
				$output     .= '<div class="input-group">';
					$output .= '<input type="password" name="post_password" id="' . esc_attr( $label ) . '" placeholder="' . esc_attr__( 'Password', 'zg' ) . '" class="form-control" />';
					$output .= '<div class="input-group-append"><input type="submit" name="submit" class="btn btn-primary" value="' . esc_attr__( 'Submit', 'zg' ) . '" /></div>';
				$output     .= '</div><!-- /.input-group -->';
			$output         .= '</div><!-- /.col -->';
		$output             .= '</form>';
	$output                 .= '</div><!-- /.row -->';

	return $output;
}
add_filter( 'the_password_form', 'zg_password_form' );


if ( ! function_exists( 'zg_comment' ) ) {
	/**
	 * Style Reply link.
	 *
	 * @since v1.0
	 *
	 * @param string $class Link class.
	 *
	 * @return string
	 */
	function zg_replace_reply_link_class( $class ) {
		return str_replace( "class='comment-reply-link", "class='comment-reply-link btn btn-outline-secondary", $class );
	}
	add_filter( 'comment_reply_link', 'zg_replace_reply_link_class' );

	/**
	 * Template for comments and pingbacks:
	 * add function to comments.php ... wp_list_comments( array( 'callback' => 'zg_comment' ) );
	 *
	 * @since v1.0
	 *
	 * @param object $comment Comment object.
	 * @param array  $args    Comment args.
	 * @param int    $depth   Comment depth.
	 */
	function zg_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback':
			case 'trackback':
				?>
		<li class="post pingback">
			<p>
				<?php
					esc_html_e( 'Pingback:', 'zg' );
					comment_author_link();
					edit_comment_link( esc_html__( 'Edit', 'zg' ), '<span class="edit-link">', '</span>' );
				?>
			</p>
				<?php
				break;
			default:
				?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php
							$avatar_size = ( '0' !== $comment->comment_parent ? 68 : 136 );
							echo get_avatar( $comment, $avatar_size );

							/* Translators: 1: Comment author, 2: Date and time */
							printf(
								wp_kses_post( __( '%1$s, %2$s', 'zg' ) ),
								sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
								sprintf(
									'<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
									esc_url( get_comment_link( $comment->comment_ID ) ),
									get_comment_time( 'c' ),
									/* Translators: 1: Date, 2: Time */
									sprintf( esc_html__( '%1$s ago', 'zg' ), human_time_diff( (int) get_comment_time( 'U' ), current_time( 'timestamp' ) ) )
								)
							);

							edit_comment_link( esc_html__( 'Edit', 'zg' ), '<span class="edit-link">', '</span>' );
						?>
					</div><!-- .comment-author .vcard -->

					<?php if ( '0' === $comment->comment_approved ) { ?>
						<em class="comment-awaiting-moderation">
							<?php esc_html_e( 'Your comment is awaiting moderation.', 'zg' ); ?>
						</em>
						<br />
					<?php } ?>
				</footer>

				<div class="comment-content"><?php comment_text(); ?></div>

				<div class="reply">
					<?php
						comment_reply_link(
							array_merge(
								$args,
								array(
									'reply_text' => esc_html__( 'Reply', 'zg' ) . ' <span>&darr;</span>',
									'depth'      => $depth,
									'max_depth'  => $args['max_depth'],
								)
							)
						);
					?>
				</div><!-- /.reply -->
			</article><!-- /#comment-## -->
				<?php
				break;
		endswitch;
	}

	/**
	 * Custom Comment form.
	 *
	 * @since v1.0
	 * @since v1.1: Added 'submit_button' and 'submit_field'
	 * @since v2.0.2: Added '$consent' and 'cookies'
	 *
	 * @param array $args    Form args.
	 * @param int   $post_id Post ID.
	 *
	 * @return array
	 */
	function zg_custom_commentform( $args = array(), $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		$commenter     = wp_get_current_commenter();
		$user          = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		$args = wp_parse_args( $args );

		$req      = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true' required" : '' );
		$consent  = ( empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"' );
		$fields   = array(
			'author'  => '<div class="form-floating mb-3">
							<input type="text" id="author" name="author" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . esc_html__( 'Name', 'zg' ) . ( $req ? '*' : '' ) . '"' . $aria_req . ' />
							<label for="author">' . esc_html__( 'Name', 'zg' ) . ( $req ? '*' : '' ) . '</label>
						</div>',
			'email'   => '<div class="form-floating mb-3">
							<input type="email" id="email" name="email" class="form-control" value="' . esc_attr( $commenter['comment_author_email'] ) . '" placeholder="' . esc_html__( 'Email', 'zg' ) . ( $req ? '*' : '' ) . '"' . $aria_req . ' />
							<label for="email">' . esc_html__( 'Email', 'zg' ) . ( $req ? '*' : '' ) . '</label>
						</div>',
			'url'     => '',
			'cookies' => '<p class="form-check mb-3 comment-form-cookies-consent">
							<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" class="form-check-input" type="checkbox" value="yes"' . $consent . ' />
							<label class="form-check-label" for="wp-comment-cookies-consent">' . esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'zg' ) . '</label>
						</p>',
		);

		$defaults = array(
			'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
			'comment_field'        => '<div class="form-floating mb-3">
											<textarea id="comment" name="comment" class="form-control" aria-required="true" required placeholder="' . esc_attr__( 'Comment', 'zg' ) . ( $req ? '*' : '' ) . '"></textarea>
											<label for="comment">' . esc_html__( 'Comment', 'zg' ) . '</label>
										</div>',
			/** This filter is documented in wp-includes/link-template.php */
			'must_log_in'          => '<p class="must-log-in">' . sprintf( wp_kses_post( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'zg' ) ), wp_login_url( esc_url( get_the_permalink( get_the_ID() ) ) ) ) . '</p>',
			/** This filter is documented in wp-includes/link-template.php */
			'logged_in_as'         => '<p class="logged-in-as">' . sprintf( wp_kses_post( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'zg' ) ), get_edit_user_link(), $user->display_name, wp_logout_url( apply_filters( 'the_permalink', esc_url( get_the_permalink( get_the_ID() ) ) ) ) ) . '</p>',
			'comment_notes_before' => '<p class="small comment-notes">' . esc_html__( 'Your Email address will not be published.', 'zg' ) . '</p>',
			'comment_notes_after'  => '',
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'class_submit'         => 'btn btn-primary',
			'name_submit'          => 'submit',
			'title_reply'          => '',
			'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'zg' ),
			'cancel_reply_link'    => esc_html__( 'Cancel reply', 'zg' ),
			'label_submit'         => esc_html__( 'Post Comment', 'zg' ),
			'submit_button'        => '<input type="submit" id="%2$s" name="%1$s" class="%3$s" value="%4$s" />',
			'submit_field'         => '<div class="form-submit">%1$s %2$s</div>',
			'format'               => 'html5',
		);

		return $defaults;
	}
	add_filter( 'comment_form_defaults', 'zg_custom_commentform' );
}

function set_default_featured_image() {
    global $post;

    // Check if the current post has a featured image
    if (is_singular() && !has_post_thumbnail($post->ID)) {
        // Get the default image ID by URL
        $default_image_url = get_template_directory_uri() . '/assets/images/default.jpg';
        $default_image_id = attachment_url_to_postid($default_image_url);

        // If the image is not in the media library, add it
        if (!$default_image_id) {
            // Insert the default image into the media library
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($default_image_url);
            $filename = basename($default_image_url);
            $file = wp_mkdir_p($upload_dir['path']) ? $upload_dir['path'] . '/' . $filename : $upload_dir['basedir'] . '/' . $filename;
            file_put_contents($file, $image_data);

            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $default_image_id = wp_insert_attachment($attachment, $file, $post->ID);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($default_image_id, $file);
            wp_update_attachment_metadata($default_image_id, $attach_data);
        }

        // Set the default image as the featured image for the current post
        set_post_thumbnail($post->ID, $default_image_id);
    }
}
add_action('wp', 'set_default_featured_image');


/**
 * Automatically set the image ALT text to the file name (converted to title case) upon upload.
 *
 * @param int $attachment_ID The ID of the uploaded attachment.
 * @return void
 */
function set_image_filename_as_alt_text( $attachment_ID ) {
    // Check if the uploaded item is an image
    $mime_type = get_post_mime_type( $attachment_ID );
    if ( strpos( $mime_type, 'image/' ) !== 0 ) {
        return; // Exit if not an image
    }

    // Get the attachment file path
    $file_path = get_attached_file( $attachment_ID );

    // Proceed only if file path is valid
    if ( ! $file_path || ! file_exists( $file_path ) ) {
        return;
    }

    // Extract filename without extension
    $filename = pathinfo( $file_path, PATHINFO_FILENAME );

    // Format the filename: replace dashes/underscores, trim, and convert to title case
    $alt_text = ucwords( str_replace( [ '-', '_' ], ' ', trim( $filename ) ) );

    // Update the image ALT text (if not already set)
    if ( ! get_post_meta( $attachment_ID, '_wp_attachment_image_alt', true ) ) {
        update_post_meta( $attachment_ID, '_wp_attachment_image_alt', $alt_text );
    }
}
add_action( 'add_attachment', 'set_image_filename_as_alt_text' );


/**
 * Add a "Thumbnail" column to the post list in the admin area.
 */
function custom_post_thumbnail_column( $columns ) {
    $new_columns = [];

    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;

        if ( 'title' === $key ) {
            $new_columns['thumbnail'] = __( 'Thumbnail', 'zg-theme' );
        }
    }

    return $new_columns;
}
add_filter( 'manage_posts_columns', 'custom_post_thumbnail_column' );

/**
 * Display thumbnails (including SVG) in the custom "Thumbnail" column.
 */
function custom_post_thumbnail_column_content( $column_name, $post_id ) {
    if ( $column_name === 'thumbnail' ) {
        $thumb_id = get_post_thumbnail_id( $post_id );

        if ( $thumb_id ) {
            $mime = get_post_mime_type( $thumb_id );
            $url  = wp_get_attachment_url( $thumb_id );

            if ( $mime === 'image/svg+xml' ) {
                // Output inline SVG or fallback <img>
                echo '<img src="' . esc_url( $url ) . '" style="width:60px; height:auto;" />';
            } else {
                // Use standard WP thumbnail
                echo get_the_post_thumbnail( $post_id, [60, 60] );
            }
        } else {
            echo '—'; // No thumbnail set
        }
    }
}
add_action( 'manage_posts_custom_column', 'custom_post_thumbnail_column_content', 10, 2 );



// Add extra fields to user profile
function zg_extra_user_profile_fields($user) { ?>
	<h3><?php _e("Full Biographical Info", "zg"); ?></h3>
	<table class="form-table">
		<?php
		$fields = [
			[
				'id' => 'title',
				'label' => __("Title", "zg"),
				'type' => 'text',
				'desc' => __("Enter user title", "zg"),
			],
			[
				'id' => 'twitter',
				'label' => __("Twitter", "zg"),
				'type' => 'text',
				'desc' => __("Twitter URL", "zg"),
			],
			[
				'id' => 'linkedin',
				'label' => __("Linkedin", "zg"),
				'type' => 'text',
				'desc' => __("Linkedin URL", "zg"),
			],
		];
		foreach ($fields as $field) { ?>
			<tr>
				<th><label for="<?php echo esc_attr($field['id']); ?>"><?php echo $field['label']; ?></label></th>
				<td>
					<input type="text" name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" value="<?php echo esc_attr(get_the_author_meta($field['id'], $user->ID)); ?>" class="regular-text" /><br />
					<span class="description"><?php echo $field['desc']; ?></span>
				</td>
			</tr>
		<?php }
		// Expertise editor
		?>
		<tr>
			<th><label for="expertise"><?php _e("Expertise", "zg"); ?></label></th>
			<td>
				<?php
				wp_editor(get_the_author_meta('expertise', $user->ID), 'expertise_editor_box', [
					'tinymce' => true,
					'textarea_name' => 'expertise',
					'media_buttons' => true,
					'editor_height' => 150,
					'teeny' => false
				]);
				?>
				<span class="description"><?php _e("Please enter your expertise.", "zg"); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="full_bio"><?php _e("Full Bio", "zg"); ?></label></th>
			<td>
				<?php
				wp_editor(get_the_author_meta('full_bio', $user->ID), 'full_bio_editor_box', [
					'tinymce' => true,
					'textarea_name' => 'full_bio',
					'media_buttons' => true,
					'editor_height' => 350,
					'teeny' => false
				]);
				?>
				<span class="description"><?php _e("Please enter your full biographical info.", "zg"); ?></span>
			</td>
		</tr>
	</table>
<?php }
add_action('show_user_profile', 'zg_extra_user_profile_fields');
add_action('edit_user_profile', 'zg_extra_user_profile_fields');

// Save extra user profile fields
function zg_save_extra_user_profile_fields($user_id) {
	if (!current_user_can('edit_user', $user_id)) {
		return false;
	}
	$fields = ['title', 'expertise', 'full_bio', 'twitter', 'linkedin'];
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			update_user_meta($user_id, $field, wp_kses_post($_POST[$field]));
		}
	}
}
add_action('personal_options_update', 'zg_save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'zg_save_extra_user_profile_fields');

add_filter( 'get_the_archive_title', function ($title) {    
	if ( is_category() ) {    
		$title = single_cat_title( '', false );    
	} elseif ( is_tag() ) {    
		$title = single_tag_title( '', false );    
	} elseif ( is_author() ) {    
		$title = '<span class="vcard">' . get_the_author() . '</span>' ;    
	} elseif ( is_tax() ) { //for custom post types
		$title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
	} elseif (is_post_type_archive()) {
		$title = post_type_archive_title( '', false );
	}
	return $title;    
});

function wpse_media_extra_column( $cols ) {
    $cols["alt"] = "ALT";
    return $cols;
}
function wpse_media_extra_column_value( $column_name, $id ) {
    if( $column_name == 'alt' )
        echo get_post_meta( $id, '_wp_attachment_image_alt', true);
}
add_filter( 'manage_media_columns', 'wpse_media_extra_column' );
add_action( 'manage_media_custom_column', 'wpse_media_extra_column_value', 10, 2 );

// Add body class when main-table shortcode is present
function add_main_table_body_class( $classes ) {
    // Check if main-table shortcode exists in the content
	if (is_404()) {
		$classes[] = 'error-404'; // Add the class 'error404' to the array of body classes
		
	} else {
		if ( has_shortcode( get_post()->post_content, 'main-table' ) ) {
			// Add the body class
			$classes[] = 'main-table-page';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'add_main_table_body_class' ); 

// Add custom settings page for 410 Pages
function custom_410_pages_settings_page() {
    add_options_page(
        '410 Pages Settings',
        '410 Pages',
        'manage_options',
        '410-pages-settings',
        'custom_render_410_pages_settings_page'
    );
}
add_action('admin_menu', 'custom_410_pages_settings_page');


// Render the custom settings page
function custom_render_410_pages_settings_page() {
    ?>
    <div class="wrap">
        <h1>410 Pages Settings</h1>
		<p>
			This adds the urls to the 410 Gone status.
		</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_410_pages_settings');
            do_settings_sections('custom_410_pages_settings');
            ?>
            <label for="410_pages_urls">Enter URLs(one per line):</label>
            <textarea id="410_pages_urls" name="410_pages_urls" rows="25" cols="100"><?php echo esc_attr(get_option('410_pages_urls')); ?></textarea>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}


// Register settings and sanitize input
function custom_register_410_pages_settings() {
    register_setting('custom_410_pages_settings', '410_pages_urls', 'custom_sanitize_410_pages_urls');
}
add_action('admin_init', 'custom_register_410_pages_settings');


// Sanitize input for 410 Pages URLs
function custom_sanitize_410_pages_urls($input) {
    return sanitize_textarea_field($input);
}


// Add custom body class and set HTTP status code for specific 404 pages based on saved URLs
function custom_specific_404_body_class($classes) {
    $saved_urls = get_option('410_pages_urls');

    // Check if the saved URLs exist and are not empty
    if (!empty($saved_urls)) {
        // Convert saved URLs string to array
        $specific_urls = explode("\n", $saved_urls);

        // Trim whitespace and remove empty elements
        $specific_urls = array_map('trim', $specific_urls);
        $specific_urls = array_filter($specific_urls);

        // Get current URL
        $current_url = home_url($_SERVER['REQUEST_URI']);

        // Check if the current URL is in the array of specific URLs
        if (in_array($current_url, $specific_urls)) {
            // Add custom class to the body
            $classes[] = 'error410';
			
			// Remove 'error404' class if it exists
            $classes = array_diff($classes, array('error404'));

            // Set HTTP status code to 410 (Gone)
            status_header(410);
        }
    }
    return $classes;
}
add_filter('body_class', 'custom_specific_404_body_class');


/**
 * SVG Upload and Handling Functions
 *
 * @param string $word The word to pluralize.
 * @return string The pluralized word.
 */
// Allow SVG Uploads
function allow_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_uploads');

// Sanitize SVG on Upload
function sanitize_svg_on_upload($file) {
    if (
        isset($file['type']) &&
        $file['type'] === 'image/svg+xml' &&
        isset($file['tmp_name']) &&
        file_exists($file['tmp_name'])
    ) {
        $dirty_svg = file_get_contents($file['tmp_name']);

        // Remove script tags and on* attributes
        $clean_svg = preg_replace([
            '#<script(.*?)>(.*?)</script>#is', // remove <script> tags
            '#on\w+="[^"]*"#i',               // remove on* attributes (e.g., onclick)
            "#<\?php(.*?)\?>#is",             // remove PHP tags
        ], '', $dirty_svg);

        // Replace the contents with cleaned SVG
        file_put_contents($file['tmp_name'], $clean_svg);
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'sanitize_svg_on_upload');

// Allow SVG uploads
add_filter('upload_mimes', function($mimes) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
});

// Extract dimensions from SVG
function my_svg_dimensions($file) {
    $svg = simplexml_load_file($file);
    if (!$svg) {
        return [100, 100]; // fallback
    }

    $attributes = $svg->attributes();
    $width  = isset($attributes->width)  ? floatval($attributes->width)  : 0;
    $height = isset($attributes->height) ? floatval($attributes->height) : 0;

    // Fallback to viewBox if width/height missing
    if ((!$width || !$height) && isset($attributes->viewBox)) {
        $viewbox = explode(' ', (string) $attributes->viewBox);
        if (count($viewbox) === 4) {
            $width  = floatval($viewbox[2]);
            $height = floatval($viewbox[3]);
        }
    }

    return [
        $width  ?: 100,
        $height ?: 100
    ];
}

// Treat SVG like normal image in metadata
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    $mime = get_post_mime_type($attachment_id);

    if ($mime === 'image/svg+xml') {
        $file = get_attached_file($attachment_id);
        list($width, $height) = my_svg_dimensions($file);

        $metadata = [
            'width'  => $width,
            'height' => $height,
            'file'   => _wp_relative_upload_path($file),
            'sizes'  => [] // no raster sizes generated
        ];
    }

    return $metadata;
}, 10, 2);

// Make wp_get_attachment_image() work with SVG
add_filter('wp_get_attachment_image_src', function($image, $attachment_id, $size, $icon) {
    $mime = get_post_mime_type($attachment_id);

    if ($mime === 'image/svg+xml') {
        $file = get_attached_file($attachment_id);
        list($width, $height) = my_svg_dimensions($file);

        $image = [
            wp_get_attachment_url($attachment_id),
            $width,
            $height,
            true // is_intermediate
        ];
    }

    return $image;
}, 10, 4);
// End of SVG upload and handling code