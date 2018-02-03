<?php

namespace Materia\Development\Patterns\MVC\Views\HTML;

/**
 * Template class
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

abstract class Template {

	protected $_debug;

	protected $_callbacks = [];
	protected $_paths     = [];
	protected $_output    = FALSE;

	/**
	 * Constructor
	 *
	 * @param	bool	$debug
	 **/
	public function __construct( bool $debug = FALSE ) {

		$this->_debug = $debug;

	}

	/**
	 **/
	public function __call( $method, $params ) {

		if ( isset( $this->_callbacks[$method] ) ) {

			return call_user_func_array( $this->_callbacks[$method], $params );

		}

	}

	/**
	 * Register a callback
	 *
	 * @param	string		$name
	 * @param	callable	$callback
	 * @return	self
	 **/
	public function registerCallback( string $name, callable $callback ) : self {

		$this->_callbacks[$name] = $callback;

		return $this;

	}

	/**
	 * Unregister a callback
	 *
	 * @param	string		$name
	 * @return	self
	 **/
	public function unregisterCallback( string $name ) : self {

		if ( isset( $this->_callbacks[$name] ) ) {

			unset( $this->_callbacks[$name] );

		}

		return $this;

	}

	/**
	 * Append a path to the list
	 *
	 * @param	string	$path
	 * @param	integer	$priority
	 * @return	self
	 **/
	public function addPath( string $path, int $priority = 1 ) : self {

		// Normalize path
		$path = rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;

		if ( in_array( $path, $this->_paths ) ) {

			return $this;

		}

		// Normalize priority
		if ( $priority < 1 ) {

			$priority = 100;

		}
		else if ( $priority > 9 ) {

			$priority = 900;

		}
		else {

			$priority = $priority * 100;

		}

		$priority = array_reduce(
			array_keys( $this->_paths ),
			function( $max, $v ) use ( $priority ) {

				return ( $v >= $priority ) && ( $v < ( $priority + 100 ) ) ? max( $v, $max ) : $max;

			},
			100
		);

		$this->_paths[++$priority] = $path;

		return $this;

	}

	/**
	 * Remove a path from the list
	 *
	 * @param	string	$path
	 * @return	self
	 **/
	public function removePath( string $path ) {

		if ( $index = array_search( $path, $this->_paths ) ) {

			unset( $this->_paths[$index] );

		}

		return $this;

	}

	/**
	 * Render a template
	 *
	 * @param	array	$templates
	 * @param	mixed	$data
	 **/
	abstract public function render( array $templates, $data = NULL );

}
