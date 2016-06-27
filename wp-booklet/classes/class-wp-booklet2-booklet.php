<?php

class WP_Booklet2_Booklet {

	private $_id;
	private $_wp_booklet_1_id;
	private $_meta;
	private $_width;
	private $_height;
	private $_speed;
	private $_delay;
	private $_direction;
	private $_arrows_enabled;
	private $_page_numbers_enabled;
	private $_cover_behavior;
	private $_padding;
	private $_thumbnails_enabled;
	private $_popup_enabled;
	private $_theme;

	protected $_pages;
	
	/**
	 * The constructor
	 *
	 * @param int $id - booklet ID 
	 *
	 * @return WP_Booklet2_Booklet
	 */
	function __construct($id) {
		
		if ( get_post_type( $id ) == 'wp-booklet' ) {
			
			$args = array(
				'post_type'=>'wp-booklet2',
				'meta_query'=>array(
					array(
						'key'=>'wp_booklet_1_id',
						'value'=>$id
					)
				)
			);
			
			$query = new WP_Query($args);
			
			if ( count( $query->posts ) ) {
				$this->_id = $query->posts[0]->ID;
			}
			else {
				$this->_id = null;
			}
			
		}
		else {
			$this->_id = $id;
		}
		
		$meta = get_post_custom($this->_id);
		
		if ( isset( $meta['wp_booklet_1_id'] ) ) {
			$this->_wp_booklet_1_id = $meta['wp_booklet_1_id'][0];
		}
		
		if ( isset( $meta['wp_booklet2_pages'] ) ) {
			
			$this->_pages = maybe_unserialize( $meta['wp_booklet2_pages'][0] );
			
		}
		
      	if ( isset( $meta['wp_booklet2_properties'] )){
			
			$booklet_properties = maybe_unserialize( $meta['wp_booklet2_properties'][0] );
			
			$this->_width = $booklet_properties['width'];
			$this->_height = $booklet_properties['height'];
			$this->_speed = $booklet_properties['speed'];
			$this->_delay = $booklet_properties['delay'];
			$this->_direction = $booklet_properties['direction'];
			$this->_arrows_enabled = (boolean) $booklet_properties['arrows_enabled'];
			$this->_page_numbers_enabled = (boolean) $booklet_properties['page_numbers_enabled'];
			$this->_cover_behavior = $booklet_properties['cover_behavior'];
			$this->_padding = $booklet_properties['padding'];
			$this->_thumbnails_enabled = (boolean) $booklet_properties['thumbnails_enabled'];
			$this->_popup_enabled = (boolean) $booklet_properties['popup_enabled'];
			$this->_theme = $booklet_properties['theme'];
        
		}
		else {
				
			$this->_width = 600;
			$this->_height = 400;
			$this->_speed = 1000;
			$this->_delay = 5000;
			$this->_direction = "LTR";
			$this->_arrows_enabled = false;
			$this->_page_numbers_enabled = true;
			$this->_cover_behavior = "open";
			$this->_padding = 10;
			$this->_thumbnails_enabled = false;
			$this->_popup_enabled = false;
			$this->_theme = 'default';
			
		}

	}
	
	/**
	 * Associate page with booklet
	 *
	 * @param int $page_id - page ID
	 * 
	 * @return void
	 */
	function add_page($page_id) {
		$this->_pages[] = $page_id;
	}
	
	/** 
	 * Disassociate page from booklet
	 *
	 * @param int $page_id - page ID
	 *
	 * @return void
	 */
	function remove_page($page_id) {
		$flipped = array_flip( $this->_pages );
		unset( $flipped[ $page_id ] );
		$this->_pages = array_values( array_flip( $flipped ) );
	}
	
	/**
	 * Returns all pages associated with the booklet
	 *
	 * @return array - ids of associated pages
	 */
	function get_all_pages() {
		return $this->_pages;
	}
	
	/**
	 * Remove all pages of booklet
	 *
	 * @return void
	 */
	function remove_all_pages() {
		$this->_pages = array();
	}
	
	/**
	 * Set title
	 *
	 * @param string $title - booklet title
	 *
	 * @return void
	 */
	function set_title($title) {
		$this->_title = $title;
	}
	
	/** Get title
	 *
	 * @return string
	 */
	function get_title() {
		return $this->_title;
	}
	
	/**
     *  Set booklet width
     *
     * @param int $width - booklet width
     * 
     * @return void
     */
	function set_width($width) {
		$this->_width = $width;
	}
	
	/**
	 * Get booklet width
	 *
	 * @return int
	 */
	function get_width() {
		return $this->_width;
	}
	 
	/**
     * Set booklet height
     *
     * @param int $height - booklet height
     *
     * @return void
     */
	function set_height($height) {
		$this->_height = $height;
	}
	
	/**
	 * Get booklet height
	 *
	 * @return int
	 */
	function get_height() {
		return $this->_height;
	}
	
	/**
     * Set booklet speed
     *
     * @param int $speed - booklet speed in milliseconds
     *
     * @return void
     */
	function set_speed($speed) {
		$this->_speed = $speed;
	}
	
	/**
	 * Get booklet speed
	 *
	 * @return int
	 */
	function get_speed() {
		return $this->_speed;
	}
	
	/**
     * Set booklet delay
     *
     * @param int $delay - booklet delay in milliseconds
     *
     * @return void
     */
	function set_delay($delay) {
		$this->_delay = $delay;
	}
	
	/**
	 * Get booklet delay
	 *
	 * @return int
	 */
	function get_delay() {
		return $this->_delay;
	}
	
	/**
     * Set booklet direction
     *
     * @param string $direction - booklet direction. 'ltr' and 'rtl' options are available
     *
     * @return void
     */
	function set_direction($direction) {
		$this->_direction = $direction;
	}
	
	/**
	 * Get booklet direction
	 *
	 * @return string
	 */
	function get_direction() {
		return $this->_direction;
	}
	
	/**
     * Enable arrows
     *
     * @return void
     */
	function enable_arrows() {
		$this->_arrows_enabled = true;
	}
	
	/**
     * Disable arrows
     *
     * @return void
     */
	function disable_arrows(){
		$this->_arrows_enabled = false;
	}
	
	/**
	 * Check if arrows are enabled
	 *
	 * @return bool
	 */
	function are_arrows_enabled() {
		return $this->_arrows_enabled;
	}
	
	/**
     * Enable page numbers
     *
     * @return void
     */
	function enable_page_numbers() {
		$this->_page_numbers_enabled = true;
	}
	
	/**
     * Disable page numbers
     *
     * @return void
     */
	function disable_page_numbers() {
		$this->_page_numbers_enabled = false; 
	}
	
	/**
	 * Check if page numbers are enabled
	 *
	 * @return bool
	 */
	function are_page_numbers_enabled() {
		return $this->_page_numbers_enabled;
	}
	
	/**
     * Set cover behavior
     *
     * @param string $behavior - 'open', 'closed', 'center-closed'
     */
	function set_cover_behavior($behavior) {
		$this->_cover_behavior = $behavior;
	}
	
	/**
	 * Get cover behavior
	 *
	 * @return string
	 */
	function get_cover_behavior() {
		return $this->_cover_behavior;
	}
	
	/**
     * Set padding
     *
     * @param int $padding - booklet padding
     *
     * @return void
     */
	function set_padding($padding) {
		$this->_padding = $padding;
	}
	
	/**
	 * Get padding
	 *
	 * @return int
	 */
	function get_padding() {
		return $this->_padding;
	}
	
	/**
     * Enable thumbnails
     *
     * @return void
     */
	function enable_thumbnails() {
		$this->_thumbnails_enabled = true;
	}
	
	/** 
     * Disable thumbnails
     *
     *@return void
     */
	function disable_thumbnails() {
		$this->_thumbnails_enabled = false; 
	}
	
	/**
	 * Check if thumbnails are enabled
	 *
	 * @return bool
	 */
	function are_thumbnails_enabled() {
		return $this->_thumbnails_enabled;
	}
	
	/** 
     * Enable popups
     *
     * @return void
     */
	function enable_popups() {
		$this->_popup_enabled = true;
	}
	
	/**
     * Disable popups
     */
	function disable_popups(){
		$this->_popup_enabled = false; 
	}
	
	/**
	 * Check if popups are enabled
	 *
	 * @return bool
	 */
	function are_popups_enabled() {
		return $this->_popup_enabled;
	}
	
	/**
	 * Set theme
	 *
	 * @param string $theme - folder name of theme
	 *
	 * @return void
	 */
	function set_theme($theme) {
		$this->_theme = $theme;
	}
	
	/**
	 * Get theme
	 *
	 * @return void
	 */
	function get_theme() {
		return $this->_theme;
	}
	
	/**
	 * Get shortcode ID. This is the ID used in shortcodes 
	 * which may be the same or different from the internal ID.
	 * 
	 * @return int
	 */
	function get_shortcode_id() {
		
		if ( $this->_wp_booklet_1_id > 0  ) {
			return $this->_wp_booklet_1_id;
		}
		else {
			return $this->_id;
		}
		
	}
	
	/**
	 * Update booklet
	 *
	 * @return bool - true on success; false on failure
	 */
	function update_booklet() {
		
		$properties['width'] = sanitize_text_field( $this->_width );
		$properties['height'] = sanitize_text_field( $this->_height );
		$properties['speed'] = sanitize_text_field( $this->_speed );
		$properties['delay'] = sanitize_text_field( $this->_delay );
		$properties['direction'] = sanitize_text_field( $this->_direction );
		$properties['arrows_enabled'] = $this->_arrows_enabled;
		$properties['page_numbers_enabled'] = $this->_page_numbers_enabled;
		$properties['cover_behavior'] = sanitize_text_field( $this->_cover_behavior );
		$properties['padding'] = sanitize_text_field( $this->_padding );
		$properties['thumbnails_enabled'] = $this->_thumbnails_enabled;
		$properties['popup_enabled'] = sanitize_text_field( $this->_popup_enabled );
		$properties['theme'] = sanitize_text_field( $this->_theme );
		
		delete_post_meta( $this->_id, 'wp_booklet2_properties');
		$result_properties = update_post_meta( $this->_id, 'wp_booklet2_properties', $properties);
		
		delete_post_meta( $this->_id, 'wp_booklet2_pages' );
		$result_pages = update_post_meta( $this->_id, 'wp_booklet2_pages', $this->_pages );
		
		if ( $result_properties && $result_pages ) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Builds booklet's HTML
	 *
	 * @return string
	 */
	function get_output() {
		
		$booklet = $this;
		
		$instance_id = uniqid();
		$page_ids = $this->get_all_pages();
		$pages = array();
		
		if ( empty( $page_ids ) ) {
			$html = "Booklet is empty or it doesn't exist.";
			return $html;
		}
		
		foreach( $page_ids as $page_id ) {
			$pages[] = new WP_Booklet2_Page($page_id);
		}
		
		$theme_manager = new WP_Booklet2_Theme_Manager();
		$themes = $theme_manager->get_all_themes();
		
		if ( array_key_exists( $this->_theme, $themes ) ) {
			$theme_object = $themes[$this->_theme];
		}
		else {
			$theme_object = $theme_manager->get_default_theme();
		}
		
		ob_start();
		include $theme_object->get_booklet_template_path();
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
		
	}
	
	/**
	 * Delete booklet
     *
     * @param int $id - booklet id
     *
     * @return mixed - return values of wp_delete_post
     */
	static function delete_booklet($id) {
		wp_delete_post($id);
	}
	
	/**
	 * Create an empty booklet
	 *
	 * @param string $title - title of the booklet
	 * @param int $wp_booklet_1_id - (optional) WP Booklet 1.x ID if available
	 *
	 * @return int - ID of the new booklet
	 */
	 static function create_booklet($title,$wp_booklet_1_id=null) {
		
		$post = wp_insert_post( array (
					'post_title'=>$title,
					'post_type'=>'wp-booklet2',
					'post_status'=>'publish'
				));
		
		if ( $wp_booklet_1_id ) {
			update_post_meta ( $post, 'wp_booklet_1_id', $wp_booklet_1_id);
		}
		
		return $post;
	 }
}