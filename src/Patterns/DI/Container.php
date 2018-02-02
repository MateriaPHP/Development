<?php

namespace Materia\Development\Patterns\DI;

/**
 * A simple Dependency Injection container
 *
 * @package Materia.Development
 * @author  Filippo Bovo
 * @link    https://lab.alchemica.io/projects/materia/
 **/

class Container {

	protected $_definitions = [];
	protected $_aliases     = [];

	/**
	 * Register a shared object or instance
	 *
	 * @param	mixed	$definition
	 * @return	self
	 **/
	public function register( $definition ) : self {

		// Lazy loader instance
		if ( $definition instanceof \Materia\Development\Patterns\Lazy ) {

			$class = $this->normalizeClass( $definition->__toString() );

		}
		// Class name
		else if ( is_string( $definition ) ) {

			$class = $definition;

		}
		// Instanced class
		else if ( is_object( $definition ) ) {

			$class = $this->normalizeClass( get_class( $definition ) );

		}
		// Invalid definition
		else {

			throw new \InvalidArgumentException( sprintf( 'Argument 1 passed to %s must be an object or a string, %s given', __METHOD__, gettype( $definition ) ) );

		}

		// Already defined
		if ( isset( $this->_definitions[$class] ) ) {

			throw new \RuntimeException( sprintf( 'Duplicate definition for %s', $class ) );

		}
		else {

			$this->_definitions[$class] = $definition;

		}

		return $this;

	}

	/**
	 * Retrieves a shared object or instance
	 *
	 * @param	string	$class		shared class name
	 * @param	boolean	$execute	execute definition
	 * @return	object
	 **/
	public function get( string $class, bool $execute = FALSE ) {

		$class = $this->normalizeClass( $class );

		if ( isset( $this->_definitions[$class] ) ) {

			if ( $execute ) {

				return $this->execute( $this->_definitions[$class] );

			}
			else {

				return $this->_definitions[$class];

			}

		}
		else {

			$alias = $this->resolveAlias( $class );

			if ( $class != $alias ) {

				return $this->get( $alias, $execute );

			}

		}

	}

	/**
	 * Set a global alias
	 *
	 * @param   string  $alias   	name of alias
	 * @param   string  $original   original class name
	 **/
	public function alias( string $alias, string $original ) : self {

		$alias                  = $this->normalizeClass( $alias );
		$this->_aliases[$alias] = $this->normalizeClass( $original );

		return $this;

	}

	/**
	 * Instantiate a class with shared objects
	 *
	 * @param	string	$class		name of the class to instatiate
	 * @param	array	$arguments	overridden arguments
	 * @return	object
	 **/
	public function make( string $class, array $arguments = [] ) {

		$reflector = new \ReflectionClass( $class );

		if ( $constructor = $reflector->getConstructor() ) {

			$params = $constructor->getParameters();

			// Iterate through constructor's params
			foreach ( $params as $param ) {

				// Try to get the name of the required class instance from type hint
				if ( $class = $param->getClass() ) {

					$pos  = $param->getPosition();
					$name = $this->resolveAlias( $this->normalizeClass( $class->name ) );

					// Override the definition
					if ( isset( $arguments[$pos] ) ) {

						// Look for an alias (interface, parent, ...)
						if ( is_string( $arguments[$pos] ) && isset( $this->_definitions[$name] ) ) {

							$arguments[$pos] = $this->execute( $this->_definitions[$name] );

							continue;

						}
						// Do not use the shared instance, but a different one
						else if ( $arguments[$pos] instanceof $name ) {

							continue;

						}

					}
					// Try to get the required instance from shared definitions
					else if ( isset( $this->_definitions[$name] ) ) {

						$arguments[$pos] = $this->execute( $this->_definitions[$name] );

						continue;

					}

					// if ( !$param->isOptional() ) {
					// 	throw new \Exception("Error Processing Request", 1);
					// }

				}

			}

			ksort( $arguments );

			// Instantiate the class with arguments
			$instance = $reflector->newInstanceArgs( $arguments );

			return $instance;

		}
		else {

			// Instantiate the class without arguments
			$instance = $reflector->newInstance();

			return $instance;

		}

	}

	/**
	 * Format the name of a class
	 *
	 * @param	string	$class	name of the class
	 * @return	string
	 **/
	private function normalizeClass( string $class ) : string {

		$class = ltrim( strtolower( $class ), '\\' );

		return $class;

	}

	/**
	 * Resolve the name of an alias
	 *
	 * @param	string	$class	name of the alias
	 * @return	string
	 **/
	private function resolveAlias( string $name ) : string {

		if ( isset( $this->_aliases[$name] ) ) {
			//
			$name = $this->resolveAlias( $this->_aliases[$name] );

		}

		return $name;

	}

	/**
	 * Execute the definition
	 *
	 * @param	mixed	$definition		definition to execute
	 * @return	object
	 **/
	private function execute( &$definition ) {

		// Lazy load
		if ( is_callable( $definition ) ) {

			$definition = call_user_func( $definition );
		}
		// Lazy load (simple)
		else if ( is_string( $definition ) ) {

			$definition = new $definition();

		}

		return $definition;

	}

}
