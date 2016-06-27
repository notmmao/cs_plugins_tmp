<?php

class WP_Booklet2_Booklet_Editor {
	
	function setUp() {
		
		//Create booklet metaboxes
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		
		//Save data
		add_action ( 'save_post', array( &$this, 'save_data' ) );
		
		//Add messages
		add_filter( 'post_updated_messages', array( &$this, 'modify_messages' ) );
		
		//Add shortcode column to booklet admin
		add_filter( 'manage_posts_columns', array( &$this, 'manage_booklet_columns' ) );
		add_filter( 'manage_posts_custom_column', array( &$this, 'manage_booklet_custom_columns' ), 10, 2);
		
		//Process PDF
        add_action('wp_ajax_process_pdf', array(&$this,'process_pdf') );	
	
        //Verify PDF
        add_action('wp_ajax_verify_pdf', array(&$this,'verify_pdf') );
		
		//Include admin scripts
		add_action( 'admin_enqueue_scripts', array( &$this, 'include_admin_scripts' ) );
		
	}
	
	/**
	 * Enqueue booklet editor scripts
	 *
	 * @return void
	 */
	function include_admin_scripts($hook_suffix) {
		
		if ( get_bloginfo("version") >= 3.8 ) {
			wp_enqueue_style( 'wpbooklet-booklet-editor-gt-3.8', WP_BOOKLET2_URL . 'themes/admin/default/css/booklet-editor-3.8.css' );
		}
		
		if ( get_post_type() != 'wp-booklet2' && $hook_suffix != 'post.php' ) { return; }
		
		wp_enqueue_style( 'wpbooklet-booklet-editor', WP_BOOKLET2_URL . 'themes/admin/default/css/booklet-editor.css' );
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_dequeue_script( 'autosave' );
		wp_enqueue_media();
		
		wp_enqueue_script('wpbooklet-booklet-editor', WP_BOOKLET2_URL . 'assets/js/wpbooklet-editor.js');
		
	}
	
	/**
	 * Add meta boxes to the editor
	 *
	 * @return void
	 */
	function add_meta_boxes() {
		
		if ( get_post_type() != 'wp-booklet2' ) {
			return;
		}
		
		//Create pages metabox
		add_meta_box(
			'booklet-pages-metabox',
			'Booklet Pages',
			array( &$this, 'create_pages_metabox' ),
			'wp-booklet2',
			'normal',
			'high'
		);
		
		//Create shortcode metabox
		add_meta_box( 
			'booklet-shortcode-metabox',
			'Booklet shortcode',
			array( &$this, 'create_shortcode_metabox' ),
			'wp-booklet2',
			'side',
			'low'
		);
		
		//Create themes metabox
		add_meta_box( 
			'booklet-themes-metabox',
			'Booklet Themes',
			array( &$this, 'create_themes_metabox' ),
			'wp-booklet2',
			'side',
			'low'
		);
		
		//Create properties metabox
		add_meta_box( 
			'booklet-properties-metabox',
			'Booklet Properties',
			array( &$this, 'create_properties_metabox' ),
			'wp-booklet2',
			'side',
			'low'
		);
		
	}
	
	/**
	 * Save booklet data
	 *
	 * @return void
	 */
	function save_data($post_id) {
		
		if ( empty( $_POST ) ) {
			return;
		} 
		
		$booklet = new WP_Booklet2_Booklet($post_id);
		
		//Save pages
		if ( !empty( $_POST['wp-booklet-attachment'] ) ) {
			
			$booklet->remove_all_pages();
			
			foreach ( $_POST['wp-booklet-attachment'] as $key => $attachment ) {
				if ( !empty($attachment) ) {
					
					$page = new WP_Booklet2_Page($attachment);
					$page->set_image($attachment);
					$page->set_page_link($_POST['wp-booklet-attachment-properties']['wp-booklet-page-link'][$key]);
					
					$page->update_page();
					
					$booklet->add_page($page->get_id());
					
				}
			}
			
		}
		
		//Save properties
		if ( !empty( $_POST['wp-booklet-metas'] ) ) {
			
			$booklet->set_width( $_POST['wp-booklet-metas']['wp-booklet-width'] );
			$booklet->set_height( $_POST['wp-booklet-metas']['wp-booklet-height'] );
			$booklet->set_speed( $_POST['wp-booklet-metas']['wp-booklet-speed'] );
			$booklet->set_delay( $_POST['wp-booklet-metas']['wp-booklet-delay'] );
			$booklet->set_direction( $_POST['wp-booklet-metas']['wp-booklet-direction'] );
			$booklet->set_cover_behavior( $_POST['wp-booklet-metas']['wp-booklet-closed'] );
			$booklet->set_padding( $_POST['wp-booklet-metas']['wp-booklet-padding'] );
			$booklet->set_theme( $_POST['wp-booklet-metas']['wp-booklet-theme'] );
			
			if ( $_POST['wp-booklet-metas']['wp-booklet-popups'] == "true" ) {
				$booklet->enable_popups();
			}
			else {
				$booklet->disable_popups();
			}
			
			if ( $_POST['wp-booklet-metas']['wp-booklet-arrows'] == "true" ) {
				$booklet->enable_arrows();
			}
			else {
				$booklet->disable_arrows();
			}
			
			if ( $_POST['wp-booklet-metas']['wp-booklet-pagenumbers'] == "true" ) {
				$booklet->enable_page_numbers();
			}
			else {
				$booklet->disable_page_numbers();
			}
			
			if ( $_POST['wp-booklet-metas']['wp-booklet-thumbnails'] == "true" ) {
				$booklet->enable_thumbnails();
			}
			else {
				$booklet->disable_thumbnails();
			}


		}
		
		$booklet->update_booklet();
		
	}
	
	/**
	 * Customize booklet editor messages
	 *
	 * @return array
	 */
	function modify_messages($messages) {
		global $post;
		
		if ( get_post_type() != 'wp-booklet2' ) {
			return $messages;
			exit;
		}
		
		$booklet = new WP_Booklet2_Booklet( $post->ID );
		
		$messages['wp-booklet2'] = array(
			"Shortcode is [wp-booklet id={$booklet->get_shortcode_id()}]",
			"Booklet updated. Shortcode is [wp-booklet id={$booklet->get_shortcode_id()}]",
			"Custom field updated.",
			"Custom field deleted.",
			"Booklet updated. Shortcode is [wp-booklet id={$booklet->get_shortcode_id()}]",
			"Shortcode is [wp-booklet id={$booklet->get_shortcode_id()}]",
			"Booklet published. Shortcode is [wp-booklet id={$booklet->get_shortcode_id()}]",
			"Booklet saved. Shortcode is [wp-booklet id={$booklet->get_shortcode_id()}]",
			"Booklet submitted",
			"Booklet scheduled",
			"Booklet draft updated"
		);
		
		return $messages;
	}
	
	/**
	 * Add new columns to the list of booklets
	 *
	 * @return array
	 */
	function manage_booklet_columns( $columns ) {
		if ( get_post_type() != 'wp-booklet2' ) {
			return $columns;
		}
	
		$columns = array_merge( $columns, array( 'shortcode' => 'Shortcode' ) );
		unset( $columns['date'] );
		
		return $columns;
		
	}

	/**
	 * Create new columns to be added to the list of booklets
	 *
	 * @return void
	 */
	function manage_booklet_custom_columns( $column, $id ) {
		if ( get_post_type() != 'wp-booklet2' ) {
			return;
		}
		
		$booklet = new WP_Booklet2_Booklet($id);
		
		switch( $column ) {
			case 'shortcode' : 
				echo "[wp-booklet id={$booklet->get_shortcode_id()}]";
				break;
		}
	}
	
	/**
	 * Processes uploaded PDF
	 *
	 * @return void
	 */
	function process_pdf() {
		
		$pdf_id = $_POST['pdf_id'];
		$pdf_offset = $_POST['pdf_offset'];
		$pdf_limit = 1;
		$pdf_path = get_attached_file( $pdf_id );
		
		$pdf = new WP_Booklet2_PDF($pdf_path);
		
		$return = array(
			'wpb_success'=>false,
			'wpb_message'=>'An unknown error occurred.'
		);
		
		$images = $pdf->get_pages_as_photos($pdf_offset);
		
		if ( $images ) {
			
			$return = array (
				'images'=>$images,
				'wpb_success'=>true,
				'wpb_message'=>'PDF processing succeeded.'
			);
			
		}

		echo json_encode($return);
		die();
		
	}
	
	/**
	 * Ensures that uploaded PDF is valid
	 *
	 * @return void
	 */
	function verify_pdf() {
		$pdf_id = $_POST['pdf_id'];
		$pdf_path = get_attached_file( $pdf_id );
	
		//Check file extension
		try {
			$pdf = new WP_Booklet2_PDF($pdf_path);
			$result = array( 
				'wpb_success'=>true,
				'wpb_page_count'=>$pdf->get_pdf_page_count()
			);
		}
		catch(Exception $e) {
			$result = array ( 
				'wpb_success'=>false, 
				'wpb_message' => 'Please select a valid PDF file.'
			);
		}
		
		//Get maximum_execution_time
		$result['max_execution_time'] = ini_get('max_execution_time');
		
		echo json_encode($result);
		die();
	}
	
	/**
	 * Render pages metabox
	 *
	 * @return void
	 */
	function create_pages_metabox( $post ) {
		
		if ( get_post_type() != 'wp-booklet2' ) {
			return;
		}
		
		//Get booklet
		$booklet = new WP_Booklet2_Booklet($post->ID);
		$page_ids = $booklet->get_all_pages();
		
		$pages = array();
		if ( !empty( $page_ids ) ) {
			foreach( $page_ids as $page_id ) {
				$pages[] = new WP_Booklet2_Page($page_id);
			}
		}
		
		//Can produce PDF?
		try {
			$pdf = new WP_Booklet2_PDF( realpath( WP_BOOKLET2_DIR . 'assets/pdf/test.pdf' ) );
			$pdf_capable = $pdf->is_convertible_to_image();
		}
		catch (Exception $e) {
			$pdf_capable = false;
		}
		
		include realpath( WP_BOOKLET2_DIR . "themes/admin/default/pages-metabox.php" );
	}
	
	/**
	 * Render shortcode metabox
	 *
	 * @return void
	 */
	function create_shortcode_metabox( $post ) {
		
		$booklet = new WP_Booklet2_Booklet($post->ID);
		
		include realpath( WP_BOOKLET2_DIR . "themes/admin/default/shortcode-metabox.php" );
	
	}
	
	/**
	 * Render themes metabox
	 *
	 * @return void
	 */
	function create_themes_metabox( $post ) {
		
		$booklet = new WP_Booklet2_Booklet($post->ID);
		$theme_manager = new WP_Booklet2_Theme_Manager();
		$themes = $theme_manager->get_all_themes();
		$default_theme = $theme_manager->get_default_theme();
		$default_theme_name = $default_theme->get_name();
		
		include realpath( WP_BOOKLET2_DIR . "themes/admin/default/themes-metabox.php" );
	
	}
	
	/**
	 * Render properties metabox
	 *
	 * @return void
	 */
	function create_properties_metabox( $post ) {
		
		$booklet = new WP_Booklet2_Booklet($post->ID);
		$theme_manager = new WP_Booklet2_Theme_Manager();
		$themes = $theme_manager->get_all_themes();
		
		include realpath( WP_BOOKLET2_DIR . "themes/admin/default/properties-metabox.php" );
	
	}
	
}