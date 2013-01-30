<?php

use LMongo\Database;

class LMongoDatabaseTest extends PHPUnit_Framework_TestCase {

	private $conn;

	private $db;

	private $connection;

	public function setUp()
	{
		$this->conn = new Database('localhost', 27017, 'lmongotestdb');
		$this->conn->connect();
		$this->connection = $this->conn->getMongoClientObject();
		$this->db = $this->conn->getMongoDBObject();
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

	public function tearDown()
	{
		if ($this->db)
		{
			$this->db->drop();
		}
	}
}