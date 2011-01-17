<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $options = $this->get_options('ads_settings'); ?>

<div class="wrap">

    <?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'sub' => 'ads' ) ); ?>

    <form action="" method="post" class="dp-ads">
        
        <table class="form-table">
            <tr>
                <th>
                    <label for="header_ad_code"><?php _e('Ad Code Header Banner', 'directory') ?></label>
                </th>
                <td>
                    <textarea id="header_ad_code" name="header_ad_code" rows="15" cols="50"><?php if ( isset( $options['header_ad_code'] ) ) echo $options['header_ad_code']; ?></textarea>
                    <br />
                    <span class="description"><?php _e('Place ad code here!', 'directory'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php wp_nonce_field('verify'); ?>
            <input type="hidden" name="key" value="ads_settings" />
            <input type="submit" class="button-primary" name="save" value="Save Changes">
        </p>
        
    </form>

</div>