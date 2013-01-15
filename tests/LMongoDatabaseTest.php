<?php

use LMongo\Database;

class LMongoDatabaseTest extends PHPUnit_Framework_TestCase {

	private $conn;

	private $db;

	private $connection;

	function setUp()
	{
		parent::setUp();

		$conn = new Database('localhost', 27017, 'lmongotestdb');
		$conn->connect();
		$this->connection = $conn->getMongoClientObject();
		$this->db = $conn->getMongoDBObject();
	}

	function testInstanceOfMongoDB() 
	{
		$this->assertInstanceOf('MongoDB', $this->db);
	}

	function testInstanceOfMongoClient() 
	{
		$this->assertInstanceOf('MongoClient', $this->connection);
	}

	function testInstanceOfMongoCollection() 
	{
		$this->assertInstanceOf('MongoCollection', $this->db->testcollection);
	}

	function testInstanceOfMongoCursor() 
	{
		$this->assertInstanceOf('MongoCursor', $this->db->testcollection->find());
	}

	function tearDown()
	{    
		parent::tearDown();

		if ($this->db) {
			$this->db->drop();
		}
	} 
}