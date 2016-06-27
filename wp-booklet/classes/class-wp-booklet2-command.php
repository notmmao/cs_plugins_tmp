<?php

class WP_Booklet2_Command {

	protected $_command;
	protected $_modifiers;

	/** 
	 * The constructor
	 * 
	 * @param $command string - the command
	 * @param $modifiers string - command arguments/options
	 *
	 * @return WP_Booklet2_Command
	 */
	function __construct( $command, $modifiers) {

		$this->_command = $command;
		$this->_modifiers = $modifiers;
		
		if ( !$this->_can_run_exec() ) {
			throw new Exception("Running external programs is disabled");
		}
		
		if ( !$this->_command_exists() ) {
			throw new Exception("Command or program doesn't exist");
		}

	}

	/** 
	 * Checks existence of the command 
	 *
	 */
	protected function _command_exists() {
		$existence = exec( $this->_command ." ". $this->_modifiers . " 2>&1", $output, $return_var );

		return $this->_is_exit_code_valid($return_var);
	}
	
	/**
     * Exit code is valid
     * 
     * @param $code int - exit code
     *
     * @return bool
     */
	protected function _is_exit_code_valid($code) {
		$valid_codes = array( 0, 99 );
		return in_array( $code, $valid_codes );
	}

	/**
	 * Run an external command or program
	 *
	 * @return array - 	An array that contains 'error' status and 'message'
	 */
	public function run_command() {
		try {
			exec( $this->_command . " " . $this->_modifiers . " 2>&1", $output, $return_var );
		
			if ( $this->_is_exit_code_valid($return_var) ) {
				$result['error'] = false;
				$result['message'] = implode(" ",$output);
				$result['output'] = $output;
			}
			else {
				$result['error'] = true;
				$result['message'] = "Command exited with error code: " . $return_var;
			}
		}
		catch(Exception $e) {
			$result['error'] = true;
			$result['message'] = $e->getMessage();
		}
		
		return $result;
	}
	
	/**
	 * Checks if 'exec' is executable
	 *
	 * @return bool;
	 */
	protected function _can_run_exec() {
		return 	function_exists('exec') &&
				!in_array('exec', array_map('trim', explode(', ', ini_get('disable_functions')))) &&
				strtolower(ini_get('safe_mode')) != 1;
	}

}