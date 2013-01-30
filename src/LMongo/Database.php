<?php namespace LMongo;

class Database
{

    /**
     * The host address of the database.
     *
     * @var string
     */
    protected $host;

    /**
     * The port of the database.
     *
     * @var int
     */
    protected $port;

    /**
     * The database name to be selected.
     *
     * @var string
     */
    protected $database;

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
     * The paginator environment instance.
     *
     * @var Illuminate\Pagination\Paginator
     */
    public $paginator;

    /**
     * Create a new MongoDB connection instance.
     *
     * @param  string  $host
     * @param  int     $port
     * @param  string     $database
     * @return void
     */
    public function __construct($host, $port, $database = 'mongo')
    {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
    }

    /**
     * Connect to the MongoDB database.
     *
     * @return void
     */
    public function connect()
    {
        if ( ! is_null($this->connection)) return;

        $this->connection = new \MongoClient($this->host . ':' . $this->port);
        $this->db = $this->connection->{$this->database};
    }

    /**
     * return MongoDB object
     *
     * @return \MongoDB
     */
    public function getMongoDBObject()
    {
        return $this->db;
    }

    /**
     * return MongoClient object
     *
     * @return \MongoClient
     */
    public function getMongoClientObject()
    {
        return $this->connection;
    }

    /**
     * Get the paginator environment instance.
     *
     * @return Illuminate\Pagination\Environment
     */
    public function getPaginator()
    {
        if (is_callable($this->paginator))
        {
            $this->paginator = call_user_func($this->paginator);
        }
        return $this->paginator;
    }

    /**
     * Set the pagination environment instance.
     *
     * @param  Illuminate\Pagination\Environment|Closure  $paginator
     * @return void
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Dynamically pass collection name to MongoCollection and return it.
     *
     * @param  string  $name
     * @return \MongoCollection
     */
    public function __get($name)
    {
        return new \MongoCollection($this->db, $name);
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

        return $builder->setCollection($collection);
    }
}
