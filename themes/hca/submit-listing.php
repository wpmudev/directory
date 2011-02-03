<?php
/*
Template Name: Submit Listing
*/
?>

<?php get_header() ?>
<?php locate_template( array( '/includes/components/sectionheaders.php' ), true ); ?>
<div id="content-wrapper">
		<div id="container">
			<div id="content" role="main">

            <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 class="entry-title"><?php the_title(); ?></h1>

                    <?php locate_template( array('checkout/checkout.php'), true ); ?>
                    
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'directory' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
                    
				</div><!-- #post-## -->

				<?php comments_template( '', true ); ?>

            <?php endwhile; ?>

			</div><!-- #content -->
			<div id="sidebar">
				Vestibulum mollis mauris enim. Morbi euismod magna ac lorem rutrum elementum. Donec viverra auctor lobortis. Pellentesque eu est a nulla placerat dignissim. Morbi a enim in magna semper bibendum. Etiam scelerisque, nunc ac egestas consequat, odio nibh euismod nulla, eget auctor orci nibh vel nisi. Aliquam erat volutpat. Mauris vel neque sit amet nunc gravida congue sed sit amet.
			</div>
			<div class="clear"></div>
			</div>
			<?php get_footer(); ?>