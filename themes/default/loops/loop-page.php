<?php

while (have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">
	<h2 class="pagetitle"><?php the_title(); ?></h2>
	<div class="entry">
	
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', THEME_TEXT_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
		<?php edit_post_link( __( 'Edit this entry.', THEME_TEXT_DOMAIN ), '<p>', '</p>'); ?>
	
	</div>
</div>
	
<div id="commentbox">
	<?php comments_template('', true); ?>
</div><!-- #commentbox -->

<?php endwhile; ?>
