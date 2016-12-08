<?php

global $wp_query;


$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$pages = $wp_query->max_num_pages;

if( ! $pages) $pages = 1;
$range = 4;
if( isset( $this->pagination_range ) && is_numeric( $this->pagination_range ) ) $range = (int)$this->pagination_range;
$showitems = ($range * 2) + 1;

$range_start = 1;
$range_end = $pages;

if( $pages > $range ){

	if( $range % 2 == 0 ){
		$range_start = $paged - ( $range / 2 );
		$range_end = $paged + ( $range / 2 ) - 1;
	}
	else{
		$range_start = $paged - floor( $range / 2 );
		$range_end = $paged + floor( $range / 2 );
	}

	if( $range_start < 1 ){

		$range_end = add_range_pages_until_limit( 'add', $range_end, abs( $range_start ) + 1, $pages );
		$range_start = 1;


	}

	if( $range_end > $pages ){
	
		$range_start = add_range_pages_until_limit( 'sub', $range_start, abs( $range_end-$pages ), 1 );
		$range_end = $pages;

	}

	
}

function add_range_pages_until_limit( $act, $val, $amount, $limit ){

	if( $act == 'add' ){

		for( $i = 1; $i<=$amount; $i++ ){
			if( $val == $limit ) break;
			$val++;
		}

	}

	if( $act == 'sub' ){

		for( $i = 1; $i<=$amount; $i++ ){
			
			if( $val == $limit ) break;
			$val--;
			
		}

	}

	return $val;

}
?>
<div class="navigation"><!--begin .dr-navigation-->

	<?php if ( $pages > 1 ) : ?>

	<div class="dr-pagination"><!--begin .dr-pagination-->

		<span><?php echo sprintf( __('Page %1$d of %2$d',$this->text_domain), $paged, $pages); ?></span>

		<?php if($paged > 2 ): ?>
		<a href="<?php echo get_pagenum_link(1); ?>">&laquo;<?php _e('First',$this->text_domain); ?></a>
		<?php endif; ?>

		<?php if($paged > 1 ) : ?>
		<a href="<?php echo get_pagenum_link($paged - 1); ?>">&lsaquo;<?php _e('Previous',$this->text_domain); ?></a>
		<?php endif; ?>

		<?php for ($i=$range_start;$i <= $range_end;$i++) :
		if (1 != $pages && ( !($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )):
		echo ($paged == $i) ? '<span class="current">' . $i . '</span>' : '<a href="' . get_pagenum_link($i) . '" class="inactive">' . $i . '</a>';
		endif;
		endfor;

		if ($paged < $pages ) : ?>
		<a href="<?php echo get_pagenum_link($paged + 1); ?>"><?php _e('Next',$this->text_domain); ?>&rsaquo;</a>
		<?php endif; ?>

		<?php if ($paged < $pages - 1 ): ?>
		<a href="<?php echo get_pagenum_link($pages); ?>"><?php _e('Last', $this->text_domain); ?>&raquo;</a>
		<?php endif; ?>

	</div> <!--end .dr-pagination-->

	<?php endif; ?>

</div><!--end .dr-navigation-->
