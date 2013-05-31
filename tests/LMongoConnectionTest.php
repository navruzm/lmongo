<?php

use LMongo\Connection;
use Mockery as m;

class LMongoConnectionTest extends PHPUnit_Framework_TestCase {

	private $conn;

	private $db;

	private $connection;

	public function setUp()
	{
		$this->conn = new Connection;
		$this->conn->connect(array('host' => 'localhost', 'port' => 27017, 'database' => 'lmongotestdb'));
		$this->connection = $this->conn->getMongoClient();
		$this->db = $this->conn->getMongoDB();
	}

	public function testInstanceOfMongoDB()
	{
		$this->assertInstanceOf('MongoDB', $this->db);
	}

	public function testInstanceOfMongoClient()
	{
		$this->assertInstanceOf('MongoClient', $this->connection);
	}

	public function testInstanceOfMongoCollection()
	{
		$this->assertInstanceOf('MongoCollection', $this->db->testcollection);
	}

	public function testInstanceOfMongoCursor()
	{
		$this->assertInstanceOf('MongoCursor', $this->db->testcollection->find());
	}

	public function testMagicDatabaseObjectMethod()
	{
		$this->assertInstanceOf('MongoCollection', $this->conn->testcollection);
	}

	public function testCollectionCreatesNewQueryBuilder()
	{
		$builder = $this->conn->collection('users');
		$this->assertInstanceOf('LMongo\Query\Builder', $builder);
		$this->assertEquals('users', $builder->collection);
	}

	public function testResolvingPaginatorThroughClosure()
	{
		$connection = $this->conn;
		$paginator  = m::mock('Illuminate\Pagination\Environment');
		$connection->setPaginator(function() use ($paginator)
		{
			return $paginator;
		});
		$this->assertEquals($paginator, $connection->getPaginator());
	}

	public function testResolvingCacheThroughClosure()
	{
		$connection = $this->conn;
		$cache  = m::mock('Illuminate\Cache\CacheManager');
		$connection->setCacheManager(function() use ($cache)
		{
			return $cache;
		});
		$this->assertEquals($cache, $connection->getCacheManager());
	}

	public function tearDown()
	{
		if ($this->db)
		{
			$this->db->drop();
		}
	}
}
