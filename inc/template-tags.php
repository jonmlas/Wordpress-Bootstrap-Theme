<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package point
 */

// Navigation between posts
if ( ! function_exists( 'zg_post_navigation' ) ) :
function zg_post_navigation() {
    ?>
    <nav class="navigation posts-navigation" role="navigation">
        <?php $zg_nav_type = get_theme_mod('zg_pagination_type');
        if (!empty($zg_nav_type)) {
            echo get_the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( 'Newer', 'zg' ),
                'next_text' => __( 'Older', 'zg' ),
            ) );
        } else { ?>
            <h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'zg' ); ?></h2>
            <div class="pagination nav-links">
                <?php if ( get_next_posts_link() ) : ?>
                    <div class="nav-previous"><?php next_posts_link( '<i class="point-icon icon-left"></i>'.__( ' Older posts', 'zg' ) ); ?></div>
                <?php endif; ?>
                <?php if ( get_previous_posts_link() ) : ?>
                    <div class="nav-next"><?php previous_posts_link( __( 'Newer posts ', 'zg' ).' <i class="point-icon icon-right"></i>' ); ?></div>
                <?php endif; ?>
            </div>
        <?php } ?>
    </nav>
    <?php
}
endif;

function render_star_rating( $rating ) {
	$rating = floatval( $rating );
	$full_stars = floor( $rating );
	$half_star = ( $rating - $full_stars ) >= 0.5 ? 1 : 0;
	$empty_stars = 5 - $full_stars - $half_star;

	ob_start(); // Start output buffering
	echo '<div class="rating">';

	// Full stars
	for ( $i = 0; $i < $full_stars; $i++ ) {
		echo '<i class="fa fa-star" aria-hidden="true"></i>';
	}

	// Half star
	if ( $half_star ) {
		echo '<i class="fa fa-star-half-stroke" aria-hidden="true"></i>';
	}

	// Empty stars
	for ( $i = 0; $i < $empty_stars; $i++ ) {
		echo '<i class="fa-regular fa-star" aria-hidden="true"></i>';
	}

	// Show numeric rating
	echo ' ' . esc_html( $rating ) . '/5';

	echo '</div>';
	return ob_get_clean(); // Return buffered output
}


// Post meta info
if ( ! function_exists( 'zg_posted_on' ) ) :
function zg_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }
    $time_string = sprintf( $time_string,
        esc_attr( get_the_date( 'c' ) ),
        esc_html( get_the_date() ),
        esc_attr( get_the_modified_date( 'c' ) ),
        esc_html( get_the_modified_date() )
    );

    $posted_on = sprintf(
        esc_html_x( 'Posted on %s', 'post date', 'zg' ),
        '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );

    $byline = sprintf(
        esc_html_x( 'by %s', 'post author', 'zg' ),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>';
}
endif;

// Entry footer meta
if ( ! function_exists( 'zg_entry_footer' ) ) :
function zg_entry_footer() {
    if ( 'post' === get_post_type() ) {
        $categories_list = get_the_category_list( ', ' );
        if ( $categories_list && zg_categorized_blog() ) {
            printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'zg' ) . '</span>', $categories_list );
        }
        $tags_list = get_the_tag_list( '', ', ');
        if ( $tags_list ) {
            printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'zg' ) . '</span>', $tags_list );
        }
    }
    if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
        echo '<span class="comments-link">';
        comments_popup_link( esc_html__( 'Leave a comment', 'zg' ), esc_html__( '1 Comment', 'zg' ), esc_html__( '% Comments', 'zg' ) );
        echo '</span>';
    }
    edit_post_link(
        sprintf(
            esc_html__( 'Edit %s', 'zg' ),
            the_title( '<span class="screen-reader-text">"', '"</span>', false )
        ),
        '<span class="edit-link">',
        '</span>'
    );
}
endif;

// Returns true if blog has more than 1 category
function zg_categorized_blog() {
    $all_cats = get_transient( 'zg_categories' );
    if ( false === $all_cats ) {
        $all_cats = count( get_categories( array(
            'fields'     => 'ids',
            'hide_empty' => 1,
            'number'     => 2,
        ) ) );
        set_transient( 'zg_categories', $all_cats );
    }
    return $all_cats > 1;
}

// Flush out the transients used in zg_categorized_blog
function zg_category_transient_flusher() {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    delete_transient( 'zg_categories' );
}
add_action( 'edit_category', 'zg_category_transient_flusher' );
add_action( 'save_post',     'zg_category_transient_flusher' );
