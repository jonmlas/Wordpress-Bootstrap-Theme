<?php
/**
 * The template for displaying content in the single.php template.
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title text-center"><?php the_title(); ?></h1>
        <?php echo do_shortcode('[reviews-table-heading]'); ?>
        <?php echo do_shortcode('[post-info reviewer="tombingo"]'); //zg_article_posted_on();  ?>
	</header><!-- /.entry-header -->
	<div class="entry-content">
        <?php echo do_shortcode('[simple-accordion title="Content Navigation"][toc][/simple-accordion]'); ?>
		<?php
			/* if ( has_post_thumbnail() ) :
				echo '<div class="post-thumbnail">' . get_the_post_thumbnail( get_the_ID(), 'large' ) . '</div>';
			endif; */

			the_content();

			wp_link_pages( array( 'before' => '<div class="page-link"><span>' . esc_html__( 'Pages:', 'zg' ) . '</span>', 'after' => '</div>' ) );
		?>
	</div><!-- /.entry-content -->

	<?php
		edit_post_link( __( 'Edit', 'zg' ), '<span class="edit-link">', '</span>' );
	?>

	<footer class="entry-meta">
		<?php echo do_shortcode('[author-box username="amelia-cassiday"]'); ?>
        <?php printf('<h2>%s</h2>', __('User Reviews', 'zg')); ?>
        <?php echo '<div id="user-reviews" class="col-md-9 position-relative">'; ?>
        <?php echo do_shortcode('[bs_button text="Add Review" url="#" class="rounded-pill float-md-end popupreview"]'); ?>
        <?php echo '<div class="box">'.do_shortcode('[site_reviews_summary title="Total Ratings" schema="true"]').'</div>'; ?>
        <?php echo do_shortcode('[site_reviews assigned_posts="post_id" title="Reviews" pagination="ajax" schema="true"]'); ?>
        <?php echo '</div>'; ?>
	</footer><!-- /.entry-meta -->
</article><!-- /#post-<?php the_ID(); ?> -->

