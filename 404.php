<?php
/**
 * Template Name: Not found
 * Description: Page template 404 Not found.
 *
 */

get_header();

$search_enabled = get_theme_mod( 'search_enabled', '1' ); // Get custom meta-value.
?>
<div id="post-0" class="content error404 not-found">
	<div class="entry-content pb-5">
		<div class="container">
			<div class="error">
				<div class="container-floud">
					<div class="col-xs-12 ground-color text-center">
						<div class="container-error-404">
							<div class="clip"><div class="shadow"><span class="digit thirdDigit"></span></div></div>
							<div class="clip"><div class="shadow"><span class="digit secondDigit"></span></div></div>
							<div class="clip"><div class="shadow"><span class="digit firstDigit"></span></div></div>
							<div class="msg">OH!<span class="triangle"></span></div>
						</div>
						<h1 class="entry-title my-5"><?php esc_html_e( 'Sorry! Page not found', 'zg' ); ?></h1>
					</div>
				</div>
			</div>
			<div>
				<?php
					get_search_form();
				?>
			</div>
			<div class="text-center mt-5">
				<p><strong><?php echo __( 'Oops! Looks like you’ve dabbed the wrong spot. Let’s get you back to the game.<br/>
			Check out these popular pages below.', 'zg' ); ?></strong></p>
			</div>
			<div>
				<?php if (function_exists('wp_nav_menu')) {
					echo '<div class="text-center"><h2 class="entry-title">' . __( 'Popular Pages', 'zg' ) . '</h2></div>';
					wp_nav_menu(array(
						'menu' => 66, // ID of the menu you want to display
						'container' => 'nav', // HTML container tag (you can change this)
						'container_class' => 'popular-pages', // CSS class of the container (optional)
						'menu_class' => 'menu', // CSS class for the ul element (optional)
						'fallback_cb' => false // No fallback if the menu doesn't exist
					));
				} else {
					
				} ?>
			</div>
		</div>
	</div><!-- /.entry-content -->
	<footer class="entry-meta pb-5">
		<div class="container">
			<div class="row">
				<div class="col-md-4 mb-3">
					<a href="https://www.bingosites.co.uk/" class="d-block box pt-5 bg-blue h-100">
						<i class="fas fa-home h2"></i>
						<h3>Take me back home</h3>
					</a>
				</div>
				<div class="col-md-4 mb-3">
					<a href="https://www.bingosites.co.uk/blog/" class="d-block box pt-5 bg-blue h-100">
						<i class="fas fa-dot-circle h2"></i>
						<h3>Checkout current games</h3>
					</a>
				</div>
				<div class="col-md-4 mb-3">
					<a href="https://www.bingosites.co.uk/contact-us/" class="d-block box pt-5 bg-blue h-100">
						<i class="fas fa-phone-alt h2"></i>
						<h3>Need Help?</h3>
					</a>
				</div>
			</div>
		</div>
	</footer>
</div><!-- /#post-0 -->
<?php
get_footer();
