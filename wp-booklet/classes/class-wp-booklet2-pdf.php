<?php

class WP_Booklet2_PDF {

	protected $_file;

	/**
	 * The constructor
	 *
	 * @param $file string - path to the PDF file
	 * 
	 * @return WP_Booklet2_PDF
	 */
	function __construct( $file ) {

		$this->_file = $file;

		if ( !$this->is_file_valid() ) {
			throw new Exception("Invalid PDF");
		}

	}

	/** 
	 * Get PDF page count
	 *
	 * return mixed - int on success, false on failure
	 */
	protected function _get_pdf_page_count() {

		$pdfinfo = new WP_Booklet2_PDFInfo($this->_file);
		
		return $pdfinfo->get_pages();
		
	}

	/**
     * Get path
     *
     * return string
     */
	function get_path() {
		return $this->_file;
	}

	/**
     * Return page count
	 *
	 * return int
	 */
	function get_pdf_page_count() {
			
		return $this->_get_pdf_page_count();
	
	}

	/**
	 * Checks that PDF is valid
	 *
	 * @return bool
	 */
	function is_file_valid() {
		$filetype = wp_check_filetype($this->_file);

		if ( $filetype['type'] == 'application/pdf' ) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if file can be converted into an image
	 *
	 * @return bool
	 */
	function is_convertible_to_image() {
		$wp_upload_dir = wp_upload_dir();
		$upload_path = $wp_upload_dir['path'];
		
		$pdf = $this->_file;
		$target = $upload_path . '/wp-booklet-test-' . uniqid() . '.jpg';
		$command = new WP_Booklet2_Command("convert", "-background white -alpha remove -limit memory 32MiB -limit map 64MiB {$pdf} {$target}");
		$result = $command->run_command();
		
		$pdfinfo = new WP_Booklet2_PDFInfo($pdf);

		if ( !$result['error'] && $pdfinfo->supports_environment() ) {
			$file_exists = file_exists( $target );
			@unlink( $target );
			return $file_exists;
		}
		else {
			return false;
		}
	}


	/** 
	 * Converts PDF images into Wordpress attachments
	 *
	 * @param $offset int - page number to start at. Defaults to first page.
	 * @param $limit int - number of pages to be converted into photos. Starts at offset. Defaults to 1
	 *
	 * @return mixed - array of attachments, false on error
	 */
	function get_pages_as_photos($offset=0,$limit=1) {
		
		//Set vars
		$pdf_path = $this->_file;
		$upload_dir = wp_upload_dir();
		$upload_path = $upload_dir['path'];
		$image_group = uniqid();
		$actual_page_count =  $this->_get_pdf_page_count( $pdf_path );
		
		//Identify start and end pages
		$end_page = $offset + ( $limit - 1 );
		
		if ( $end_page == $offset ) {
			$conversion_page_count = "[{$offset}]";
		}
		else {
			$conversion_page_count = "[{$offset}-{$end_page}]";
		}
		
		//Check that upload directory is writable by server
		if ( $upload_dir['error'] || !is_writable($upload_path) ) {
			return false;
		}
		
		//Use Imagemagick and Ghostscript to convert PDF pages into jpegs
		$command = new WP_Booklet2_Command("convert", "-background white -alpha remove -density 200 -verbose {$pdf_path}{$conversion_page_count} {$upload_path}/{$image_group}.jpg");
		$operation = $command->run_command();
		
		if ( $operation['error'] ) {
			return false;
		}

		//Fill this array with new attachments
		$images = array();

		for ( $ctr = 0; $ctr < $limit; $ctr++ ) {
			
			//If more than 1 page is converted, append a number
			if ( $limit > 1 ) {
				$filename = $upload_path."/".$image_group."-".$ctr.".jpg";
			}
			else {
				$filename = $upload_path."/".$image_group.".jpg";
			}
			
			//Prepare attachment
			$wp_filetype = wp_check_filetype(basename($filename), null );
			$attachment = array(
				'guid' => $upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content' => '',
				'post_status' => 'inherit'
			);

			//Insert attachment
			$attach_id = wp_insert_attachment( $attachment, $filename);

			//Create attachment metadata
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			$meta = wp_update_attachment_metadata( $attach_id, $attach_data );
			$size = getimagesize($filename);
			
			$images[] = array(
				'id'=>$attach_id,
				'src'=>$upload_dir['url'] . '/' . basename( $filename ),
				'width'=>$size[0],
				'height'=>$size[1]
			);

		}

		return $images;
	}

}