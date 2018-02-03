<?php

namespace Materia\Development\Patterns\MVC\Views\HTML\Templates;

/**
 * HTML template class
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

class PHP extends \Materia\Development\Patterns\MVC\Views\HTML\Template {

	protected $_blocks  = [];
	protected $_parents = [];

	/**
	 * Begin of a block
	 *
	 * @param	string	$name
	 **/
	protected function start( string $name ) {

		// Start output buffer (stack)
		ob_start();

		echo $this->_debug ? '<!-- ' . $name . ' -->' : NULL;

	}

	/**
	 * End of a block
	 *
	 * @param	string	$name
	 **/
	protected function stop( string $name ) {

		echo $this->_debug ? '<!-- /' . $name . ' -->' : NULL;

		if ( !isset( $this->_blocks[$name] ) ) {

			$this->_blocks[$name] = ob_get_contents();

		}

		ob_end_clean();

		return $this->_blocks[$name];

	}

	/**
	 * Extend a template
	 *
	 * @param	string	$templates
	 **/
	protected function extend( string $template ) {

		$this->_parents[] = $template;

	}

	/**
	 * Render a template
	 *
	 * @param	array	$templates
	 * @param	array	$data
	 **/
	public function render( array $templates, $data = NULL ) {

		// Start the output buffer
		if ( ob_get_level() < 1 ) {

			ob_start();

			$this->render( $templates, $data );

			return ob_get_clean();

		}

		if ( $template = $this->locate( $templates ) ) {

			ob_start();

			echo $this->_debug ? $this->debug( $templates, $template ) : NULL;

			// Require the file
			require( $template );

			ob_end_flush();

			// Require parent(s)
			foreach ( $this->_parents as &$parent ) {

				if ( $parent ) {

					$templates = [ $parent ];
					$parent    = FALSE;

					$this->render( $templates, $data );

				}

			}

		}
		else {

			// Print debug information
			echo $this->_debug ? $this->debug( $templates ) : NULL;

		}

	}

	/**
	 * Locate template file by matching the first occurence
	 *
	 * @param   array   $files
	 * @return  mixed
	 **/
	protected function locate( array $files ) {

		krsort( $this->_paths );

		// Iterate the queue (by priority)
		foreach ( $this->_paths as $path ) {

			foreach ( $files as $file ) {

				$current = $path . ltrim( $file, DIRECTORY_SEPARATOR ) . '.php';

				if ( file_exists( $current ) ) {

					return $current;

				}

			}

		}

	}

	/**
	 * Debug information
	 *
	 * @param	array	$templates
	 * @param	string	$file
	 * @return  string
	 **/
	protected function debug( array $templates, string $file = NULL ) {

		$file = substr( $file, 0, -4 );

		return PHP_EOL . '<!-- ' . PHP_EOL . array_reduce(
				$templates,
				function( $debug, $template ) use ( $file ) {

					return $debug . ( ( substr( $file, - strlen( $template ) ) == $template ) ? '  + ' : '  - ' ) . $template . PHP_EOL;

				},
				NULL
			) . '  -->' . PHP_EOL;

	}

}
