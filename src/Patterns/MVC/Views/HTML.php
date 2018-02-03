<?php

namespace Materia\Development\Patterns\MVC\Views;

/**
 * HTML view class
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

abstract class HTML implements \Materia\Development\Patterns\MVC\View {

	protected $_template;

	/**
	 * Constructor
	 *
	 * @param	HTML\Template	$template
	 **/
	public function __construct( HTML\Template $template ) {

		$this->_template = $template;

	}

	/**
	 * Append a flash message
	 *
	 * @param	string	$message
	 * @return	self
	 **/
	public function addFlashMessage( string $message ) : self {

		$_SESSION['_flash_messages'][] = $message;

		return $this;

	}

}
