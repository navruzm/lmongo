<?php namespace LMongo;

use Illuminate\Support\ServiceProvider;
use LMongo\Eloquent\Model as Model;

class LMongoServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		Model::setConnectionResolver($this->app['lmongo']);

		Model::setEventDispatcher($this->app['events']);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register the package configuration.
		$this->app['config']->package('navruzm/lmongo', __DIR__.'/../config');

		$this->app['lmongo'] = $this->app->share(function($app)
		{
			return new DatabaseManager($app);
		});
	}
}