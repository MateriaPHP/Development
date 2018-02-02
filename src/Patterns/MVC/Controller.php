<?php

namespace Materia\Development\Patterns\MVC;

/**
 * Simple implementation of the MVC pattern
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

use \Materia\Network\Request as Request;
use \Materia\Network\Response as Response;

abstract class Controller {

	/**
	 * GET request handler
	 *
	 * @param	Request		$request	HTTP request
	 * @return	Response
	 **/
	public function get( Request $request ) : Response {

		throw new \DomainException( 'Unsupported request method: GET' );

	}

	/**
	 * POST request handler
	 *
	 * @param	Request		$request	HTTP request
	 * @return	Response
	 **/
	public function post( Request $request ) : Response {

		throw new \DomainException( 'Unsupported request method: POST' );

	}

	/**
	 * HEAD request handler
	 *
	 * @param	Request		$request	HTTP request
	 * @return	Response
	 **/
	public function head( Request $request ) : Response {

		throw new \DomainException( 'Unsupported request method: HEAD' );

	}

	/**
	 * PUT request handler
	 *
	 * @param	Request		$request	HTTP request
	 * @return	Response
	 **/
	public function put( Request $request ) : Response {

		throw new \DomainException( 'Unsupported request method: PUT' );

	}

	/**
	 * DELETE request handler
	 *
	 * @param	Request		$request	HTTP request
	 * @return	Response
	 **/
	public function delete( Request $request ) : Response {

		throw new \DomainException( 'Unsupported request method: DELETE' );

	}

	/**
	 * PATCH request handler
	 *
	 * @param	Request		$request	HTTP request
	 * @return	Response
	 **/
	public function patch( Request $request ) : Response {

		throw new \DomainException( 'Unsupported request method: PATCH' );

	}

}
