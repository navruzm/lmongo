<?php namespace LMongo;

use Closure;
use DateTime;
use Illuminate\Cache\CacheManager;

class Connection {

	/**
	 * The event dispatcher instance.
	 *
	 * @var Illuminate\Events\Dispatcher
	 */
	protected $events;

	/**
	 * The paginator environment instance.
	 *
	 * @var Illuminate\Pagination\Paginator
	 */
	protected $paginator;

	/**
	 * The cache manager instance.
	 *
	 * @var \Illuminate\Cache\CacheManger
	 */
	protected $cache;

	/**
     * The MongoDB database handler.
     *
     * @var resource
     */
    protected $db;

    /**
     * The MongoClient connection handler.
     *
     * @var resource
     */
    protected $connection;

	/**
	 * The database connection configuration options.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Establish a database connection.
	 *
	 * @param  array  $config
	 * @return LMongo\Connection
	 */
	public function connect(array $config)
	{
		if ( ! is_null($this->connection)) return;

		$this->config = $config;

		$options = array_get($this->config, 'options', array());

      	$this->connection = new \MongoClient($this->getDsn($config), $options);

      	//Select database
        $this->db = $this->connection->{$config['database']};

        return $this;
	}

	/**
	 * Create a DSN string from a configuration.
	 *
	 * @param  array   $config
	 * @return string
	 */
	protected function getDsn(array $config)
	{
		// First we will create the basic DSN setup as well as the port if it is in
		// in the configuration options. This will give us the basic DSN we will
		// need to establish the MongoClient and return them back for use.
		extract($config);

		$dsn = "mongodb://";

		if (isset($config['username']) and isset($config['password']))
		{
			$dsn .= "{$username}:{$password}@";
		}

		$dsn.= "{$host}";

		if (isset($config['port']))
		{
			$dsn .= ":{$port}";
		}

		$dsn.= "/{$database}";

		return $dsn;
	}

	/**
	 * Get the event dispatcher used by the connection.
	 *
	 * @return Illuminate\Events\Dispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->events;
	}

	/**
	 * Set the event dispatcher instance on the connection.
	 *
	 * @param  Illuminate\Events\Dispatcher
	 * @return void
	 */
	public function setEventDispatcher(\Illuminate\Events\Dispatcher $events)
	{
		$this->events = $events;
	}

	/**
	 * Get the paginator environment instance.
	 *
	 * @return Illuminate\Pagination\Environment
	 */
	public function getPaginator()
	{
		if ($this->paginator instanceof Closure)
		{
			$this->paginator = call_user_func($this->paginator);
		}

		return $this->paginator;
	}

	/**
	 * Set the pagination environment instance.
	 *
	 * @param  Illuminate\Pagination\Environment|\Closure  $paginator
	 * @return void
	 */
	public function setPaginator($paginator)
	{
		$this->paginator = $paginator;
	}

	/**
	 * Get the cache manager instance.
	 *
	 * @return \Illuminate\Cache\CacheManager
	 */
	public function getCacheManager()
	{
		if ($this->cache instanceof Closure)
		{
			$this->cache = call_user_func($this->cache);
		}

		return $this->cache;
	}

	/**
	 * Set the cache manager instance on the connection.
	 *
	 * @param  \Illuminate\Cache\CacheManager|\Closure  $cache
	 * @return void
	 */
	public function setCacheManager($cache)
	{
		$this->cache = $cache;
	}

	/**
	 * Get the database connection name.
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->getConfig('name');
	}

	/**
	 * Get an option from the configuration options.
	 *
	 * @param  string  $option
	 * @return mixed
	 */
	public function getConfig($option)
	{
		return array_get($this->config, $option);
	}

	/**
     * return MongoDB object
     *
     * @return \MongoDB
     */
    public function getMongoDB()
    {
        return $this->db;
    }

    /**
     * return MongoClient object
     *
     * @return \MongoClient
     */
    public function getMongoClient()
    {
        return $this->connection;
    }

    /**
     * Return new Query Builder instance
     *
     * @param  string $collection
     * @return \LMongo\Query\Builder
     */
    public function collection($collection)
    {
        $builder = new Query\Builder($this);

        return $builder->collection($collection);
    }

    /**
     * Dynamically pass collection name to MongoCollection and return it.
     *
     * @param  string  $name
     * @return \MongoCollection
     */
    public function __get($name)
    {
        return new \MongoCollection($this->getMongoDB(), $name);
    }
}