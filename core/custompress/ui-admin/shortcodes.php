<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<div class="wrap">

	<?php $this->render_admin( 'message' ); ?>

	<h3><?php _e( 'CustomPress Shortcodes', $this->text_domain ); ?></h3>


	<div class="postbox">
		<h3 class='hndle'><span><?php _e( 'CustomPress Shortcodes', $this->text_domain ) ?></span></h3>
		<div class="inside">
			<table class="form-table">
				<tr>
					<th>
						Custom Fields Input
					</th>
					<td>
						<p>Used to embed the input fields of a set of custom fields for the post "ID" specifed. Must be used inside a &lt;form&gt; and after submit the receiving form code must call the global:
							<br /><code>global $CustomPress_Core;<br />$CustomPress_Core->save_custom_fields( $post_id );</code><br />
							to save the input back to the post.
						</p>
						<div class="embed-code-wrap">
							<?php _e('Basic shortcode', $this->text_domain); ?>
							<br /><code>[custom_fields_input id="post_id"]</code>
							<br /><span class="description"><?php _e('Returns a full set of input fields based on the post type of the post id provided.', $this-text_domain); ?></span>

							<br /><br /><?php _e('or with field list', $this->text_domain); ?><br />
							<code>[custom_fields_input id="post_id"] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/custom_fields_input]</code>
							<br /><span class="description"><?php _e('Returns a set of input fields as supplied by the field id list in the shortcode. Any ids not associate with the post type will be ignored.', $this-text_domain); ?></span>

							<br /><br /><?php _e('or with field list filtered by category', $this->text_domain);?><br />
							<code>[custom_fields_input id="post_id"] [ct_filter terms="cat1, cat2,.."] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/ct_filter] [/custom_fields_input]</code>
							<br /><span class="description"><?php _e('Multiple filters may be used in one input block.', $this->text_doamin); ?></span>
						</div>

					</td>
				</tr>

				<tr>
					<th>
						Custom Fields Block
					</th>
					<td>
						<div>
							<p>Used to embed the output of a set of custom fields for the current post. Must be used inside the loop.
							</p>
							<div class="embed-code-wrap">
								<?php _e('Basic shortcode', $this->text_domain); ?>
								<br /><code>[custom_fields_block]</code>
								<br /><span class="description"><?php _e('Returns a full set of input fields based on the post type of the post id provided.', $this-text_domain); ?></span>

								<br /><br /><?php _e('or with field list', $this->text_domain); ?><br />
								<code>[custom_fields_block] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/custom_fields_block]</code>
								<br /><span class="description"><?php _e('Returns a set of input fields as supplied by the field id list in the shortcode. Any ids not associate with the post type will be ignored.', $this-text_domain); ?></span>

								<br /><br /><?php _e('or with field list filtered by category', $this->text_domain);?>
								<br /><code>[custom_fields_block] [ct_filter terms="cat1, cat2,.."] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/ct_filter] [/custom_fields_block]</code>
								<br /><span class="description"><?php _e('Multiple filters may be used in one input block.', $this->text_doamin); ?></span>
							</div>


							<p>
								<strong><?php _e('Attributes for the [custom_fields_block]', $this->text_domain); ?></strong>
								<br /><span class="description"><?php _e( 'wrap        = Wrap the fields in either a "table", a "ul" or a "div" structure.', $this->text_domain ) ?></span>
								<br /><strong><?php _e( 'The default wrap attributes may be overriden using the following individual attributes:', $this->text_domain); ?></strong>
								<br /><span class="description"><?php _e( 'open = HTML to begin the block with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php _e( 'close = HTML to end the block with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php _e( 'open_line = HTML to begin a line with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php _e( 'close_line = HTML to end a line with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php _e( 'open_title = HTML to begin the title with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php _e( 'close_title = HTML to end the title with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php _e( 'open_value = HTML to begin the value with', $this->text_domain ) ?></span>
								<br /><span class="description"><?php _e( 'close_value = HTML to end the value with', $this->text_domain ) ?></span>
							</p>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						Custom Field Filter
					</th>
					<td>
						<p><?php _e('Used to restrict the list of fields returned depending on the categories of the post. Multiple [ct_filter] shortcodes may be added to a [custom_field_input] or [custom_field_block] shortcode.', $this->text_domain); ?></p>
						<code>[ct_filter terms="cat1, cat2,.."] _ct_selectbox_4cf582bd61fa4, _ct_text_4cfeb3eac6f1f,... [/ct_filter]</code>
						<br /><span class="description"><?php _e('terms= Comma separated category list to filter on. Categories not associated with the post type of the current post will be ignored.', $this-text_domain); ?></span>
						<br /><span class="description"><?php _e('The comma separated list of fields between the opening and closing tags will be returned if the categories match. Fields not associated with the post type of the current post will be ignored.', $this-text_domain); ?></span>
					</td>
				</tr>


			</table>
		</div>
	</div>

</div>
