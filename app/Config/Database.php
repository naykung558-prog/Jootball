<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
	/**
	 * The directory that holds the Migrations
	 * and Seeds directories.
	 *
	 * @var string
	 */
	public $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

	/**
	 * Lets you choose which connection group to
	 * use if no other is specified.
	 *
	 * @var string
	 */
	public $defaultGroup = 'default';

	public $default = array(
		'hostname' => '',
		'username' => '',
		'password' => '',
		'database' => '',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'pconnect' => true,
		'DBDebug' => true,
		'cache_on' => false,
		'cachedir' => '',
		'char_set' => 'UTF8',
		'DBCollat' => 'UTF8',
		'swap_pre' => '',
		'autoinit' => true,
		// 'compress' => false,
		'stricton' => false,
		// 'failover' => array(),
		// 'save_queries' => true,
	);

	// public $default = array(
	// 	'hostname' => 'localhost',
	// 	'username' => 'root',
	// 	'password' => 'root',
	// 	'database' => 'jootball',
	// 	'DBDriver' => 'MySQLi',
	// 	'DBPrefix' => '',
	// 	'pconnect' => false,
	// 	'port' => 8889,
	// 	'DBDebug' => true,
	// 	'cache_on' => false,
	// 	'cachedir' => '',
	// 	'char_set' => 'UTF8',
	// 	'DBCollat' => 'UTF8',
	// 	'swap_pre' => '',
	// 	'autoinit' => true,
	// 	// 'compress' => false,
	// 	'stricton' => false,
	// 	// 'failover' => array(),
	// 	// 'save_queries' => true,
	// );


	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// Ensure that we always set the database group to 'tests' if
		// we are currently running an automated test suite, so that
		// we don't overwrite live data on accident.
		if (ENVIRONMENT === 'testing') {
			$this->defaultGroup = 'tests';
		}
	}

	//--------------------------------------------------------------------

}