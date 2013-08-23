<?php
/**
 * Social Marketing contextual help implementation.
 */

class DR_ContextualHelp {

	private $_help;

	private $_pages = array(
		'list', 'edit', 'get_started', 'directory_settings',
	);

	private $_dr_sidebar = '';
    private $text_domain = DR_TEXT_DOMAIN;


    /**
     * PHP 5 constructor
     **/
    function __construct () {
        if ( !class_exists( 'WpmuDev_ContextualHelp' ) ) require_once DR_PLUGIN_DIR . 'libs/class_wd_contextual_help.php';
        $this->_help = new WpmuDev_ContextualHelp();
        $this->_set_up_sidebar();

        foreach ( $this->_pages as $page ) {
            $method = "_add_{$page}_page_help";
            if ( method_exists( $this, $method ) ) $this->$method();
        }
        $this->_help->initialize();
    }

	private function _set_up_sidebar () {
		$this->_dr_sidebar = '<h4>' . __( 'Directory', $this->text_domain ) . '</h4>';
		if ( defined( 'WPMUDEV_REMOVE_BRANDING' ) && constant( 'WPMUDEV_REMOVE_BRANDING' ) ) {
			$this->_dr_sidebar .= '<p>' . __( 'The Directory plugin transforming your WordPress install from a blogging platform into a powerful online directory with loads of features and built in payment gateways.', $this->text_domain ) . '</p>';
		} else {
				$this->_dr_sidebar .= '<ul>' .
					'<li><a href="http://premium.wpmudev.org/project/wordpress-directory" target="_blank">' . __( 'Project page', $this->text_domain ) . '</a></li>' .
					'<li><a href="http://premium.wpmudev.org/project/wordpress-directory/installation/" target="_blank">' . __( 'Installation and instructions page', $this->text_domain ) . '</a></li>' .
					'<li><a href="http://premium.wpmudev.org/forums/tags/directory/" target="_blank">' . __( 'Support forum', $this->text_domain ) . '</a></li>' .
				'</ul>' .
			'';
		}
	}

	private function _initialize () {
		foreach ( $this->_pages as $page ) {
			$method = "_add_{$page}_page_help";
			if ( method_exists( $this, $method ) ) $this->$method();
		}
		$this->_help->initialize();
	}


/* Pages */

	private function _add_list_page_help () {
        $this->_help->add_page(
            'edit-directory_listing',
            array(
                array(
                    'id' => 'dr-intro',
                    'title' => __( 'Intro', $this->text_domain ),
                    'content' => '<p>' . __( 'All existing listings are listed here.', $this->text_domain ) . '</p>',
                ),
                array(
                    'id' => 'dr-general',
                    'title' => __( 'General Info', $this->text_domain ),
                    'content' => '' .
                        '<p>' . __( 'The Directory plugin transforming your WordPress install from a blogging platform into a powerful online directory with loads of features and built in payment gateways.', $this->text_domain ) . '</p>' .
                        '<ul>' .
                            '<li>' . __( 'You can make your site available free to create lists, or to charge money for it.', $this->text_domain ) . '</li>' .
                        '</ul>' .
                        '<p>' . sprintf( __( 'Get up and running quickly with the <a href="%s">Getting Started Guide</a>', $this->text_domain ), admin_url( 'edit.php?post_type=directory_listing&amp;page=dr-get_started' ) ) . '</p>' .
                    ''
                ),

                array(
                    'id' => 'dr-tutorial',
                    'title' => __( 'Tutorial', $this->text_domain ),
                    'content' => '' .
                        '<p>' .
                            __( 'Tutorial dialogs will guide you through the important bits.', $this->text_domain ) .
                        '</p>' .
                        '<p><a href="#" class="dr-restart_tutorial" data-dr_tutorial="setup">' . __( 'Restart the tutorial', $this->text_domain ) . '</a></p>',
                ),
            ),
            $this->_dr_sidebar,
            true
        );
    }

    private function _add_edit_page_help () {
        $this->_help->add_page(
            'directory_listing',
            array(
                array(
                    'id' => 'dr-intro',
                    'title' => __( 'Intro', $this->text_domain ),
                    'content' => '<p>' . __( 'This is where you can edit or create a listing.', $this->text_domain ) . '</p>',
                ),
                array(
                    'id' => 'dr-general',
                    'title' => __( 'General Info', $this->text_domain ),
                    'content' => '' .
                        '<p>' . __( 'The Directory plugin transforming your WordPress install from a blogging platform into a powerful online directory with loads of features and built in payment gateways.', $this->text_domain ) . '</p>' .
                        '<ul>' .
                            '<li>' . __( 'You can make your site available free to create lists, or to charge money for it.', $this->text_domain ) . '</li>' .
                        '</ul>' .
                        '<p>' . sprintf( __( 'Get up and running quickly with the <a href="%s">Getting Started Guide</a>', $this->text_domain ), admin_url( 'edit.php?post_type=directory_listing&amp;page=dr-get_started' ) ) . '</p>' .
                    ''
                ),

                array(
                    'id' => 'dr-tutorial',
                    'title' => __( 'Tutorial', $this->text_domain ),
                    'content' => '' .
                        '<p>' .
                            __( 'Tutorial dialogs will guide you through the important bits.', $this->text_domain ) .
                        '</p>' .
                        '<p><a href="#" class="dr-restart_tutorial" data-dr_tutorial="setup">' . __( 'Restart the tutorial', $this->text_domain ) . '</a></p>',
                ),
            ),
            $this->_dr_sidebar,
            true
        );
    }

    private function _add_get_started_page_help () {
        $this->_help->add_page(
            'directory_listing_page_dr-get_started',
            array(
                array(
                    'id' => 'dr-intro',
                    'title' => __( 'Intro', $this->text_domain ),
                    'content' => '<p>' . __( 'This is the guide to get you started with <b>Directory</b> plugin', $this->text_domain ) . '</p>',
                ),
                array(
                    'id' => 'dr-general',
                    'title' => __( 'General Info', $this->text_domain ),
                    'content' => '' .
                        '<p>' . __( 'The Directory plugin transforming your WordPress install from a blogging platform into a powerful online directory with loads of features and built in payment gateways.', $this->text_domain ) . '</p>' .
                        '<ul>' .
                            '<li>' . __( 'You can make your site available free to create lists, or to charge money for it.', $this->text_domain ) . '</li>' .
                        '</ul>' .
                        '<p>' . sprintf( __( 'Get up and running quickly with the <a href="%s">Getting Started Guide</a>', $this->text_domain ), admin_url( 'edit.php?post_type=directory_listing&amp;page=dr-get_started' ) ) . '</p>' .
                    ''
                ),

                array(
                    'id' => 'dr-tutorial',
                    'title' => __( 'Tutorial', $this->text_domain ),
                    'content' => '' .
                        '<p>' .
                            __( 'Tutorial dialogs will guide you through the important bits.', $this->text_domain ) .
                        '</p>' .
                        '<p><a href="#" class="dr-restart_tutorial" data-dr_tutorial="setup">' . __( 'Restart the tutorial', $this->text_domain ) . '</a></p>',
                ),
            ),
            $this->_dr_sidebar,
            true
        );
    }



	private function _add_directory_settings_page_help () {
		$this->_help->add_page(
			'directory_listing_page_directory_settings',
			array(
				array(
					'id' => 'dr-intro',
					'title' => __( 'Intro', $this->text_domain ),
					'content' => '<p>' . __( 'This is where you configure <b>Directory</b> plugin for your site', $this->text_domain ) . '</p>',
				),
				array(
					'id' => 'dr-general',
					'title' => __( 'General Info', $this->text_domain ),
					'content' => '' .
						'<p>' . __( 'The Directory plugin transforming your WordPress install from a blogging platform into a powerful online directory with loads of features and built in payment gateways.', $this->text_domain ) . '</p>' .
						'<ul>' .
							'<li>' . __( 'You can make your site available free to create lists, or to charge money for it.', $this->text_domain ) . '</li>' .
						'</ul>' .
						'<p>' . sprintf( __( 'Get up and running quickly with the <a href="%s">Getting Started Guide</a>', $this->text_domain ), admin_url( 'edit.php?post_type=directory_listing&amp;page=dr-get_started' ) ) . '</p>' .
					''
				),

				array(
					'id' => 'dr-tutorial',
					'title' => __( 'Tutorial', $this->text_domain ),
					'content' => '' .
						'<p>' .
							__( 'Tutorial dialogs will guide you through the important bits.', $this->text_domain ) .
						'</p>' .
						'<p><a href="#" class="dr-restart_tutorial" data-dr_tutorial="setup">' . __( 'Restart the tutorial', $this->text_domain ) . '</a></p>',
				),
			),
			$this->_dr_sidebar,
			true
		);
	}
}


/* Initiate Admin */
new DR_ContextualHelp();