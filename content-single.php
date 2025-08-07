<?php
/**
 * The template for displaying content in the single.php template.
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php
			if ( 'post' === get_post_type() ) :
		?>
				<?php echo do_shortcode('[post-info reviewer="tombingo"]'); //zg_article_posted_on();  ?>
		<?php
			endif;
		?>
	</header><!-- /.entry-header -->
	<div class="entry-content">
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
		<?php echo do_shortcode('[related_posts]'); ?>
	</footer><!-- /.entry-meta -->
</article><!-- /#post-<?php the_ID(); ?> -->

