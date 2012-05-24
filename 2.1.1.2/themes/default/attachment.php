<?php get_header() ?>

<div id="content-fullwidth"><!-- start #content -->
	<div class="padder">
		<div class="page" id="attachments-page">

			<?php if ( have_posts() ) : ?>
				<?php locate_template( array( '/loops/loop-attachment.php' ), true ); ?>
				<?php locate_template( array( '/components/pagination.php' ), true ); ?>
			<?php else: ?>
				<?php locate_template( array( '/components/messages.php' ), true ); ?>
			<?php endif; ?>

		</div><!-- end #blog-latest -->
	</div>
</div><!-- end #content -->

<?php get_footer() ?>
