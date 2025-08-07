			<?php
				// If Single or Archive (Category, Tag, Author or a Date based page).
			/*	if ( is_single() || is_archive() ) :
			?>
					</div><!-- /.col -->

					<?php
						get_sidebar();
					?>

				</div><!-- /.row -->
			<?php
				endif;  */
			?>
		</main><!-- /#main -->
		<footer id="footer">
			<section>
				<div class="container">
					<div class="row gx-1">
						<?php
						if ( is_active_sidebar( 'footer_1' ) ) :
						?>
							<div class="col-md-5">
								<?php
									dynamic_sidebar( 'footer_1' );
								?>
							</div>
						<?php
						endif;
						?>

						<?php
						if ( is_active_sidebar( 'footer_2' ) ) :
						?>
							<div class="col-md-7">
								<?php
									dynamic_sidebar( 'footer_2' );
								?>
							</div>
						<?php
						endif;
						?>
					</div><!-- /.row -->	
				</div><!-- /.container -->
			</section>
			<section>
				<div class="container">
					<div class="row gx-1">
						<?php
						if ( is_active_sidebar( 'footer_3' ) ) :
						?>
							<div class="col-md-2">
								<?php
									dynamic_sidebar( 'footer_3' );
								?>
							</div>
						<?php
						endif;
						?>

						<?php
						if ( is_active_sidebar( 'footer_4' ) ) :
						?>
							<div class="col-md-2">
								<?php
									dynamic_sidebar( 'footer_4' );
								?>
							</div>
						<?php
						endif;
						?>

						<?php
						if ( is_active_sidebar( 'footer_5' ) ) :
						?>
							<div class="col-md-2">
								<?php
									dynamic_sidebar( 'footer_5' );
								?>
							</div>
						<?php
						endif;
						?>
						
						<?php
						if ( is_active_sidebar( 'footer_6' ) ) :
						?>
							<div class="col-md-6">
								<?php
									dynamic_sidebar( 'footer_6' );
								?>
							</div>
						<?php
						endif;
						?>
					</div>
					<div class="row gx-1">
						<?php
						if ( is_active_sidebar( 'footer_7' ) ) :
						?>
							<div class="col-md-12 pt-0">
								<?php
									dynamic_sidebar( 'footer_7' );
								?>
							</div>
						<?php
						endif;
						?>
					</div>
				</div>
			</section>
			<section>
				<div class="container">
					<div class="row gx-1">
	
						<?php
							if ( current_user_can( 'manage_options' ) ) :
							?>
								<span class="edit-link"><a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>" class="badge bg-secondary"><?php esc_html_e( 'Edit', 'zg' ); ?></a></span><!-- Show Edit Widget link -->
							<?php
							endif;
							?>

							<?php
							if ( has_nav_menu( 'footer-menu' ) ) : // See function register_nav_menus() in functions.php
								/*
									Loading WordPress Custom Menu (theme_location) ... remove <div> <ul> containers and show only <li> items!!!
									Menu name taken from functions.php!!! ... register_nav_menu( 'footer-menu', 'Footer Menu' );
									!!! IMPORTANT: After adding all pages to the menu, don't forget to assign this menu to the Footer menu of "Theme locations" /wp-admin/nav-menus.php (on left side) ... Otherwise the themes will not know, which menu to use!!!
								*/
								wp_nav_menu(
									array(
										'container'       => 'nav',
										'container_class' => 'col-md-6',
										//'fallback_cb'     => 'WP_Bootstrap4_Navwalker_Footer::fallback',
										'walker'          => new WP_Bootstrap4_Navwalker_Footer(),
										'theme_location'  => 'footer-menu',
										'items_wrap'      => '<ul class="menu nav justify-content-end">%3$s</ul>',
									)
								);
							endif;
							?>
					</div><!-- /.row -->
					<div class="col-md-12 pt-4">
						<?php $copyright = get_theme_mod( 'zg_footer_copyright' );
						if ( $copyright ) {
							echo '<p class="footer-copyright">' . esc_html( $copyright ) . '</p>';
						} ?>
					</div>
				</div>
			</section>
		</footer><!-- /#footer -->
	</div><!-- /#wrapper -->
	<?php
		wp_footer();
	?>
</body>
</html>
