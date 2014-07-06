<?php

/**
* CustomPress_Content_Types
*
* @uses CustomPress_Core
* @copyright Incsub 2007-2011 {@link http://incsub.com}
* @author Ivan Shaovchev (Incsub), Arnold Bailey (Incsub)
* @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
*/


if (!class_exists('CustomPress_Content_Types')):

//Allow shortcodes in Widgets
add_filter( 'widget_text', 'do_shortcode' );

class CustomPress_Content_Types extends CustomPress_Core {

	/** @public Array Available Post Types */
	public $post_types = array();
	/** @public array Available Network Post Types */
	public $network_post_types = array();
	/** @public Array All Available Post Types */
	public $all_post_types = array();

	/** @public array Available Taxonomies */
	public $taxonomies = array();
	/** @public array Available Network Taxonomies */
	public $network_taxonomies = array();
	/** @public array All Available Taxonomies */
	public $all_taxonomies = array();

	/** @public array Available Custom fields */
	public $custom_fields = array();
	/** @public array Available Network Custom fields */
	public $network_custom_fields = array();
	/** @public array All Available Custom fields */
	public $all_custom_fields = array();

	/** @public boolean Flag whether to flush the rewrite rules or not */
	public $flush_rewrite_rules = false;
	/** @public boolean Flag whether the users have the ability to declare post types for their own blogs */
	public $enable_subsite_content_types = false;
	/** @public bool  keep_network_content_type for site_options */
	public $network_content = true;
	// Setup the various structures table, ul, div

	public $display_network_content = false;

	public $active_inputs = array();

	public 	$structures = array (
	"none" =>
	array (
	"open" => "",
	"close" => "",
	"open_line" => "",
	"close_line" => "",
	"open_title" => "",
	"close_title" => "",
	"open_value" => "",
	"close_value" => "",
	),
	"table" =>
	array (
	"open" => "<table>\n",
	"close" => "</table>\n",
	"open_line" => "<tr>\n",
	"close_line" => "</tr>\n",
	"open_title" => "<th>\n",
	"close_title" => "</th>\n",
	"open_value" => "<td>\n",
	"close_value" => "</td>\n",
	),
	"ul" =>
	array (
	"open" => "<ul>\n",
	"close" => "</ul>\n",
	"open_line" => "<li>\n",
	"close_line" => "</li>\n",
	"open_title" => "<span>",
	"close_title" => "</span>",
	"open_value" => " ",
	"close_value" => "",
	),
	"div" =>
	array (
	"open" => "<div>",
	"close" => "</div>\n",
	"open_line" => "<p>",
	"close_line" => "</p>\n",
	"open_title" => "<span>",
	"close_title" => "</span>",
	"open_value" => " ",
	"close_value" => "",
	),
	);

	/**
	* Constructor
	*
	* @return void
	*/
	function __construct(){

		parent::__construct();

		//Init early to make sure types are registers
		add_action( 'after_setup_theme', array( &$this, 'register_taxonomies' ), 1 );
		add_action( 'after_setup_theme', array( &$this, 'register_post_types' ), 2 );
		add_action( 'init', array( &$this, 'flush_rewrite_rules' ), 99 ); //Give everyone else a chance to set rules, endpoints etc.

		//Add custom terms and fields on media page
		add_action( 'add_attachment', array(&$this,'save_custom_for_attachment'), 1 );
		add_action( 'edit_attachment', array(&$this,'save_custom_for_attachment'), 1 );

		add_action( 'add_meta_boxes', array( &$this, 'on_add_meta_boxes' ), 2 );
		add_action( 'save_post', array( &$this, 'save_custom_fields' ), 1, 1 );
		//add_action( 'user_register', array( &$this, 'set_user_registration_rewrite_rules' ) );

		add_shortcode('ct', array($this,'ct_shortcode'));
		add_shortcode('ct_in', array($this,'ct_in_shortcode'));
		add_shortcode('tax', array($this,'tax_shortcode'));
		add_shortcode('ct_filter', array($this,'filter_shortcode'));
		add_shortcode('ct_validate', array($this,'validation_rules'));

		add_shortcode('custom_fields_input', array($this,'inputs_shortcode'));
		add_shortcode('custom_fields_block', array($this,'fields_shortcode'));

		add_filter('the_content', array($this,'run_custom_shortcodes'), 6 ); //Early priority so that other shortcodes can use custom values

		$this->init_vars();
	}

	/**
	* Initiate variables
	*
	* @return void
	*/
	function init_vars() {

		$this->display_network_content = get_site_option('display_network_content_types');

		$this->enable_subsite_content_types = apply_filters( 'enable_subsite_content_types', false );

		if ( is_multisite() ) {
			$this->network_post_types    = get_site_option( 'ct_custom_post_types' );
			$this->network_taxonomies    = get_site_option( 'ct_custom_taxonomies' );
			$this->network_custom_fields = get_site_option( 'ct_custom_fields' );
		}
		$this->network_post_types = (empty($this->network_post_types)) ? array() : $this->network_post_types;
		$this->network_taxonomies = (empty($this->network_taxonomies)) ? array() : $this->network_taxonomies;
		$this->network_custom_fields = (empty($this->network_custom_fields)) ? array() : $this->network_custom_fields;

		if ( $this->enable_subsite_content_types == 1 ) {
			$this->post_types    = get_option( 'ct_custom_post_types' );
			$this->taxonomies    = get_option( 'ct_custom_taxonomies' );
			$this->custom_fields = get_option( 'ct_custom_fields' );
		} else {
			$this->post_types    = get_site_option( 'ct_custom_post_types' );
			$this->taxonomies    = get_site_option( 'ct_custom_taxonomies' );
			$this->custom_fields = get_site_option( 'ct_custom_fields' );
		}

		$this->post_types = (empty($this->post_types)) ? array() : $this->post_types;
		$this->taxonomies = (empty($this->taxonomies)) ? array() : $this->taxonomies;
		$this->custom_fields = (empty($this->custom_fields)) ? array() : $this->custom_fields;

		$this->all_post_types    = array_merge( $this->network_post_types, $this->post_types );
		$this->all_taxonomies    = array_merge( $this->network_taxonomies, $this->taxonomies );
		$this->all_custom_fields = array_merge( $this->network_custom_fields, $this->custom_fields );
	}

	/**
	* Get available custom post types and register them.
	* The function attach itself to the init hook and uses priority of 2. It loads
	* after the register_taxonomies() function which hooks itself to the init
	* hook with priority of 1 ( that's kinda important ) .
	*
	* @return void
	*/
	function register_post_types() {
		global $wp_post_types;
		$post_types = array();
		if(is_multisite() ) {
			if($this->display_network_content){
				if($this->enable_subsite_content_types) $post_types = $this->all_post_types;
				else $post_types = $this->network_post_types;
			}
			elseif($this->enable_subsite_content_types){
				$post_types = $this->post_types;
			}
		} else {
			$post_types = $this->post_types;
		}


		// Register each post type if array of data is returned
		if ( is_array( $post_types ) ) {
			foreach ( $post_types as $post_type => $args ) {

				//register post type
				register_post_type( $post_type, $args );
				//assign post type with regular taxanomies
				if ( isset( $args['supports_reg_tax'] ) ) {
					foreach ( $args['supports_reg_tax'] as $key => $value ) {
						if ( taxonomy_exists( $key ) && '1' == $value ) {
							register_taxonomy_for_object_type( $key, $post_type );
						}
					}
				}
			}
		}
		//var_dump($wp_post_types);
	}

	/**
	* Get available custom taxonomies and register them.
	* The function attaches itself to the init hook and uses priority of 1. It loads
	* before the ct_admin_register_post_types() func which hook itself to the init
	* hook with priority of 2 ( that's kinda important )
	*
	* @uses apply_filters() You can use the 'sort_custom_taxonomies' filter hook to sort your taxonomies
	* @return void
	*/
	function register_taxonomies() {
		$taxonomies = array();
		if(is_multisite() ) {
			if($this->display_network_content){
				if($this->enable_subsite_content_types) $taxonomies = $this->all_taxonomies;
				else $taxonomies = $this->network_taxonomies;
			}
			elseif($this->enable_subsite_content_types){
				$taxonomies = $this->taxonomies;
			}
		} else {
			$taxonomies = $this->taxonomies;
		}

		// Plugins can filter this value and sort taxonomies
		$sort = null;
		$sort = apply_filters( 'sort_custom_taxonomies', $sort );
		// If custom taxonomies are present, register them
		if ( is_array( $taxonomies ) ) {
			// Sort taxonomies
			if ( $sort == 'alphabetical' )
			ksort( $taxonomies );
			// Register taxonomies
			foreach ( $taxonomies as $taxonomy => $args )
			register_taxonomy( $taxonomy, $args['object_type'], $args['args'] );
		}
	}

	/**
	* Display custom fields template on add custom post pages
	*
	* @return void
	*/
	function display_custom_fields($fields = '', $style = '') {
		$this->render_admin('display-custom-fields', array( 'type' => 'local', 'fields' => $fields, 'style' => $style ) );
	}

	/**
	* Display custom fields template on add custom post pages
	*
	* @return void
	*/
	function display_custom_fields_network($fields = '', $style = '') {
		$this->render_admin('display-custom-fields', array( 'type' => 'network', 'fields' => $fields, 'style' => $style ) );
	}

	/**
	* Save custom fields data
	*
	* @param int $post_id The post id of the post being edited
	*/
	function save_custom_fields( $post_id ) {
		// Prevent autosave from deleting the custom fields
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE
		|| defined('DOING_AJAX') && DOING_AJAX
		|| defined('DOING_CRON') && DOING_CRON
		|| isset($_REQUEST['bulk_edit'])
		|| (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array( 'trash', 'untrash' ) ) )
		)
		return;

		$params = stripslashes_deep($_POST);

		$custom_fields = $this->all_custom_fields;
		$post_type = get_post_type($post_id);

		if ( !empty( $custom_fields )) {
			foreach ( $custom_fields as $custom_field ) {

				//Is field used by the post type?
				if(in_array( $post_type, $custom_field['object_type'])) {

					$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';

					//If field value save it
					if( isset( $params[$prefix . $custom_field['field_id']] )){
						update_post_meta( $post_id, $prefix . $custom_field['field_id'], $params[$prefix . $custom_field['field_id']] );
					}

					if('checkbox' == $custom_field['field_type']){
						//no boxes checked
						if( !isset($params[$prefix . $custom_field['field_id']]) )
						delete_post_meta( $post_id, $prefix . $custom_field['field_id'] );
					}
				}
			}
		}
	}

	/**
	* Makes sure the admin always has all rights to all custom post types.
	*
	* @return void
	*/
	function add_admin_capabilities(){
		global $wp_roles;

		if ( ! is_object($wp_roles) ) return;

		$role = get_role('administrator');

		if(is_multisite()) {
			$post_types = $this->network_post_types;
			if(is_array($post_types)){
				foreach($post_types as $key => $pt){
					$post_type = get_post_type_object($key);
					if($post_type !== null ) {
						$all_caps = $this->all_capabilities($key);
						foreach($all_caps as $capability){
							$role->add_cap( $capability );
						}
					}
				}
			}
		}

		$post_types = $this->post_types;
		if(is_array($post_types)){
			foreach($post_types as $key => $pt){
				$post_type = get_post_type_object($key);
				if($post_type !== null ) {
					$all_caps = $this->all_capabilities($key);
					foreach($all_caps as $capability){
						$role->add_cap( $capability );
					}
				}
			}
		}
	}

	/**
	* Flush rewrite rules based on boolean check
	*  Setting 'ct_flush_rewrite_rules' site_option to a unique triggers across network
	*/
	function flush_rewrite_rules() {
		// Mechanism for detecting changes in sub-site content types for flushing rewrite rules

		$hard = true;
		if ( is_multisite() ) {
			$network_frr_id = get_site_option('ct_flush_rewrite_rules');
			$local_frr_id = get_option('ct_flush_rewrite_rules');
			if ( $network_frr_id != $local_frr_id ) {
				$hard = (1 == $network_frr_id + 0); //Convert to number
				$this->flush_rewrite_rules = true;
				update_option('ct_flush_rewrite_rules', $network_frr_id );
			}
		} else {
			$local_frr_id = get_option('ct_flush_rewrite_rules');
			if ( ! empty( $local_frr_id ) ) {
				$hard = (1 == $local_frr_id + 0); //Convert to number
				$this->flush_rewrite_rules = true;
				update_option('ct_flush_rewrite_rules', 0 );
			}
		}

		// flush rewrite rules
		if ( $this->flush_rewrite_rules || !empty( $_GET['frr'] ) ) {
			flush_rewrite_rules($hard);
			$this->flush_rewrite_rules = false;
		}
		$this->add_admin_capabilities();
	}

	/**
	* Check whether new users are registered, since we need to flush the rewrite
	* rules for them.
	*
	* @return void
	*/
	function set_user_registration_rewrite_rules() {
		flush_network_rewrite_rules();
	}

	//Save custom fields value from media page
	function save_custom_for_attachment( $post_id = 0 ) {

		if(empty($post_id) ) return;
		$post = get_post($post_id);
		if($post->post_type == 'revision') return;

		$params = stripslashes_deep($_POST);

		//Save custom fields for Attachment post type
		if ( is_array( $this->all_custom_fields ) ) {
			foreach ( $this->all_custom_fields as $custom_field ) {
				$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';
				$fid = $prefix . $custom_field['field_id'];

				if ( in_array ( 'attachment', $custom_field['object_type'] ) ) {
					if( isset( $params[ $fid] ) ){
						// update_post_meta
						update_post_meta( $post->ID, $fid, $params[$fid] );
					}
					elseif('checkbox' == $custom_field['field_type']){
						//no boxes checked
						if( !isset($params[$prefix . $custom_field['field_id']]) )
						delete_post_meta( $post_id, $prefix . $custom_field['field_id'] );
					}
				}
			}
		}
	}


	/**
	* Creates shortcodes for fields which may be used for shortened embed codes.
	*
	* @return string
	* @uses appy_filters()
	*/
	function ct_shortcode($atts, $content=null){
		global $post;

		extract( shortcode_atts( array(
		'id' => '',
		'property' => 'value',
		), $atts ) );

		// Take off the prefix for indexing the array;

		$cid = preg_replace('/^(_ct|ct)_/', '', $id);

		$custom_field = (isset($this->all_custom_fields[$cid])) ? $this->all_custom_fields[$cid] : null;
		$property = strtolower($property);

		$result = '';

		switch ($property){
			case 'title': $result = $custom_field['field_title']; break;
			case 'description': $result = $custom_field['field_description']; break;
			case 'value':
			default: {
				switch ($custom_field['field_type']){
					case 'checkbox':
					case 'multiselectbox': {
						if( $values = get_post_meta( $post->ID, $id, true ) ) {
							foreach ( (array)$values as $value ) {
								$result .= (empty($result)) ? $value : ', ' . $value;
							}
						}
						break;
					}
					case 'selectbox':
					case 'radio': {
						if( $values = get_post_meta( $post->ID, $id, true ) ) {
							foreach ( (array)$values as $value ) {
								$result .= (empty($result)) ? $value : ', ' . $value;
							}
						}
						break;
					}
					case 'datepicker': {
						$result = strip_tags(get_post_meta( $post->ID, $id, true ) ); break;
					}
					default: {
						$result = get_post_meta( $post->ID, $id, true ); break;
					}
				}
			}
		}
		$result = apply_filters('ct_shortcode', $result, $atts, $content);
		return $result;
	}

	/**
	* Creates shortcodes for fields which may be used for shortened embed codes.
	*
	* @return string
	* @uses appy_filters()
	*/
	function ct_in_shortcode($atts, $content=null){
		global $post;

		wp_enqueue_script('jquery-validate');

		extract( shortcode_atts( array(
		'id' => '',
		'property' => 'input',
		'class' => '',
		'required' => null,
		), $atts ) );

		// Take off the prefix for indexing the array;

		$cid = preg_replace('/^(_ct|ct)_/', '', $id);

		$custom_field = (isset($this->all_custom_fields[$cid])) ? $this->all_custom_fields[$cid] : null;

		if($custom_field) {
			$this->active_inputs[$id] = $custom_field;
			if( !is_null($required) ) {
				$this->active_inputs[$id]['field_required'] = ( strtolower($required) == 'true');
			}
		}

		$property = strtolower($property);
		$result = '';

		if( !empty( $custom_field['field_options'] ) ) {
			$field_options = $custom_field['field_options'];
			if( $custom_field['field_sort_order'] == 'asc' ) {
				asort($field_options);
			}
			elseif( $custom_field['field_sort_order'] == 'desc' ) {
				arsort($field_options);
			};
		}

		switch ($property){
			case 'title': $result = $custom_field['field_title']; break;
			case 'description': $result = $custom_field['field_description']; break;
			case 'input':
			default: {
				switch ($custom_field['field_type']){
					case 'checkbox': {
						$field_values = get_post_meta( $post->ID, $id, true );


						foreach ( $field_options as $key => $field_option ) {
							if($field_values)
							$result .= sprintf('<label><input type="checkbox" class="ct-field ct-checkbox %s" name="%s[]" id="%s" value="%s" %s /> %s</label>', $class, $id, "{$id}_{$key}", esc_attr( $field_option ), checked( is_array($field_values) && array_search($field_option, $field_values) !== false, true, false ), $field_option );
							else
							$result .= sprintf('<label><input type="checkbox" class="ct-field ct-checkbox %s" name="%s[]" id="%s" value="%s" %s /> %s</label>', $class, $id, "{$id}_{$key}", esc_attr( $field_option ), checked( $custom_field['field_default_option'], $key, false ), $field_option );
						}
						break;
					}
					case 'multiselectbox': {
						$multiselectbox_values = get_post_meta( $post->ID, $id, true );
						$multiselectbox_values = (is_array($multiselectbox_values)) ? $multiselectbox_values : (array)$multiselectbox_values;

						$result = sprintf('<select class="ct-field ct-select-multiple %s" name="%s[]" id="%s" multiple="multiple">', $class, $id, $id ) . PHP_EOL;
						foreach ( $field_options as $key => $field_option ) {
							if($multiselectbox_values)
							$result .= sprintf('<option value="%s" %s >%s</option>', esc_attr( $field_option ), selected(in_array($field_option, $multiselectbox_values), true, false ), $field_option ) . PHP_EOL;
							else
							$result .= sprintf('<option value="%s" %s >%s</option>', esc_attr( $field_option ), selected($custom_field['field_default_option'], $key, false ), $field_option ) . PHP_EOL;
						}
						$result .= "</select>\n";
						break;
					}
					case 'selectbox': {
						$field_value = get_post_meta( $post->ID, $id, true );

						$result = sprintf('<select class="ct-field ct-selectbox %s" name="%s" id="%s" >', $class, $id, $id) . PHP_EOL;
						foreach ( $field_options as $key => $field_option ) {
							if ($field_value)
							$result .= sprintf('<option value="%s" %s >%s</option>', esc_attr( $field_option ), selected($field_value, $field_option, false), $field_option ) . PHP_EOL;
							else
							$result .= sprintf('<option value="%s" %s>%s</option>', esc_attr( $field_option ), selected($custom_field['field_default_option'], $key, false ), $field_option ) . PHP_EOL;
						}
						$result .= "</select>\n";
						break;
					}
					case 'radio': {
						$field_value = get_post_meta( $post->ID, $id, true );

						foreach ( $field_options as $key => $field_option ) {
							if($field_value)
							$result .=	sprintf('<label><input type="radio" class="ct-field ct-radio %s"  name="%s" id="%s" value="%s" %s /> %s</label>', $class, $id, "{$id}_{$key}", esc_attr( $field_option ), checked($field_value, $field_option, false), $field_option);
							else
							$result .=	sprintf('<label><input type="radio" class="ct-field ct-radion %s" name="%s" id="%s" value="%s" %s /> %s</label>', $class, $id, "{$id}_{$key}", esc_attr( $field_option ), checked($custom_field['field_default_option'], $key, false), $field_option);
						}
						break;
					}
					case 'text': {
						$result = sprintf('<input type="text" class="ct-field ct-text %s" name="%s" id="%s" value="%s" />', $class, $id, $id, esc_attr( get_post_meta( $post->ID, $id, true ) ) );
						break;
					}
					case 'textarea': {
						$result = sprintf('<textarea class="ct-field ct-textarea %s" name="%s" id="%s" rows="5" cols="40" >%s</textarea>', $class, $id, $id, esc_textarea( get_post_meta( $post->ID, $id, true ) ) );
						break;
					}
					case 'datepicker': {
						wp_enqueue_style('jquery-ui-datepicker');
						wp_enqueue_script('jquery-ui-datepicker');
						wp_enqueue_script('jquery-ui-datepicker-lang');
						//						$result = $this->jquery_ui_css() . PHP_EOL;
						$result = sprintf('<input type="text" class="pickdate ct-field %s" name="%s" id="%s" value="%s" />', $class, $id, $id, esc_attr( strip_tags(get_post_meta( $post->ID, $id, true ) ) ) ) . PHP_EOL;
						$result .= sprintf('
						<script type="text/javascript">
						jQuery(document).ready(function(){
						jQuery("#%s").datepicker({ dateFormat : "%s" });
						});
						</script>
						', $id, $custom_field['field_date_format'] );
						break;
					}
				}
			}
		}
		$result = apply_filters('ct_in_shortcode', $result, $atts, $content);
		return $result;
		;
	}



	/**
	* Creates shortcodes for fields which may be used for shortened embed codes.
	*
	* @string
	* @uses appy_filters()
	*/
	function tax_shortcode($atts, $content = null){
		global $post;

		extract( shortcode_atts( array(
		'id' => '',
		'before' => '',
		'separator' => ', ',
		'after' => '',
		), $atts ) );

		$result = get_the_term_list( $post->ID, $id, $before, $separator, $after );

		$result = (is_wp_error($result)) ? __('Invalid Taxonomy name in [tax ] shortcode', $this->text_domain) : $result;

		$result = apply_filters('tax_shortcode', $result, $atts, $content);
		return $result;
	}

	/**
	*
	*
	*
	*/
	function inputs_shortcode($atts, $content=null){
		global $post;
		extract( shortcode_atts( array(
		'post_id' => 0,
		'style' => '',
		'wrap' => 'ul',
		'open' => null,
		'close' => null,
		'open_line' => null,
		'close_line' => null,
		'open_title' => null,
		'close_title' => null,
		'open_value' => null,
		'close_value' => null,
		), $atts ) );

		if ( ! empty($post_id) ) $post = get_post($post_id);
		ob_start();

		wp_enqueue_script('jquery-validate');

		$this->display_custom_fields( do_shortcode($content), $style );
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	* Creates shortcodes for fields which may be used for shortened embed codes.
	*
	* @string
	* @uses apply_filters()
	*/
	function fields_shortcode($atts, $content = null){
		global $post;

		extract( shortcode_atts( array(
		'wrap' => 'ul',
		'open' => null,
		'close' => null,
		'open_line' => null,
		'close_line' => null,
		'open_title' => null,
		'close_title' => null,
		'open_value' => null,
		'close_value' => null,
		), $atts ) );

		//Initialize with blanks
		$fmt = $this->structures['none'];

		// If its' predefined
		if(in_array($wrap, array('table','ul','div'))){
			$fmt = $this->structures[$wrap];
		}

		//Override any defined in $atts
		foreach($fmt as $key => $item){
			$fmt[$key] = ($$key === null) ? $fmt[$key] : $$key;
		}

		//See if a filter is defined
		$field_ids = array_filter( array_map('trim',( array)explode(',', do_shortcode($content) ) ) );

		$custom_fields = array();

		if(empty($field_ids)){
			$custom_fields = $this->all_custom_fields;
		} else {
			foreach($field_ids as $field_id){
				$cid = preg_replace('/^(_ct|ct)_/', '', $field_id);

				if(array_key_exists($cid, $this->all_custom_fields)) {
					$custom_fields[$cid] = $this->all_custom_fields[$cid];
				}
			}
		}

		if (empty($custom_fields)) $custom_fields = array();

		$result = $fmt['open'];
		foreach ( $custom_fields as $custom_field ){
			$output = in_array($post->post_type, $custom_field['object_type']);
			if ( $output ){

				$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';
				$fid = $prefix . $custom_field['field_id'];
				$value = do_shortcode('[ct id="' . $fid . '"]');
				if($value != ''){

					$result .= $fmt['open_line'];
					$result .= $fmt['open_title'];
					$result .= ( $custom_field['field_title'] );
					$result .= $fmt['close_title'];
					$result .= $fmt['open_value'];

					$result .= $value;

					$result .= $fmt['close_value'];
					$result .= $fmt['close_line'];
				}
			}
		}
		$result .= $fmt['close'];

		$result = apply_filters('custom_fields', $result, $atts, $content);

		// Wrap of for CSS after filtering
		$result = '<div class="ct-custom-field-block">' . "\n{$result}</div>\n";
		return $result;
	}

	/**
	* returns list of a comma delimited field names matching the custom term supplied
	*
	* @terms Comma separated list of terms.
	* @content Comma separated list of custom field ids.
	*
	* @return Comma separated list of field IDs filtered by terms
	*/

	function filter_shortcode($atts, $content=null){
		global $post;

		extract( shortcode_atts( array(
		'terms' => '',
		'not' => 'false',
		), $atts ) );

		$post_type = get_post_type($post->ID);
		$content = do_shortcode($content);
		$content = preg_replace('/\s/','', $content);
		$fields = array_map('trim', (array)explode(',', $content) );

		if( ! empty($terms) ){
			$belongs = false;
			$taxonomies = array_values( get_object_taxonomies($post_type, 'object') );

			$term_names = array_map('trim', (array)explode(',',$terms) );

			foreach($taxonomies as $taxonomy){
				if($taxonomy->hierarchical){ //Only the category like
					foreach($term_names as $name){
						$term = get_term_by('slug', $name, $taxonomy->name);
						if( empty($term->parent) ) {
							$children = get_term_children($term->term_id, $taxonomy->name);
							if(is_object_in_term($post->ID, $taxonomy->name, $children) ) $belongs = true;
						}

						if(is_object_in_term($post->ID, $taxonomy->name, $name) ) $belongs = true;
					}
				}
			}
			if(! $belongs) $fields = array();
		}


		if($not == 'true'){

			foreach($fields as &$field) $field = preg_replace('/^(_ct|ct)_/', '', $field);

			$fields = array_keys( array_diff_key( $this->all_custom_fields, array_flip($fields)) );
		}

		$result = implode(',', array_filter($fields) );  //filter blanks
		if($result) $result .= ',';

		$result = apply_filters('custom_fields_filter', $result, $atts, $content);
		return $result;
	}

	/**
	* Process the [ct] and [tax] shortcodes.
	*
	* Since the [ct] and [tax] shortcodes needs to be run earlier than all other shortcodes
	* so the values may be used by other shortcodes, media [embed] is the earliest with an 8 priority so we need an earlier priority.
	* this function removes all existing shortcodes, registers the [ct] and [tax] shortcode,
	* calls {@link do_shortcode()}, and then re-registers the old shortcodes.
	*
	* @uses $shortcode_tags
	* @uses remove_all_shortcodes()
	* @uses add_shortcode()
	* @uses do_shortcode()
	*
	* @param string $content Content to parse
	* @return string Content with shortcode parsed
	*/
	function run_custom_shortcodes($content){
		global $shortcode_tags;

		// Back up current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		remove_all_shortcodes();

		add_shortcode( 'ct', array(&$this, 'ct_shortcode') );

		add_shortcode( 'ct_in', array(&$this, 'ct_in_shortcode') );

		add_shortcode( 'tax', array(&$this, 'tax_shortcode') );

		// Do the shortcode (only the [ct] and [tax] are registered)
		$content = do_shortcode( $content );

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}

	/**
	* gets the current post type in the WordPress Admin
	*/
	function get_current_post_type() {
		global $post, $typenow, $current_screen;

		//we have a post so we can just get the post type from that
		if ( $post && $post->post_type )
		return $post->post_type;

		//check the global $typenow - set in admin.php
		elseif( $typenow )
		return $typenow;

		//check the global $current_screen object - set in sceen.php
		elseif( $current_screen && $current_screen->post_type )
		return $current_screen->post_type;

		//lastly check the post_type querystring
		elseif( isset( $_REQUEST['post_type'] ) )
		return sanitize_key( $_REQUEST['post_type'] );

		//we do not know the post type!
		return null;
	}

	/**
	* Return JQuery script to validate an array of custom fields
	* @$custom_fields array of custom field definition to generate rules for.
	*
	*/

	function validation_rules($atts, $content = null){
		extract( shortcode_atts( array(
		'id' => '',
		'before' => '',
		'separator' => ', ',
		'after' => '',
		), $atts ) );

		$custom_fields = $this->active_inputs;

		//if(empty($custom_fields)) return '';

		$selector = array_keys( array_filter($custom_fields) );
		$selector = reset($selector);


		$rules = array();
		$messages = array();

		$validation = array();

		if( !$selector ) {
			$validation[] = '<input type="hidden" id="ct_custom_fields_form" />';
			$selector = "ct_custom_fields_form";
		}

		$validation[] = '<script type="text/javascript">';
		$validation[] = "		jQuery(document).ready( function($) {";
		$validation[] = "jQuery('[id^={$selector}]').closest('form').validate();"; //find the form we're validating

		foreach($custom_fields as $custom_field) {

			$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';

			$fid = $prefix . $custom_field['field_id'];
			if ( in_array( $custom_field['field_type'], array('checkbox', 'multiselectbox') ) )
			$fid = '"' . $fid . '[]"'; //Multichoice version
			else
			$fid = '"' . $fid . '"' ;

			// collect messages
			$msgs = array();

			$message = ( empty( $custom_field['field_message']) ) ? '' : trim($custom_field['field_message']);
			if( ! empty( $message ) ) $msgs[] = "required: '{$message}'";

			$regex_options = ( empty( $custom_field['field_regex_options']) ) ? '' : trim($custom_field['field_regex_options']);

			$regex_message = ( empty( $custom_field['field_regex_message']) ) ? '' : trim($custom_field['field_regex_message']);
			if( ! empty( $regex_message ) ) $msgs[] = "regex: '{$regex_message}'";


			if( ! empty($msgs) )	$validation[] = "jQuery('[name={$fid}]').rules('add', { messages: {" . implode(", ", $msgs ) . " } });";

			//Collect rules
			$rls = array();
			if ($custom_field['field_required'] || ! empty($custom_field['field_regex'])) { //we have validation rules
				if( ! empty($custom_field['field_required']) ) $rls[] = 'required: true';
				if( ! empty($custom_field['field_regex'])) $rls[] = "regex: /{$custom_field['field_regex']}/{$regex_options}";
				//Add more in the future
			}

			if( ! empty($rls) ) $validation[] = "jQuery('[name={$fid}]').rules('add', { " . implode(", ", $rls ) . " } );";
		}
		$validation[] = "});";
		$validation[] = '</script>';
		$validation = implode("\n", $validation);

		return $validation;
	}


	function __set($name, $value){

		switch ($name) {
			case 'import': $this->_import($value); break;

		}
	}

	function _import($types = array()){

		if(! (is_array($types) && defined( 'CT_ALLOW_IMPORT' ) ) ) return;

		if ( is_network_admin() ) {
			$post_types = get_site_option('ct_custom_post_types');
			$taxonomies = get_site_option('ct_custom_taxonomies');
			$custom_fields = get_site_option('ct_custom_fields');
		} else {
			$post_types = get_option('ct_custom_post_types');
			$taxonomies = get_option('ct_custom_taxonomies');
			$custom_fields = get_option('ct_custom_fields');
		}

		if(! empty($types['post_types']) && is_array($types['post_types'])) {
			foreach($types['post_types'] as $key => $value) {
				$post_types[$key] = $value;
			}
		}

		if(! empty($types['taxonomies']) && is_array($types['taxonomies'])) {
			foreach($types['taxonomies'] as $key => $value) {
				$taxonomies[$key] = $value;
			}
		}

		if(! empty($types['custom_fields']) && is_array($types['custom_fields'])) {
			foreach($types['custom_fields'] as $key => $value) {
				$custom_fields[$key] = $value;
			}
		}

		if ( is_network_admin() ) {
			update_site_option('ct_custom_post_types', $post_types);
			update_site_option('ct_custom_taxonomies', $taxonomies);
			update_site_option('ct_custom_fields', $custom_fields);
		} else {
			update_option('ct_custom_post_types', $post_types);
			update_option('ct_custom_taxonomies', $taxonomies);
			update_option('ct_custom_fields', $custom_fields);
		}

	}

	/**
	* Returns an array of the custom fields belonging to a given post_type.
	* @param string post_type to get custom fields for
	* @return array
	*/
	function get_custom_fields_set($post_type = ''){
		if(empty($post_type) ) return array();

		$result = array();

		foreach($this->all_custom_fields as $key => $field) {
			if( in_array($post_type, $field['object_type']) ) {
				$result[$key] = $field;
			}
		}

		return $result;
	}

}

// Initiate Content Types Module


if(!is_admin()) {
	$CustomPress_Core = new CustomPress_Content_Types();
}

endif;