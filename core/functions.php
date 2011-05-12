<?php


function the_listing_category() {
	global $dir_cats;

	$dir_cats->the_listing_category();
}

class Directory_Categories {

	var $listing_categories;
	var $categories_count = 0;
	var $current_category = -1;

	function Directory_Categories() {
		$this->listing_categories = $this->get_listing_categories();
	}

	function the_listing_category() {
		global $post;
		$this->in_the_loop = true;

		if ( $this->current_post == -1 ) // loop has just started
			do_action_ref_array('loop_start', array(&$this));

		$post = $this->next_post();
		setup_postdata($post);
	}

	function have_listing_categories() {

		if ( $this->current_category + 1 < $this->categories_count ) {
			return true;
		} elseif ( $this->current_post + 1 == $this->post_count && $this->post_count > 0 ) {
			do_action_ref_array('loop_end', array(&$this));
			// Do some cleaning up after the loop
			$this->rewind_posts();
		}

		$this->in_the_loop = false;
		return false;

		$terms = get_listing_categories();

	}

	/**
	 * Set up the next post and iterate current post index.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return object Next post.
	 */
	function next_post() {

		$this->current_post++;

		$this->post = $this->posts[$this->current_post];
		return $this->post;
	}

	/**
	 * Rewind the posts and reset post index.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	function rewind_posts() {
		$this->current_post = -1;
		if ( $this->post_count > 0 ) {
			$this->post = $this->posts[0];
		}
	}

	function get_listing_categories( $args = '' ) {

		$defaults = array(
			'orderby' => 'name', 
			'order' => 'ASC',
			'hide_empty' => false, 
			'exclude' => array(), 
			'exclude_tree' => array(), 
			'include' => array(),
			'number' => '10', 
			'fields' => 'all', 
			'slug' => '', 
			'parent' => '',
			'hierarchical' => true, 
			'child_of' => 0, 
			'get' => '', 
			'name__like' => '',
			'pad_counts' => false, 
			'offset' => '', 
			'search' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		return	$terms = get_terms( 'listing_category', $args );
	}

}

?>
