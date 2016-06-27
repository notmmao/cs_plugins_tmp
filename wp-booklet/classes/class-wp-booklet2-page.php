<?php

class WP_Booklet2_Page {

	private $_id;
	private $_image_id;
	private $_page_link;

	protected $_meta;
	
	/**
	 * The constructor
	 *
	 * @param int $id - the page ID
	 *
	 * @return WP_Booklet2_Page
	 */
	function __construct($id) {
		
		$this->_id = $id;

		$meta = get_post_custom( $id );
		
		if ( isset( $meta['wp_booklet2_page_properties'][0] ) ) {
			$properties = maybe_unserialize( $meta['wp_booklet2_page_properties'][0] );
			$this->_image_id = $properties['image_id'];
			$this->_page_link = $properties['page_link'];
		}

	}

	/**
	 * Get page image
	 * 
	 * @param string $size - any available Wordpress image size
	 *
	 * @return string
	 */
	function get_image_url($size){

		$image_attributes = wp_get_attachment_image_src($this->_image_id, $size);

		return $image_attributes[0];

	}
	
	/**
	 * Get actual page width. This is the same as the width of the 'full' image size.
	 *
	 * @return int
	 */
	function get_actual_width() {
		
		$file_path = get_attached_file( $this->_image_id );
		
		$size = getimagesize( $file_path );
		
		return $size[0];
		
	}
	
	/**
	 * Get actual page height. This is the same as the height of the 'full' image size.
	 *
	 * @return int
	 */
	function get_actual_height() {
		
		$file_path = get_attached_file( $this->_image_id );
		
		$size = getimagesize( $file_path );
		
		return $size[1];
		
	}
	 

	/**
	 * Get page link
	 *
	 * @return string
	 */
	function get_page_link() {

		return $this->_page_link;

	}
	
	/** 
	 * Get page id
	 *
	 * @return int
	 */
	function get_id() {
		return $this->_id;
	}

	/**
	 * Set page image
	 * 
	 * @param int $attachment_id - Wordpress attachment ID
	 *
	 * @return void
	 */
	function set_image($attachment_id) {
		$this->_image_id = $attachment_id;
	}	
	
	/**
	 * Set page link
	 *
	 * @param string $page_link - URL the page must point to
	 *
	 * @return void
	 */
		
	function set_page_link($page_link) {
		$this->_page_link = $page_link;
	}		
		
	/**
	 * Save page
	 *
	 * @return bool - true on success; false on failure
	 */
	function update_page() {
		
		if ( $this->_id ) {
			
			$properties['image_id'] = $this->_image_id;
			$properties['page_link'] = $this->_page_link;
			
			delete_post_meta( $this->_id, 'wp_booklet2_page_properties');
			$result = update_post_meta( $this->_id, 'wp_booklet2_page_properties', $properties);
			
			if ( $result ) {
				return true;
			}
			
			return false;
		}	
		
	}
		
	/** 
	 * Delete page
	 *
	 * @param int $page_id - Page ID
	 *
	 * @return mixed
	 */
	static function delete_page($page_id) {
		
		return wp_delete_post($page_id);
		
	}
		
	/**
	 * Create page
	 *
	 * @param int $attachment_id - Wordpress attachment ID
	 * @param string $page_link - URL the page page must point to
	 *
	 * @return int - page ID;
	 */
	static function create_page($attachment_id, $page_link) {
		
		$post = array(
			'post_title'=>'',
			'post_content'=>''
		);
			
		$id = wp_insert_post($post);	
		
		if ( $id ) {
			
			$properties['image_id'] = $attachment_id;
			$properties['page_link'] = $page_link;
			
			delete_post_meta( $id, 'wp_booklet2_page_properties');
			update_post_meta( $id, 'wp_booklet2_page_properties', $properties);
			
		}	
		
		return $id;
		
	}	
		
}