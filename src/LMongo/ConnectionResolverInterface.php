<?php namespace LMongo;

interface ConnectionResolverInterface {

	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return LMongo\Connection
	 */
	public function connection($name = null);

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection();
}