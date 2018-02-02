<?php

namespace Materia\Development\Patterns\Observer;

/**
 * Abstract Subject class, implements the Observer Design Pattern
 *
 * @package	Materia.Development
 * @author	Filippo Bovo
 * @link	https://lab.alchemica.io/projects/materia/
 **/

abstract class Subject implements \SplSubject {

	protected $_observers = [];

	/**
	 * Add observer
	 *
	 * @param	Observer	$observer
	 * @return	self
	 **/
	public function attach( Observer $observer ) : self {

		$this->_observers[] = $observer;

		return $this;

	}

	/**
	 * Remove observer
	 *
	 * @param	Observer	$observer
	 * @return	self
	 **/
	public function detach( Observer $observer ) : self {

		if ( FALSE !== ( $key = array_search( $observer, $this->_observers, TRUE ) ) ) {

			unset( $this->_observers[$key] );

		}

		return $this;

	}

	/**
	 * Notify observers
	 **/
	public function notify() {

		foreach ( $this->_observers as $observer ) {

			$observer->update( $this );

		}

	}

}
