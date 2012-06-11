<?php

/**
* Ratings_Core
*
* @package Ratings
* @version 1.0.0
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/
class DR_Ratings {

	/** @var string The current page. Used for custom hooks. */
	var $page;
	/** @var array Array with values used for determining post quality. */
	var $quality = array( 1 => 'Not so great', 2 => 'Quite good', 3 => 'Good', 4 => 'Great!', 5 => 'Excellent!' );

	/**
	* Class constructor.
	*/
	function DR_Ratings() {
		$this->init();
	}

	/**
	* Hook class methods.
	*
	* @access public
	* @return void
	*/
	function init() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		//add_action( 'wp_print_styles', array( &$this, 'print_styles' ));
		add_action( 'wp_head', array( &$this, 'print_scripts' ) );
		add_action( 'sr_avg_rating', array( &$this, 'render_avg_rating' ) );
		add_action( 'sr_avg_ratings_of_listings', array( &$this, 'render_avg_ratings_of_listings' ) );
		add_action( 'sr_user_rating', array( &$this, 'render_user_rating' ) );
		add_action( 'sr_rate_this', array( &$this, 'render_rate_this' ) );
		add_action( 'wp_ajax_sr_save_vote', array( &$this, 'handle_ajax_requests' ) );
		add_action( 'wp_ajax_nopriv_sr_save_vote', array( &$this, 'handle_ajax_requests' ) );
	}

	/**
	* Returns rating for post. If user ID is passed it will return the rating
	* given by the particular user ( if it exists for the current post ). If no
	* user ID is passed the method returns the aggregated rating for the post.
	*
	* @param mixed $post_id
	* @param mixed $user_id
	* @access public
	* @return string|array
	*/
	function get_rating( $post_id, $user_id = null ) {
		if ( isset( $user_id ) ) {
			$rating = get_user_meta( $user_id, '_sr_post_vote', true );
			if ( isset( $rating[$post_id] ) ) {
				return $rating[$post_id];
			} else {
				return 'no_rate';
			}
		} else {
			$votes = get_post_meta( $post_id, '_sr_post_votes', true ) ? get_post_meta( $post_id, '_sr_post_votes', true ) : '0';
			$rating = get_post_meta( $post_id, '_sr_post_rating', true ) ? get_post_meta( $post_id, '_sr_post_rating', true ) : '0';
			$avg = ( !empty( $rating ) && !empty( $votes ) ) ? round( (int) $rating / (int) $votes ) : '0';
			return array( 'votes' => $votes, 'rating' => $rating, 'avg' => $avg );
		}
	}

	/**
	* save_rating
	*
	* @param mixed $post_id
	* @param mixed $rating
	* @access public
	* @return void
	*/
	function save_rating( $post_id, $rating ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			update_user_meta( $user_id, '_sr_post_vote', array( $post_id => $rating ) );
		}
		$votes          = get_post_meta( $post_id, '_sr_post_votes', true );
		$current_rating = get_post_meta( $post_id, '_sr_post_rating', true );
		$votes++;
		$rating = $current_rating + $rating;
		update_post_meta( $post_id, '_sr_post_votes', $votes  );
		update_post_meta( $post_id, '_sr_post_rating', $rating  );
	}

	/**
	* Ajax callback which gets the post types associated with each page.
	*
	* @return JSON Encoded data
	**/
	function handle_ajax_requests() {
		// veriffy user input!
		$rating = $this->in_range( $_POST['rate'], 1, 5 );
		// update statistic and save to file
		$this->save_rating( $_POST['post_id'], $rating );
		$respons = $this->get_rating( $_POST['post_id'] );
		// return json object
		header( "Content-Type: application/json" );
		echo json_encode( $respons );
		exit;
	}

	/**
	* Enqueue styles.
	*/
	function enqueue_styles() {
		wp_enqueue_style( 'jquery-ui-stars', DR_PLUGIN_URL . 'ui-front/js/jquery-ui-stars/jquery-ui-stars.css');
	}

	/**
	* Enqueue scripts.
	*/
	function enqueue_scripts() {
		wp_register_script( 'jquery-ui-core-1.8', DR_PLUGIN_URL . 'ui-front/js/jquery-ui-stars/jquery-ui.custom.min.js' );
		wp_enqueue_script( 'jquery-ui-stars-script', DR_PLUGIN_URL . 'ui-front/js/jquery-ui-stars/jquery-ui-stars.js', array( 'jquery', 'jquery-ui-core-1.8', 'jquery-form' ) );
	}

	/**
	* Print document scripts.Handles the colorpickers.
	*
	* @access public
	* @global object $post
	* @return void
	*/
	function print_scripts() {
		global $post;

		$post_id = 0;
		if ( isset( $post->ID ) && 0 < $post->ID )
		$post_id = $post->ID;



		$ajaxurl = admin_url( 'admin-ajax.php' ); ?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(function($) {
				$("#avg").children().not(":input").hide();
				$("#rat").children().not("select, #messages").hide();
				$(".user_votes").children().not(":input").hide();
				// Create stars for: Average rating
				$("#avg").stars();
				$(".avg_of_listings").stars();
				$(".user_votes").stars();
				// Create stars for: Rate this
				$("#rat").stars({
					inputType: "select",
					cancelShow: false,
					captionEl: $("#caption"),
					callback: function(ui, type, value) {
						// Disable Stars for exclude the next vote
						ui.disable();
						// Display message to the user at the begining of request
						$("#messages").text("Saving...").stop().css("opacity", 1).fadeIn(30);
						// Send request to the server using POST method
					$.post("<?php echo $ajaxurl; ?>", { action: 'sr_save_vote', post_id: <?php echo $post_id; ?>, rate: value }, function(response) {
						// Select stars from "Average rating" control to match the returned average rating value
						$("#avg").stars("select", Math.round(response.avg));
						// Update other text controls...
						$("#all_votes").text(response.votes);
						$("#all_avg").text(response.avg);
						// Display confirmation message to the user
						$("#messages").text("Rating saved (" + value + "). Thanks!").stop().css("opacity", 1).fadeIn(30);
						// Hide confirmation message and enable stars for "Rate this" control, after 2 sec...
						setTimeout(function(){
							$("#messages").fadeOut(1000, function(){})
						}, 2000);
					}, "json" );
				}
			});
			// Since the <option value="3"> was selected by default, we must remove selection from Stars.
			$("#rat").stars("selectID", -1);
			// Create element to use for confirmation messages
			$('<div id="messages"/>').appendTo("#rat");
		});
		//]]>
		</script> <?php
	}

	/**
	* Render rate this block.
	*
	* @access public
	* @global object $post
	* @return void
	*/
	function render_rate_this() {
		global $post;

		$user_id    = get_current_user_id();
		$rating     = $this->get_rating( $post->ID, $user_id );

		if ( 'no_rate' != $rating ) {
			?>
			<div class="sr-user-rating"><strong><?php _e( 'Rating:', 'directory' ); ?></strong>
				<span>(<?php echo $this->quality[$rating] ?>)</span>
				<form class="user_votes" style="float: left; padding: 3px 8px 0 0;" action="#">
					<?php foreach ( $this->quality as $scale => $text ): ?>
					<input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>" disabled="disabled" <?php echo $scale == $rating ? 'checked="checked"' : '' ?> />
					<?php endforeach; ?>
				</form>
			</div>

			<?php

		} else {
			$rating = $this->get_rating( $post->ID ); ?>
			<?php /*
			<?php if (isset($post_message)): ?>
			<div class="message-box ok">Thanks, vote saved: <?php echo $post_message ?></div>
			<?php endif; ?>
			*/ ?>
			<div class="clear-left"></div>
			<div class="sr-avg-rating"><strong>Rate this:</strong> <span id="caption"></span>
				<form id="rat" action="#" method="post" action="#">
					<select name="rate">
						<?php foreach ( $this->quality as $scale => $text ): ?>
						<option <?php echo $scale == 3 ? 'selected="selected"' : '' ?> value="<?php echo $scale; ?>"><?php echo $text; ?></option>
						<?php endforeach; ?>
					</select>
					<input type="submit" value="Rate it!" />
				</form>
			</div> <?php
		}
	}

	/**
	* Render avarage rating.
	*
	* @access public
	* @return void
	*/
	function render_avg_rating() {
		global $post;
		$rating = $this->get_rating( $post->ID ); ?>
		<div class="sr-rate-this"><strong>Average rating</strong>
			<span>(<span id="all_votes-<?php echo $post->ID; ?>"><?php echo $rating['votes']; ?></span> votes; <span id="all_avg<?php echo $post->ID; ?>"><?php echo $rating['avg'] ?></span>)</span>
			<form id="avg" style="float: left; padding: 3px 8px 0 0;" action="#">
				<?php foreach ( $this->quality as $scale => $text ): ?>
				<input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>" disabled="disabled" <?php echo $scale == $rating['avg'] ? 'checked="checked"' : '' ?> />
				<?php endforeach; ?>
			</form>
		</div> <?php
	}

	/**
	* Render avarage rating of listings on category page.
	*
	* @access public
	* @return void
	*/
	function render_avg_ratings_of_listings( $post_id = 0 ) {

		if ( 0 == $post_id ) {
			global $post;
			$post_id = $post->ID;
		}

		$rating = $this->get_rating( $post_id ); ?>
		<div class="sr-rate-this"><strong>Average rating</strong>
			<span>(<span id="all_votes<?php echo $post_id; ?>"><?php echo $rating['votes']; ?></span> votes; <span id="all_avg<?php echo $post_id; ?>"><?php echo $rating['avg'] ?></span>)</span>
			<form class="avg_of_listings" style="float: left; padding: 3px 8px 0 0;" action="#">
				<?php foreach ( $this->quality as $scale => $text ): ?>
				<input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>" disabled="disabled" <?php echo $scale == $rating['avg'] ? 'checked="checked"' : '' ?> />
				<?php endforeach; ?>
			</form>
		</div> <?php
	}

	/**
	* render_user_rating
	*
	* @access public
	* @return void
	*/
	function render_user_rating() {
		global $post;

		$user_id = get_current_user_id();
		$rating = $this->get_rating( $post->ID, $user_id );

		?>
		<div class="sr-user-rating"><strong><?php _e( 'Rating:', 'directory' ); ?></strong>
			<span>(<?php echo $this->quality[$rating] ?>)</span>
			<form class="user_votes" style="float: left; padding: 3px 8px 0 0;" action="#">
				<?php foreach ( $this->quality as $scale => $text ):

				?>
				<input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>" disabled="disabled" <?php echo $scale == $rating ? 'checked="checked"' : '' ?> />
				<?php endforeach; ?>
			</form>
		</div> <?php
	}

	/**
	* in_range
	*
	* @param mixed $val
	* @param int $from
	* @param float $to
	* @access public
	* @return void
	*/
	function in_range( $val, $from = 0, $to = 100 ) {
		return min( $to, max( $from, (int) $val ) );
	}

}

new DR_Ratings();
