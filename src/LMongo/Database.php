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
     * The MongoDB connection handler.
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

        $conn = new \MongoClient($this->host . ':' . $this->port);
        $this->connection = $conn->{$this->database};
    }

    
    public function __get($name)
    {
        return new \MongoCollection($this->connection, $name);
    }
}
