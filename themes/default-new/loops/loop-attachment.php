<?php

while ( have_posts() ) : the_post(); 

$attachment_link = get_the_attachment_link( $post->ID, true, array( 450, 800 ) ); // This also populates the iconsize for the next line 
$_post = &get_post( $post->ID ); //TODO WTF is this ? 
$classname = ( $_post->iconsize[0] <= 128 ? 'small' : '' ) . 'attachment'; // This lets us style narrow icons specially ?>

<div class="post" id="post-<?php the_ID(); ?>">
	<h2 class="posttitle"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &rarr; <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
	<div class="entry">
		<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename( $post->guid ); ?></p>
		<?php the_content( __('<p class="serif">Read the rest of this entry &rarr;</p>', THEME_TEXT_DOMAIN ) ); ?>
		<?php wp_link_pages( array( 'before' => __( '<p><strong>Pages:</strong> ', THEME_TEXT_DOMAIN ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
	</div>
</div>

<div id="commentbox">
	<?php comments_template('', true); ?>
</div> 

<?php endwhile; ?>
