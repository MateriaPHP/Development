<?php

namespace Materia\Development\Patterns;

/**
 * Lazy load/initialization
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class Lazy {

	private $_class;
	private $_arguments;
	private $_calls;
	private $_instance;

	/**
	 * Constructor
	 *
	 * @param   string  $class      class name
	 * @param   array   $arguments  constructor arguments
	 **/
	public function __construct( string $class, array $arguments = [] ) {

		$this->_class     = $class;
		$this->_arguments = $arguments;
		$this->_calls     = [];

	}

	/**
	 * Treat class like a string
	 *
	 * @return	string
	 **/
	public function __toString() : string {

		return $this->_class;

	}

	/**
	 * Instantiate the class
	 *
	 * @return	object
	 **/
	public function __invoke() {

		if ( !isset( $this->_instance ) ) {

			$reflector       = new \ReflectionClass( $this->_class );
			$this->_instance = $reflector->newInstanceArgs( $this->_arguments );

			foreach ( $this->_calls as $call ) {

				list( $method, $arguments ) = $call;

				call_user_func_array( [ $this->_instance, $method ], $arguments );

			}

		}

		return $this->_instance;

	}

	/**
	 * Append a callback to a method after class instantialization
	 *
	 * @param	string	$method		method name
	 * @param	array	$arguments	constructor arguments
	 * @return	self
	 **/
	public function setCallback( string $method, array $arguments = [] ) : self {

		$this->_calls[] = [ $method, $arguments ];

		return $this;

	}

}
