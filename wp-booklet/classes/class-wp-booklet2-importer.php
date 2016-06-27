<?php

class WP_Booklet2_Importer {
	
	private $_limit;
	
	/**
	 * Constructor
	 *
	 * @param int $limit - (optional) number of booklets to be imported each time the function runs. Defaults to 1 
	 *
	 * @return WP_Booklet2_Importer
	 */
	function __construct($limit=1) {
		
		$this->_limit = $limit;
		
	}
	
	/**
	 * Get limit
	 *
	 * @return int
	 */
	function getLimit() {
		return $this->_limit;
	}
	
	/**
	 * Set limit
	 *
	 * @param int $limit - number of booklets to be imported each time the function runs. 
	 *
	 * @return void
	 */
	function SetLimit($limit) {
		$this->_limit = $limit;
	}
	
	/**
	 * Import WP Booklet 1 booklets
	 *
	 * @return void
	 */
	function import() {
	
		//Fetch WP Booklet 1 booklets
		
		$args = array(
			'post_type'=>'wp-booklet',
			'posts_per_page'=>$this->_limit,
			'meta_query'=>array(
				array( 
					'key'=>'wp_booklet2_import_date',
					'compare'=>'NOT EXISTS'
				)
			)
		);
		
		$query = new WP_Query($args);

		foreach( $query->posts as $key=>$post ) {
			$old_booklets[$key]['post'] = $post;
			$old_booklets[$key]['meta'] = get_post_custom($post->ID);
		}
	
		//Loop through the old booklets
	
		foreach ( $old_booklets as $old_booklet ) {
			
			//Create new booklet
			$new_booklet_id = WP_Booklet2_Booklet::create_booklet( $old_booklet['post']->post_title, $old_booklet['post']->ID );
			$new_booklet = new WP_Booklet2_Booklet($new_booklet_id);
			
			//Save pages
			$old_pages = maybe_unserialize( $old_booklet['meta']['wp_booklet_pages'][0] );
			$old_pages_properties = maybe_unserialize( $old_booklet['meta']['wp_booklet_pages_properties'][0] );
			
			foreach( $old_pages as $key=>$old_page ) {
				
				$new_page = new WP_Booklet2_Page($old_page);
				$new_page->set_image($old_page);
				
				if ( isset( $old_pages_properties['key']['wp_booklet_page_link'] ) ) {
					$new_page->set_page_link($old_pages_properties['key']['wp_booklet_page_link']);
				}
				
				$new_page->update_page();
				
				$new_booklet->add_page( $new_page->get_id() );
				
			}
			
			//Save properties
			$old_properties = maybe_unserialize( $old_booklet['meta']['wp_booklet_metas'][0] );
			
			$new_booklet->set_width( $old_properties['wp-booklet-width'] );
			$new_booklet->set_height( $old_properties['wp-booklet-height'] );
			$new_booklet->set_speed( $old_properties['wp-booklet-speed'] );
			$new_booklet->set_delay( $old_properties['wp-booklet-delay'] );
			$new_booklet->set_direction( $old_properties['wp-booklet-direction'] );
			$new_booklet->set_padding( $old_properties['wp-booklet-padding'] );
			
			$theme_manager = new WP_Booklet2_Theme_Manager();
			$default_theme = $theme_manager->get_default_theme()->get_name();
			$new_booklet->set_theme( $default_theme );
			
			switch ( $old_properties['wp-booklet-closed'] ) {
				
				case 'false' :
					$new_booklet->set_cover_behavior('open');
					break;
				
				case 'true' :
					$new_booklet->set_cover_behavior('closed');
					break;
				
				case 'closable-centered' :
					$new_booklet->set_cover_behavior('center-closed');
					break;
				
			}
			
			if ( $old_properties['wp-booklet-popup'] == "true" ) {
				$new_booklet->enable_popups();
			}
			else {
				$new_booklet->disable_popups();
			}
			
			if ( $old_properties['wp-booklet-arrows'] == "true" ) {
				$new_booklet->enable_arrows();
			}
			else {
				$new_booklet->disable_arrows();
			}
			
			if ( $old_properties['wp-booklet-pagenumbers'] == "true" ) {
				$new_booklet->enable_page_numbers();
			}
			else {
				$new_booklet->disable_page_numbers();
			}
			
			if ( $old_properties['wp-booklet-thumbnails'] == "true" ) {
				$new_booklet->enable_thumbnails();
			}
			else {
				$new_booklet->disable_thumbnails();
			}
			
			if( $new_booklet->update_booklet() ) {
				
				delete_post_meta( $old_booklet['post']->ID, 'wp_booklet2_import_date' );
				update_post_meta( $old_booklet['post']->ID, 'wp_booklet2_import_date', time() );
		
			}
			
		}

	}
	
	/**
	 * Get the count of remaining booklets to be imported
	 *
	 * @return int - booklet count
	 */
	function getRemainingBookletCount() {
		
		$args = array(
			'post_type'=>'wp-booklet',
			'posts_per_page'=>-1,
			'meta_query'=>array(
				array(
					'key'=>'wp_booklet2_import_date',
					'compare'=>'NOT EXISTS'
				)
			)
		);
		
		$query = new WP_Query($args);
		
		return $query->post_count;
		
	}
	
	
}