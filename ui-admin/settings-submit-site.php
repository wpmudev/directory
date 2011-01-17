<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $options = $this->get_options('submit_site_settings'); ?>

<div class="wrap">
    
    <?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'sub' => 'submit_site' ) ); ?>
    
    <form action="" method="post" class="dp-payments">
        
        <table class="form-table">
            <tr>
                <th>
                    <label for="annual_cost"><?php _e('Annual Payment Option', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" id="annual_cost" name="annual_cost" value="<?php if ( isset( $options['annual_cost'] ) ) echo $options['annual_cost']; ?>" />
                    <span class="description"><?php _e('Price of "Annual" service.', 'directory'); ?></span>
                    <br /><br />
                    <input type="text" name="annual_txt" value="<?php if ( isset( $options['annual_txt'] ) ) echo $options['annual_txt']; ?>" />
                    <span class="description"><?php _e('Text of "Annual" service.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="one_time_cost"><?php _e('One Time Payment Option', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" id="one_time_cost" name="one_time_cost" value="<?php if ( isset( $options['one_time_cost'] ) ) echo $options['one_time_cost']; ?>" />
                    <span class="description"><?php _e('Price of "One Time" service.', 'directory'); ?></span>
                    <br /><br />
                    <input type="text" name="one_time_txt" value="<?php if ( isset( $options['one_time_txt'] ) ) echo $options['one_time_txt']; ?>" />
                    <span class="description"><?php _e('Text of "One Time" service.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="tos_txt"><?php _e('Terms of Service Text', 'directory') ?></label>
                </th>
                <td>
                    <textarea name="tos_txt" id="tos_txt" rows="15" cols="50"><?php if ( isset( $options['tos_txt'] ) ) echo $options['tos_txt']; ?></textarea>
                    <br />
                    <span class="description"><?php _e('Text for "Terms of Service"'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php wp_nonce_field('verify'); ?>
            <input type="hidden" name="key" value="submit_site_settings" />
            <input type="submit" class="button-primary" name="save" value="Save Changes">
        </p>
        
    </form>
</div>