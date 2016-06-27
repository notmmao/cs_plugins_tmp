<?php

class WP_Booklet2_Theme_Manager {
	
	private $_themes;
	private $_default_theme;
	
	/**
	 * The constructor
	 *
	 * @return WP_Booklet2_Theme_Manager
	 */
	function __construct() {
		
		$this->_default_theme = 'light';
		
		$theme_locations = unserialize( WP_BOOKLET2_THEME_LOCATIONS );
		
		foreach( $theme_locations as $theme_location ) {
			
			if ( !is_dir( $theme_location['theme_directory'] ) ) { return; }
			
			$themes = scandir( $theme_location['theme_directory'] );
			
			foreach ( $themes as $theme ) {
				
				if ( is_dir( $theme_location['theme_directory'] . DIRECTORY_SEPARATOR . $theme ) && $theme != '.' && $theme !== '..' ) {
					
					$theme_object = new WP_Booklet2_Theme($theme);
					$this->_themes[$theme] = $theme_object;
					
				}
				
			}
			
		}
		
	}
	
	/**
	 * Get all themes
	 *
	 * @return array
	 */
	function get_all_themes() {
		return $this->_themes;
	}
	
	/**
	 * Get default theme
	 *
	 * @return WP_Booklet2_Theme
	 */
	function get_default_theme() {
		$themes = $this->_themes;
		return $themes[ $this->_default_theme ];
	}
	
	/**
	 * Checks theme's existence
	 *
	 * @param string $theme - theme name
	 *
	 * @return bool
	 */
	function theme_exists($theme) {
		return array_key_exists( $theme, $this->_themes );
	}
	
	/**
	 * Set up theme dependencies
	 *
	 * @return void
	 */
	function setUpDependencies() {
		
		add_action( 'wp_enqueue_scripts', array( $this, 'include_frontend_scripts' ), 100);
		
	}
	
	/**
	 * Enqueue front end scripts
	 *
	 * @return void
	 */
	function include_frontend_scripts() {
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-effects-core' );
		
		wp_register_script( 'jquery-wpbooklet', WP_BOOKLET2_URL . 'assets/js/jquery.wpbooklet.js' );
		wp_register_script( 'jquery-wpbookletcarousel', WP_BOOKLET2_URL . 'assets/js/jquery.wpbookletcarousel.js' );
		wp_register_script( 'jquery-wpbookletimagepopup', WP_BOOKLET2_URL . 'assets/js/jquery.wpbooklet-image-popup.min.js' );
		wp_register_script( 'jquery-wpbookletextended', WP_BOOKLET2_URL . 'assets/js/jquery.wpbooklet-extended.js' );
		
		wp_enqueue_script( 'jquery-wpbooklet' );
		wp_enqueue_script( 'jquery-wpbookletcarousel' );
		wp_enqueue_script( 'jquery-wpbookletimagepopup' );
		wp_enqueue_script( 'jquery-wpbookletextended' );
		
		foreach( $this->_themes as $theme ) {
			
			wp_enqueue_style( 'wp-booklet-' . $theme->get_name(), $theme->get_url() . "/booklet.css" );
			
		}
		
	}
	
	/**
	 * Set up shortcode
	 * 
	 * @return void
	 */
	function setUpShortcode() {
		
		add_shortcode ( 'wp-booklet', array( $this, 'process_shortcode' ) );
		
	}
	
	/**
	 * Process shortcode
	 *
	 * @return void
	 */
	function process_shortcode($atts) {
		
		extract( $atts );
		
		$booklet = new WP_Booklet2_Booklet($id);
		
		return $booklet->get_output();
		
	}
	 
}