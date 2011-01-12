<?php $options = get_site_option('dp_options'); ?>

<form action="" method="post"  class="dp-sys-info">

    <span class="dp-submit-txt"><?php _e('Cost of Service', 'directorypress'); ?></span>

    <table <?php do_action( 'billing_invalid' ); ?>>
        <tr>
            <th><label for="billing"><?php echo $options['submit_site_settings']['annual_txt']; ?></label></th>
            <td>
                <input type="radio" name="billing" value="annual" <?php if ( $_POST['billing'] == 'annual' ) echo 'checked="checked"'; ?> />  <?php echo $options['submit_site_settings']['annual_price']; ?> <?php echo $options['paypal']['currency_code']; ?>
                <input type="hidden" name="annual_cost" value="<?php echo $options['submit_site_settings']['annual_price']; ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="billing"><?php echo $options['submit_site_settings']['one_time_txt']; ?></label></th>
            <td>
                <input type="radio" name="billing" value="one_time" <?php if ( $_POST['billing'] == 'one_time' ) echo 'checked="checked"'; ?> /> <?php echo $options['submit_site_settings']['one_time_price']; ?> <?php echo $options['paypal']['currency_code']; ?>
                <input type="hidden" name="one_time_cost" value="<?php echo $options['submit_site_settings']['one_time_price']; ?>" />
            </td>
        </tr>
    </table>

    <span class="dp-submit-txt"><?php _e('Terms of Service', 'directorypress'); ?></span>

    <table>
        <tr>
            <th class="dp-terms"><?php echo nl2br( $options['submit_site_settings']['tos_txt'] ); ?></th>
        </tr>
    </table>

    <table  <?php do_action( 'tos_agree_invalid' ); ?> >
        <tr>
            <th><label for="tos-agree"><?php _e('I agree with the Terms of Service', 'directorypress'); ?></label></th>
            <td><input type="checkbox" name="tos_agree" value="1" <?php if ( $_POST['tos_agree'] ) echo 'checked="checked"'; ?> /></td>
        </tr>
    </table>

    <input type="submit" name="terms_submit" value="Continue" />
</form>

<form action="" method="post" class="dp-prs-info">

    <span class="dp-submit-txt"><?php _e('Existing client', 'directorypress'); ?>.</span>

    <table <?php do_action( 'login_invalid' ); ?> >
        <tr>
            <th><label for="username"><?php _e('Username', 'directorypress'); ?>:</label></th>
            <td><input type="text" name="username" /></td>
        </tr>
        <tr>
            <th><label for="user_password"><?php _e('Password', 'directorypress'); ?>:</label></th>
            <td><input type="password" name="password" /></td>
        </tr>
    </table>

    <div class="clear"></div>

    <input type="submit" name="login_submit" value="Continue" />
    <br />
    <?php do_action( 'login_invalid_txt' ); ?>

</form>