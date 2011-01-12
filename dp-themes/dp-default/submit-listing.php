<?php
/*
Template Name: Submit Listing
*/
?>

<?php get_header(); ?>

		<div id="container">
			<div id="content" role="main">

            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 class="entry-title"><?php the_title(); ?></h1>

                    <?php locate_template( array('checkout/checkout.php'), true ); ?>
                    
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'directorypress' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
                    
				</div><!-- #post-## -->

				<?php comments_template( '', true ); ?>

            <?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_footer(); ?>