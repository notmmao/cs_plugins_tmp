<?php
/**
 * Plugin Name: WP Booklet
 * Description: Allows creation of flip books using the jQuery Booklet plugin. Successor to WP Booklet 1.x
 * Version: 2.1.4
 * Author: BinaryStash
 * Author URI:  http://www.binarystash.net
 * License: GPLv2 (http://www.gnu.org/licenses/gpl-2.0.html)
 */
 
/*
 * Define constants
 */
if(!defined('WP_BOOKLET2_URL')){
	define('WP_BOOKLET2_URL', plugin_dir_url(__FILE__) );
}

if(!defined('WP_BOOKLET2_DIR')){
	define('WP_BOOKLET2_DIR', realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR );
}

if(!defined('WP_BOOKLET2_THEME_LOCATIONS')){
	define('WP_BOOKLET2_THEME_LOCATIONS', serialize( array(
		array(
			'theme_directory' => WP_BOOKLET2_DIR . "themes/booklet",
			'theme_url' => WP_BOOKLET2_URL . "themes/booklet"
		),
		array(
			'theme_directory' => get_stylesheet_directory() . DIRECTORY_SEPARATOR . "wpbooklet",
			'theme_url' => get_stylesheet_directory_uri() . "/wpbooklet"
		)
	) ) );
}

/**
 * Register booklet post type
 */
 
function wp_booklet_register_booklet_post_type() {

	$labels = array(
		'name' => _x( 'Booklet', 'post type general name', 'WP_BOOKLET2' ),
		'singular_name' => _x( 'Booklet', 'post type singular name', 'WP_BOOKLET2' ),
		'add_new' => _x( 'Add New Booklet', 'Booklet', 'WP_BOOKLET2' ),
		'add_new_item' => _x( 'Add New Booklet', 'WP_BOOKLET2' ),
		'edit_item' => _x( 'Edit Booklet', 'WP_BOOKLET2' ),
		'new_item' => _x( 'New Booklet', 'WP_BOOKLET2' ),
		'all_items' => _x( 'All Booklets', 'WP_BOOKLET2' ),
		'view_item' => _x( 'View Booklet', 'WP_BOOKLET2' ),
		'search_items' => _x( 'Search Booklet', 'WP_BOOKLET2' ),
		'not_found' => _x( 'No Booklet Found', 'WP_BOOKLET2' ),
		'not_found_in_trash' => _x( 'No Booklet Found in Trash', 'WP_BOOKLET2' ),
		'parent_item_colon' => '',
		'menu_name' => _x( 'Booklets', 'WP_BOOKLET2' )
	);
	 
	$args = array(
		'labels' => $labels,
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'supports' => array( 'title' )
	);

	register_post_type( 'wp-booklet2', $args );

}

add_action( 'init', 'wp_booklet_register_booklet_post_type' );

/**
 * Register booklet page post type
 */
function wp_booklet_register_booklet_page_post_type() {
	 
	$args = array(
		'public' => false,
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'supports' => array( 'thumbnail' )
	);

	register_post_type( 'wp-booklet2-page', $args );

} 

add_action( 'init', 'wp_booklet_register_booklet_page_post_type' );

/*
 * Include classes
 */
 
$wp_booklet_included_classes = array(
	'class-wp-booklet2-pdfinfo.php',
	'class-wp-booklet2-booklet.php',
	'class-wp-booklet2-page.php',
	'class-wp-booklet2-command.php',
	'class-wp-booklet2-controller.php',
	'class-wp-booklet2-pdf.php',
	'class-wp-booklet2-theme.php',
	'class-wp-booklet2-theme-manager.php',
	'class-wp-booklet2-importer.php',
	'class-wp-booklet2-booklet-editor.php',
	'class-wp-booklet2-settings-page.php',
	'class-wp-booklet2-importer-page.php'
); 
 
foreach( $wp_booklet_included_classes as $class ) {
	
	include WP_BOOKLET2_DIR . 'classes' . DIRECTORY_SEPARATOR . $class;
	
}

/**
 * Initialize plugin
 */
function wp_booklet2_instantiate() {
	new WP_Booklet2_Controller();
}

if ( get_bloginfo("version") >= 3.9 ) {
	//Initialize plugin only if Wordpress version >= 3.9
	add_action( 'plugins_loaded', 'wp_booklet2_instantiate', 15 );
}


