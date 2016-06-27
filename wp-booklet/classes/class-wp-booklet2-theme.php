<?php

class WP_Booklet2_Theme {
	
	private $_name;
	private $_directory;
	private $_url;
	
	/**
	 * The constructor
	 *
	 * @param string $name - the name of the theme
	 *
	 * @return WP_Booklet2_Theme
	 */
	function __construct($name) {
		$this->_name = $name;	
		
		$theme_locations = unserialize( WP_BOOKLET2_THEME_LOCATIONS );
		
		foreach( $theme_locations as $theme_location ) {
			
			if ( is_dir( $theme_location['theme_directory'] . DIRECTORY_SEPARATOR . $this->_name ) ) {
					
				$this->_directory = $theme_location['theme_directory'] . DIRECTORY_SEPARATOR . $this->_name;
				$this->_url = $theme_location['theme_url'] . '/' . $this->_name;
				
			}
			
		}
		
	}
	
	/**
	 * Get directory
	 *
	 * @return string
	 */
	function get_directory() {
		return $this->_directory;
	}
	
	/**
	 * Get URL
	 *
	 * @return string
	 */
	function get_url() {
		return $this->_url;
	}
	
	/**
	 * Get name
	 *
	 * @return string
	 */
	function get_name() {
		return $this->_name;
	}
	
	/**
	 * Get booklet.php path
	 *
	 * @return string
	 */
	function get_booklet_template_path() {
		return realpath( $this->_directory . DIRECTORY_SEPARATOR . "booklet.php" );
	}
	
}