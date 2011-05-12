<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<div class="wrap">
    <?php screen_icon('options-general'); ?>

    <?php $this->render_admin( 'navigation', array( 'page' => 'settings', 'tab' => 'payments', 'sub' => 'authorizenet' ) ); ?>

    <table class="form-table">
        <tr>
            <th><code><?php _e( 'Under Development. Coming Soon!', $this->text_domain ); ?></code></th>
        </tr>
    </table>
</div>
