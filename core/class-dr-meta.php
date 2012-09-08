<?php

class DR_Meta{

	protected $_meta = null;

	public $post_id = 0;
	public $text_domain = DR_TEXT_DOMAIN;
	public $options_name = DR_OPTIONS_NAME;
	public $plugin_dir = DR_PLUGIN_DIR;
	public $plugin_url = DR_PLUGIN_URL;
	public $meta_name = '_dr_meta';

	private $struc = array(
	'status' => 'unpaid',
	'expires' => 0,

	);

	function DR_Meta($id = 0){ __construct($id); }

	function __construct($id = 0){
		global $post_id;

		$this->post_id = empty($id) ? $post_id['ID'] : $id;

		$this->_meta = get_post_meta( $this->post_id, $this->meta_name, true);

		if( empty($this->_meta) ) {
			$this->_meta = $this->struc;
			update_post_meta($this->post_id, $this->meta_name, $this->_meta);
		}
	}

	function __get(  $property = '' ){
		$this->_meta = get_post_meta( $this->post_id, $this->meta_name, true);

		switch( $property ) {
			case 'meta' : return $this->_meta; break;
			case 'status' : return $this->_meta['status']; break;
			case 'expires' : return $this->_meta['expires']; break;
		}

	}

	function __set($property, $value){
		$this->_meta = get_post_meta( $this->post_id, $this->meta_name, true);

		switch( $property ) {
			case 'meta' : $this->_meta = $value; break;
			case 'status' : $this->_meta['status'] = $value; break;
			case 'expires' : $this->_meta['expires'] = $value; break;
		}

		update_post_meta($this->post_id, $this->meta_name, $this->_meta);
	}

	function __isset($property){
		return ( in_array($property, array(
		'status',
		'expires',
		)
		) );
	}

}
