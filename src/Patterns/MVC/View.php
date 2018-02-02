<?php

namespace Materia\Development\Patterns\MVC;

/**
 * Simple implementation of the MVC pattern
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

use \Materia\Network\Response as Response;

interface View {

	/**
	 * Build the response
	 *
	 * @param	Response	$response
	 * @return	Response
	 **/

	public function getResponse( Response $response = NULL ) : Response;

}
