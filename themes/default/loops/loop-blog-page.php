<?php

$page = ( get_query_var('paged') ) ? get_query_var('paged') : 1; 
query_posts( "cat=&showposts=5&paged=$page" ); 

while ( have_posts() ) : the_post();?>

<div class="excerptpost" id="post-<?php the_ID(); ?>">

	<div class="entry-meta">
		<span class="byline"><?php the_time('M j Y') ?> <?php _e( 'in', THEME_TEXT_DOMAIN ) ?> <?php the_category(', ') ?> <em><?php _e( 'by ', THEME_TEXT_DOMAIN ) ?><?php the_author_link();  ?></em></span>
		<span class="tags"><?php the_tags( __( 'Tags: ', THEME_TEXT_DOMAIN ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Reviews &#187;', THEME_TEXT_DOMAIN ), __( '1 Review &#187;', THEME_TEXT_DOMAIN ), __( '% Reviews &#187;', THEME_TEXT_DOMAIN ), '',  __( 'Reviews Off', THEME_TEXT_DOMAIN) ); ?></span>
	</div>

	<div class="entry-post">
		<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', THEME_TEXT_DOMAIN ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
		<div class="entry">
			<?php if( has_post_thumbnail() ): ?> 
			<a href="<?php the_permalink() ?>"><?php the_post_thumbnail( array( 275, 100 ), array( 'class' => 'alignleft' ) ); ?></a>
			<?php endif; ?>
			<?php the_excerpt(); ?>
			<div class="clear-left"></div>
		</div>
	</div>

	<div class="clear"></div>

</div>

<?php endwhile; ?>
