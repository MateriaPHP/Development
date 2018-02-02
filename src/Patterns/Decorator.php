<?php

namespace Materia\Development\Patterns;

/**
 * A basic implementation of decorator pattern
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

abstract class Decorator {

	protected $_target;

	/**
	 * Constructor
	 *
	 * @param	$target
	 **/
	public function __construct( $target ) {

		$this->_target = $target;

	}

	/**
	 * @param	string	$method		called method
	 * @param	array	$arguments	arguments to pass
	 * @return	mixed
	 **/
	public function __call( string $method, array $arguments = [] ) {

		if ( is_callable( [ $this->_target, $method ] ) ) {

			return call_user_func_array( [ $this->_target, $method ], $arguments );

		}

	}

}
