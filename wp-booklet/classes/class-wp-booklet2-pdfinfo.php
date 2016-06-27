<?php

class WP_Booklet2_PDFInfo {
	
	protected $_os;
	protected $_program_path;
	protected $_pdf;

	function __construct($pdf) {
		
		$this->_os = PHP_OS;
		$this->_pdf = $pdf;
		$this->_program_path = $this->_which_program();
		
	}

	/**
     * Choose pdfinfo version depending on operating system
	 *
     * @return string
     */
	private function _which_program() {
		
		if ( stristr ( $this->_os, 'WIN' ) ) {
			return WP_BOOKLET2_DIR . 'assets' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'pdfinfowin.exe';
		}
		else {
			return 'pdfinfo';
		}

	}
	
	/**
	 * Return version
     *
     * @return mixed - string if available, false otherwise
	 */
	 function get_version() {
		
		try {
			$cmd = new WP_Booklet2_Command($this->_program_path," " . $this->_pdf . " -v");
			$operation = $cmd->run_command();
			return $operation['message'];
		}
		catch (Exception $e) {
			return false;
		}

	}

	/**
     * Return page count
     *
     * @return mixed - int on success, false on failure
     */
	function get_pages() {

		if ( !$this->supports_environment() ) { return false; }
		
		try {
			$command = $this->_program_path;
			$modifiers = $this->_pdf;

			$operation = new WP_Booklet2_Command($command,$modifiers);
			$output = $operation->run_command();

			foreach($output['output'] as $line) {	
				
				if(preg_match("/Pages:\s*(\d+)/i", $line, $matches) === 1) {
					return intval($matches[1]);
				}
			}
		}
		catch ( Exception $e ) {
			return false;
		}

	}


	/**
     * Check if there is an available pdfinfo version for operating system
     *
     * @return bool
     */
	function supports_environment() {
		
		if ( !$this->get_version() ) {
			return false;
		}

		return true;
	
	}



}