<?php

class WP_Booklet2_Controller {

	function __construct() {

		//Initialize booklet editor
		$booklet_editor = new WP_Booklet2_Booklet_Editor();
		$booklet_editor->setUp();

		//Initialize settings page
		$settings_page = new WP_Booklet2_Settings_Page();
		$settings_page->setUp();
		
		//Initialize importer page
		$importer_page = new WP_Booklet2_Importer_Page();
		$importer_page->setUp();
	
		//Initialize front-end components
		$theme_manager = new WP_Booklet2_Theme_Manager();
		$theme_manager->setUpDependencies();
		$theme_manager->setUpShortcode();
		
		
	}
}