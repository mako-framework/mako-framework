<?php

namespace mako\core;

use \mako\http\Request;
use \mako\request\RequestException;
use \mako\core\errorhandler\ErrorHandler;
use \mako\core\errorhandler\handlers\RequestExceptionHandler;

/**
 * Mako.
 *
 * @author     Frederic G. Østby
 * @copyright  (c) 2008-2013 Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

class App
{
	//---------------------------------------------
	// Class properties
	//---------------------------------------------
	
	// Nothing here
	
	//---------------------------------------------
	// Class constructor, destructor etc ...
	//---------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access  public
	 */
	
	public function __construct()
	{
		// Nothing here
	}

	/**
	 * Factory method making method chaining possible right off the bat.
	 * 
	 * @access  public
	 */

	public static function factory()
	{
		return new static();
	}
	
	//---------------------------------------------
	// Class methods
	//---------------------------------------------
	
	/**
	 * Executes request and sends response.
	 *
	 * @access  public
	 * @param   string  $route  (optional) Route passed to the request handler.
	 */
	
	public function run($route = null)
	{
		// Start output buffering

		ob_start();
				
		// Removes slashes added to the superglobals by magic quotes

		if(MAKO_MAGIC_QUOTES === 1)
		{
			$superglobals = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

			foreach($superglobals as &$superglobal)
			{
				array_walk_recursive($superglobal, function(&$value, $key)
				{
					$value = stripslashes($value);
				});
			}

			unset($superglobals);
		}

		// Register the RequestException handler

		ErrorHandler::handle('\mako\http\RequestException', function($exception)
		{
			$handler = new RequestExceptionHandler($exception);

			$handler->handle();

			return true; // Return true to stop further handling of the RequestException
		});

		// Check if the application is offline

		if(file_exists(MAKO_APPLICATION_PATH . '/storage/offline'))
		{
			throw new RequestException(503);
		}

		// Include routes

		include MAKO_APPLICATION_PATH . '/routes.php';

		// Execute the request

		Request::factory($route)->execute()->send();	
	}
}

/** -------------------- End of file -------------------- **/