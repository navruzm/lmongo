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
     * Dynamically pass collection name to MongoCollection and return it.
     *
     * @param  string  $name
     * @return \MongoCollection
     */
    public function __get($name)
    {
        return new \MongoCollection($this->db, $name);
    }
}
