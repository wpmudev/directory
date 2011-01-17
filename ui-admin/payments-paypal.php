<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $options = $this->get_options('paypal'); ?>

<div class="wrap">
    <?php screen_icon('options-general'); ?>
    
    <?php $this->render_admin( 'navigation', array( 'sub' => 'paypal' ) ); ?>

    <form action="" method="post" class="dp-payments">
        <table class="form-table">
            <tr>
                <th>
                    <label for="api_url"><?php _e('PayPal API Calls URL', $this->text_domain ) ?></label>
                </th>
                <td>
                    <select id="api_url" name="api_url">
                        <option value="sandbox" <?php if ( isset( $options['api_url'] ) && $options['api_url'] == 'Sandbox' ) echo 'selected="selected"' ?>><?php _e( 'Sandbox', $this->text_domain ); ?></option>
                        <option value="live"    <?php if ( isset( $options['api_url'] ) && $options['api_url'] == 'Live' )    echo 'selected="selected"' ?>><?php _e( 'Live', $this->text_domain ); ?></option>
                    </select>
                    <span class="description"><?php _e( 'Choose between PayPal Snadbox and PayPal Live.', $this->text_domain ); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="api_username"><?php _e( 'API Username', $this->text_domain ) ?></label>
                </th>
                <td>
                    <input type="text" id="api_username" name="api_username" value="<?php if ( isset( $options['api_username'] ) ) echo $options['api_username']; ?>" />
                    <span class="description"><?php _e( 'Your PayPal API Username.', $this->text_domain ); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="api_password"><?php _e( 'API Password', $this->text_domain ) ?></label>
                </th>
                <td>
                    <input type="text" id="api_password" name="api_password" value="<?php if ( isset( $options['api_password'] ) ) echo $options['api_password']; ?>" />
                    <span class="description"><?php _e( 'Your PayPal API Password.', $this->text_domain ); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="api_signature"><?php _e( 'API Signature', $this->text_domain ) ?></label>
                </th>
                <td>
                    <textarea rows="1" cols="55" id="api_signature" name="api_signature"><?php if ( isset( $options['api_signature'] ) ) echo $options['api_signature']; ?></textarea>
                    <br />
                    <span class="description"><?php _e( 'Your PayPal API Signature.', $this->text_domain ); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="currency"><?php _e( 'Currency', $this->text_domain ) ?></label>
                </th>
                <td>
                    <select id="currency" name="currency">
                        <option value="USD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'USD' ) echo 'selected="selected"' ?>><?php _e( 'U.S. Dollar', $this->text_domain ) ?></option>
                        <option value="EUR" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'EUR' ) echo 'selected="selected"' ?>><?php _e( 'Euro', $this->text_domain ) ?></option>
                        <option value="GBP" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'GBP' ) echo 'selected="selected"' ?>><?php _e( 'Pound Sterling', $this->text_domain ) ?></option>
                        <option value="CAD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'CAD' ) echo 'selected="selected"' ?>><?php _e( 'Canadian Dollar', $this->text_domain ) ?></option>
                        <option value="AUD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'AUD' ) echo 'selected="selected"' ?>><?php _e( 'Australian Dollar', $this->text_domain ) ?></option>
                        <option value="JPY" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'JPY' ) echo 'selected="selected"' ?>><?php _e( 'Japanese Yen', $this->text_domain ) ?></option>
                        <option value="CHF" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'CHF' ) echo 'selected="selected"' ?>><?php _e( 'Swiss Franc', $this->text_domain ) ?></option>
                        <option value="SGD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'SGD' ) echo 'selected="selected"' ?>><?php _e( 'Singapore Dollar', $this->text_domain ) ?></option>
                        <option value="NZD" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'NZD' ) echo 'selected="selected"' ?>><?php _e( 'New Zealand Dollar', $this->text_domain ) ?></option>
                        <option value="SEK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'SEK' ) echo 'selected="selected"' ?>><?php _e( 'Swedish Krona', $this->text_domain ) ?></option>
                        <option value="DKK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'DKK' ) echo 'selected="selected"' ?>><?php _e( 'Danish Krone', $this->text_domain ) ?></option>
                        <option value="NOK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'NOK' ) echo 'selected="selected"' ?>><?php _e( 'Norwegian Krone', $this->text_domain ) ?></option>
                        <option value="CZK" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'CZK' ) echo 'selected="selected"' ?>><?php _e( 'Czech Koruna', $this->text_domain ) ?></option>
                        <option value="HUF" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'HUF' ) echo 'selected="selected"' ?>><?php _e( 'Hungarian Forint', $this->text_domain ) ?></option>
                        <option value="PLN" <?php if ( isset( $options['currency'] ) && $options['currency'] == 'PLN' ) echo 'selected="selected"' ?>><?php _e( 'Polish Zloty', $this->text_domain ) ?></option>
                    </select>
                    <span class="description"><?php _e( 'The currency in which you want to process payments.', $this->text_domain ); ?></span>
                </td>
            </tr>
        </table>

        <p class="submit">
            <?php wp_nonce_field('verify'); ?>
            <input type="hidden" name="key" value="paypal" />
            <input type="submit" class="button-primary" name="save" value="Save Changes">
        </p>

    </form>
</div>