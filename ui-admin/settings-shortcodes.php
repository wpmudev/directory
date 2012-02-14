<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<div class="wrap">

    <?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'shortcodes' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

    <h1><?php _e( 'Shortcodes', $this->text_domain ); ?></h1>

    <div class="postbox">
        <h3 class='hndle'><span><?php _e( 'Shortcodes', $this->text_domain ) ?></span></h3>
        <div class="inside">
            <p>
                <?php _e( 'Shortcodes allow you to include dynamic store content in posts and pages on your site. Simply type or paste them into your post or page content where you would like them to appear. Optional attributes can be added in a format like <em>[shortcode attr1="value" attr2="value"]</em>.', $this->text_domain ) ?>
            </p>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e( 'List of Categories:', $this->text_domain ) ?></th>
                    <td>
                        <strong>[dr_list_categories]</strong> -
                        <span class="description"><?php _e( 'Displays a list of categories.', $this->text_domain ) ?></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>
