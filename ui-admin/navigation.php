<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $settings_page = 'dp_main';  ?>

<h2>
    <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'general' || isset( $_GET['tab'] ) && $_GET['tab'] == 'payments' || ( isset( $_GET['page'] ) && $_GET['page'] == 'dp_main' && empty( $_GET['tab'] ) ) ): ?>
        <a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'general' || empty( $_GET['tab'] ) )  echo 'nav-tab-active'; ?>" href="admin.php?page=<?php echo $settings_page; ?>&tab=general&sub=general"><?php _e( 'General', $this->text_domain ); ?></a>
        
    <?php elseif ( isset( $_GET['tab'] ) && $_GET['tab'] == 'my_credits' || isset( $_GET['tab'] ) && $_GET['tab'] == 'send_credits' || ( isset( $_GET['page'] ) && $_GET['page'] == 'classifieds_credits' && empty( $_GET['tab'] ) ) ): ?>
        <a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'my_credits' || empty( $_GET['tab'] ) )  echo 'nav-tab-active'; ?>" href="admin.php?page=<?php echo $settings_page; ?>&tab=my_credits"><?php _e( 'My Credits', $this->text_domain ); ?></a>
    <?php endif; ?>
</h2>

<?php if ( $sub == 'general' || $sub == 'submit_site' || $sub == 'ads' ): ?>
<ul>
    <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'general' || empty( $_GET['tab'] ) )   echo 'current'; ?>" href="admin.php?page=<?php echo $settings_page; ?>&tab=general&sub=general"><?php _e( 'General Settings', $this->text_domain ); ?></a> | </h3></li>
    <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'submit_site' ) echo 'current'; ?>" href="admin.php?page=<?php echo $settings_page; ?>&tab=general&sub=submit_site"><?php _e( 'Submit Site', $this->text_domain ); ?></a> | </h3></li>
    <li class="subsubsub"><h3><a class="<?php if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'ads' ) echo 'current'; ?>" href="admin.php?page=<?php echo $settings_page; ?>&tab=general&sub=ads"><?php _e( 'Advertising', $this->text_domain ); ?></a></h3></li>
</ul>
<?php endif; ?>

<?php if ( $sub == 'credits' || $sub == 'checkout' ): ?>
<ul>
    <li class="subsubsub"><h3><a class="<?php if ( $_GET['sub'] == 'credits' || empty( $_GET['tab'] ) )   echo 'current'; ?>" href="admin.php?page=<?php echo $settings_page; ?>&tab=general&sub=credits"><?php _e( 'Credits', $this->text_domain ); ?></a> | </h3></li>
    <li class="subsubsub"><h3><a class="<?php if ( $_GET['sub'] == 'checkout' ) echo 'current'; ?>" href="admin.php?page=<?php echo $settings_page; ?>&tab=general&sub=checkout"><?php _e( 'Checkout', $this->text_domain ); ?></a></h3></li>
</ul>
<?php endif; ?>

<div class="clear"></div>

<?php $this->render_admin( 'message' ); ?>