<?php

namespace Materia\Development\Patterns\MVC\Views\HTML;

/**
 * Abstract Tag class
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

abstract class Tag {

	const NAME  = NULL;
	const EMPTY = FALSE;

	protected $_before     = [];
	protected $_after      = [];
	protected $_attributes = [];
	protected $_wrapper    = NULL;
	protected $_content    = NULL;
	protected $_rendered   = FALSE;

	/**
	 * Constructor
	 *
	 * @param	array	$attributes		Additional data for the HTML element
	 * @param	mixed	$content		Content of the HTML element
	 **/
	public function __construct( array $attributes = [], string $content = NULL ) {

		// Set attributes
		if ( $attributes ) {

			$this->setAttributes( $attributes );

		}

		// Set content
		if ( $content ) {

			$this->setContent( $content );

		}

	}

	/**
	 * Render the tag
	 *
	 * @return	string
	 **/
	public function __toString() : string {

		if ( !$this->_rendered ) {

			$this->_rendered = TRUE;

			return $this->render();

		}

		return '';

	}

	/**
	 * Returns rendered version of the tag
	 *
	 * @param	array	$whitelist
	 * @param	array	$blacklist
	 * @return	string
	 **/
	public function render( array $whitelist = [], array $blacklist = [] ) : string {

		if ( static::EMPTY ) {

			$element = '<' . static::NAME . $this->getAttributesAsString( $whitelist, $blacklist ) . ' />';

		}
		else {

			$element = '<' . static::NAME . $this->getAttributesAsString( $whitelist, $blacklist ) . '>' . implode( PHP_EOL, $this->_before ) . $this->_content . ( $this->_content && $this->_after ? PHP_EOL : '' ) . implode( PHP_EOL, $this->_after ) . '</' . static::NAME . '>';

		}

		if ( $this->_wrapper ) {

			$element = $this->_wrapper->setContent( $element )->render();

		}

		return $element;

	}

	/**
	 * Set multipe attributes to the HTML element
	 *
	 * @param	array	$attributes
	 * @return	self
	 **/
	public function setAttributes( array $attributes ) : self {

		foreach ( $attributes as $name => $value ) {

			$this->setAttribute( $name, $value );

		}

		return $this;

	}

	/**
	 * Set a single attribute to the HTML element
	 *
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	self
	 **/
	public function setAttribute( string $name, $value ) : self {

		if ( $name = $this->cleanAttributeName( $name ) ) {

			$this->_attributes[$name] = $value;

		}

		return $this;

	}

	/**
	 * Clean-up attribute name
	 *
	 * @param	string	$name
	 * @return	string
	 **/
	protected function cleanAttributeName( string $name ) : string {

		return preg_replace( '/[^a-zA-Z0-9-]+/', '', $name );

	}

	/**
	 * Returns some or all of the HTML element's attributes
	 *
	 * @param	array	$whitelist
	 * @param	array	$blacklist
	 * @return	array
	 **/
	public function getAttributes( array $whitelist = [], array $blacklist = [] ) : array {

		$results = $this->_attributes;

		// Apply filters
		if ( $whitelist ) {

			$results = array_intersect_key( $results, array_flip( $whitelist ) );

		}

		if ( $blacklist ) {

			$results = array_diff_key( $results, array_flip( $blacklist ) );

		}

		return $results;

	}

	/**
	 * Returns the attributes as a string for HTML output
	 *
	 * @param	array	$whitelist
	 * @param	array	$blacklist
	 * @return	string
	 **/
	public function getAttributesAsString( array $whitelist = [], array $blacklist = [] ) : string {

		$attributes = $this->getAttributes( $whitelist, $blacklist );

		ksort( $attributes );

		foreach ( $attributes as $k => &$v ) {

			if ( $v === TRUE ) {

				$v = $k;

			}
			else {

				if ( $k == 'class' ) {

					$v = implode( ' ', $v);

				}

				$v = $k . '="' . $this->escapeAttribute( $v ) . '"';

			}

		}

		if ( $attributes ) {

			return ' ' . implode( ' ', $attributes );

		}

		return '';

	}

	/**
	 * Returns one of HTML element's properties
	 *
	 * @param	string	$name
	 * @return	mixed
	 **/
	public function getAttribute( string $name ) {

		$name = $this->cleanAttributeName( $name );

		return $name && isset( $this->_attributes[$name] ) ? $this->_attributes[$name] : NULL;

	}

	/**
	 * Remove one of HTML element's properties
	 *
	 * @param	string	$name
	 * @return	self
	 **/
	public function removeAttribute( string $name ) : self {

		$name = $this->cleanAttributeName( $name );

		if ( $name && isset( $this->_attributes[$name] ) ) {

			unset( $this->_attributes[$name] );

		}

		return $this;

	}

	/**
	 * Add a class to the element's class list
	 *
	 * @param	string	$class
	 * @return	self
	 **/
	public function addClass( string $class ) : self {

		$classes = $this->getAttribute( 'class' );

		if ( !$classes ) {

			$this->setAttribute( 'class', [ $class ] );

		}
		else {

			$classes[] = $class;

			$this->setAttribute( 'class', $classes );

		}

		return $this;

	}

	/**
	 * Remove a class from the element's class list
	 *
	 * @param	string	$class
	 * @return	self
	 **/
	public function removeClass( string $class ) : self {

		$classes = $this->getAttribute( 'class' );

		if ( $classes && ( ( $index = array_search( $class, $classes ) ) ) !== FALSE ) {

			unset( $classes[$index] );

			$this->setAttribute( 'class', $classes );

		}

		return $this;

	}

	/**
	 * Toggles a class on the element
	 *
	 * @param	string	$class
	 * @return	self
	 **/
	public function toggleClass( string $class ) : self {

		if ( $this->hasClass( $class ) ) {

			return $this->removeClass( $class );

		}
		else {

			return $this->addClass( $class );

		}

	}

	/**
	 * Checks if the element has a specific class
	 *
	 * @param	string	$class
	 * @return	boolean
	 **/
	public function hasClass( string $class ) : bool {

		return in_array( $class, $this->getAttribute( 'class' ) );

	}

	/**
	 * Add (append) content
	 *
	 * @param	string	$content
	 * @return	self
	 **/
	public function addContent( string $content ) : self {

		if ( static::EMPTY ) {

			throw new \RuntimeException( "Empty tags cannot have content" );

		}

		$this->_content .= $content;

		return $this;

	}

	/**
	 * Set the content
	 *
	 * @param	string	$content
	 * @return	self
	 **/
	public function setContent( string $content ) : self {

		if ( static::EMPTY ) {

			throw new \RuntimeException( "Empty tags cannot have content" );

		}

		$this->_content = $content;

		return $this;

	}

	/**
	 * Get the content
	 *
	 * @return	string
	 **/
	public function getContent() : string {

		return $this->_content;

	}

	/**
	 * Empties the content of the tag
	 *
	 * @return	self
	 **/
	public function clearContent() : self {

		$this->_content = NULL;

		return $this;

	}

	/**
	 * Prepend a child to the tag
	 *
	 * @param	Tag		$tag
	 * @return	self
	 **/
	public function addChildBefore( Tag $tag ) : self {

		if ( static::EMPTY ) {

			throw new \RuntimeException( "Empty tags cannot have children" );

		}

		$this->_before[] = $tag;

		return $this;

	}

	/**
	 * Append a child to the tag
	 *
	 * @param	Tag		$tag
	 * @return	self
	 **/
	public function addChildAfter( Tag $tag ) : self {

		if ( static::EMPTY ) {

			throw new \RuntimeException( "Empty tags cannot have children" );

		}

		$this->_after[] = $tag;

		return $this;

	}

	/**
	 * Remove a child from the tag
	 *
	 * @param	Tag		$tag
	 * @param	bool	$before
	 * @return	self
	 **/
	public function removeChild( Tag $tag, bool $before = FALSE ) : self {

		if ( static::EMPTY ) {

			return $this;

		}

		if ( !$before && ( ( $index = array_search( $tag, $this->_after ) ) !== FALSE ) ) {

			unset( $this->_after[$index] );

		}
		else if ( $before && ( ( $index = array_search( $tag, $this->_before ) ) !== FALSE ) ) {

			unset( $this->_before[$index] );

		}

		return $this;

	}

	/**
	 * @param	string	$attribute
	 * @return	string
	 **/
	protected function escapeAttribute( string $attribute ) : string {

		return htmlspecialchars( $attribute, ENT_COMPAT );

	}

	/**
	 *
	 *
	 * @param	Tag		$tag
	 * @return	self
	 **/
	public function setWrapper( Tag $tag ) : self {

		$this->_wrapper = $tag;

		return $this;

	}

	/**
	 *
	 *
	 * @return	self
	 **/
	public function removeWrapper() : self {

		$this->_wrapper	 =	NULL;

		return $this;

	}

}
