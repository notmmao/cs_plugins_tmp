<?php

class WP_Booklet2_Importer_Page {
	
	function setUp() {
		
		//Add importer page
		add_action('admin_menu', array( &$this, 'booklet_import_page' ));
		
		//Importer action handlers
		add_action('wp_ajax_import_booklets', array(&$this,'import_booklets') );
		add_action( 'admin_notices', array(&$this,'import_notice') );
		
		//Include admin scripts
		add_action( 'admin_enqueue_scripts', array( &$this, 'include_admin_scripts' ) );
	
	}
	
	/**
	 * Enqueue importer scripts
	 *
	 * @return void
	 */
	function include_admin_scripts($hook_suffix) {
		
		wp_enqueue_script('jquery');
		
		if ( $hook_suffix == 'wp-booklet2_page_wp-booklet2-import' ) {
			wp_enqueue_style( 'wpbooklet-importer-css', WP_BOOKLET2_URL . 'themes/admin/default/css/importer.css' );
			wp_enqueue_script( 'wpbooklet-importer-js', WP_BOOKLET2_URL . 'assets/js/wpbooklet-importer-page.js' );
		}
		
	}
	
	/**
	 * Create the importer page
	 *
	 * @return void
	 */
	function booklet_import_page() {
		add_submenu_page( 'edit.php?post_type=wp-booklet2', 'Import', 'Import', 'manage_options', 'wp-booklet2-import', array( &$this, 'display_import_page') ); 
	}
	
	/**
	 * Render the importer page
	 *
	 * @return void
	 */
	function display_import_page() {
		
		$importer = new WP_Booklet2_Importer();
		$remaining_booklets = $importer->getRemainingBookletCount();
		
		include realpath( WP_BOOKLET2_DIR . "themes/admin/default/importer-page.php" );
		
	}
	
	/**
	 * Make booklets 2.0 compatible
	 *
	 * @return void
	 */
	function import_booklets() {
		
		$limit = 1;
		
		$importer = new WP_Booklet2_Importer($limit);
		$importer->import();
		
		$result = array(
			'remaining'=>$importer->getRemainingBookletCount()
		);
		
		echo json_encode( $result );
		die();
		
	}
	
	/**
	 * Render nag
	 *
	 * @return void
	 */
	function import_notice() {
		
		if ( isset ( $_GET['page'] ) ) {
			if ( $_GET['page'] == 'wp-booklet2-import' ) {
				return;
			}
		}
		
		$importer = new WP_Booklet2_Importer();
		
		if ( $importer->getRemainingBookletCount() > 0 ) {
			include realpath( WP_BOOKLET2_DIR . "themes/admin/default/import-nag.php" );
		}
	}
	
}