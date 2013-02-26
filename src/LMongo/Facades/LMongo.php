<?php namespace LMongo\Facades;

use Illuminate\Support\Facades\Facade;

class LMongo extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'lmongo';
	}

}