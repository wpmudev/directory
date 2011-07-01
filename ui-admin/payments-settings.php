<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $options = $this->get_options('payment_settings'); ?>

<div class="wrap">
    <?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'payments', 'sub' => 'checkout' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

    <form action="" method="post" class="dp-payments">
        <h3><?php _e( 'Recurring Payments', 'directory' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="recurring_cost"><?php _e('Cost of Service', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" class="small-text" id="recurring_cost" name="recurring_cost" value="<?php if ( isset( $options['recurring_cost'] ) ) echo $options['recurring_cost']; ?>" />
                    <span class="description"><?php _e('Amount to bill for each billing cycle.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="recurring_name"><?php _e('Name of Service', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" name="recurring_name" value="<?php if ( isset( $options['recurring_name'] ) ) echo $options['recurring_name']; ?>" />
                    <span class="description"><?php _e('Name of the service.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="billing_period"><?php _e('Billing Period', 'directory') ?></label>
                </th>
                <td>
                    <select id="billing_period" name="billing_period">
                        <option value="Day" <?php if ( isset( $options['billing_period'] ) && $options['billing_period'] == 'Day' ) echo 'selected="selected"'; ?>><?php _e( 'Day', 'directory' ); ?></option>
                        <option value="Week" <?php if ( isset( $options['billing_period'] ) && $options['billing_period'] == 'Week' ) echo 'selected="selected"'; ?>><?php _e( 'Week', 'directory' ); ?></option>
                        <option value="SemiMonth" <?php if ( isset( $options['billing_period'] ) && $options['billing_period'] == 'SemiMonth' ) echo 'selected="selected"'; ?>><?php _e( 'SemiMonth', 'directory' ); ?></option>
                        <option value="Month" <?php if ( isset( $options['billing_period'] ) && $options['billing_period'] == 'Month' ) echo 'selected="selected"'; ?>><?php _e( 'Month', 'directory' ); ?></option>
                        <option value="Year" <?php if ( isset( $options['billing_period'] ) && $options['billing_period'] == 'Year' ) echo 'selected="selected"'; ?>><?php _e( 'Year', 'directory' ); ?></option>
                    </select>
                    <span class="description"><?php _e('The unit of measure for the billing cycle.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="billing_frequency"><?php _e('Billing Frequency', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" class="small-text" id="billing_frequency" name="billing_frequency" value="<?php if ( isset( $options['billing_frequency'] ) ) echo $options['billing_frequency']; ?>" />
                    <span class="description"><?php _e('Number of billing periods that make up one billing cycle. The combination of billing frequency and billing period must be less than or equal to one year. If the billing period is SemiMonth, the billing frequency must be 1.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="billing_agreement"><?php _e('Billing Agreement', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" name="billing_agreement" value="<?php if ( isset( $options['billing_agreement'] ) ) echo $options['billing_agreement']; ?>" />
                    <span class="description"><?php _e('The description of the goods or services associated with that billing agreement. PayPal recommends that the description contain a brief summary of the billing agreement terms and conditions. For example, customer will be billed at "$9.99 per month for 2 years."', 'directory'); ?></span>
                </td>
            </tr>
        </table>

        <h3><?php _e( 'One-time Payments', 'directory' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="one_time_cost"><?php _e('Cost of Service', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" id="one_time_cost" name="one_time_cost" value="<?php if ( isset( $options['one_time_cost'] ) ) echo $options['one_time_cost']; ?>" />
                    <span class="description"><?php _e('Cost of service.', 'directory'); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="one_time_cost"><?php _e('Name of Service', 'directory') ?></label>
                </th>
                <td>
                    <input type="text" name="one_time_name" value="<?php if ( isset( $options['one_time_name'] ) ) echo $options['one_time_name']; ?>" />
                    <span class="description"><?php _e('Name of the service.', 'directory'); ?></span>
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Terms of Service', 'directory' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="tos_content"><?php _e('Terms of Service', 'directory') ?></label>
                </th>
                <td>
                    <textarea name="tos_content" id="tos_content" rows="15" cols="50"><?php if ( isset( $options['tos_content'] ) ) echo $options['tos_content']; ?></textarea>
                    <br />
                    <span class="description"><?php _e('Text for "Terms of Service"'); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php wp_nonce_field('verify'); ?>
            <input type="hidden" name="key" value="payment_settings" />
            <input type="submit" class="button-primary" name="save" value="Save Changes">
        </p>

    </form>
</div>
