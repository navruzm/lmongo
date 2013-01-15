<?php

use LMongo\Database;

class LMongoDatabaseTest extends PHPUnit_Framework_TestCase {

	private $conn;

	private $db;

	private $connection;

	function setUp()
	{
		parent::setUp();

		$this->conn = new Database('localhost', 27017, 'lmongotestdb');
		$this->conn->connect();
		$this->connection = $this->conn->getMongoClientObject();
		$this->db = $this->conn->getMongoDBObject();
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

	function testMagicDatabaseObjectMethod() 
	{
		$this->assertInstanceOf('MongoCollection', $this->conn->testcollection);
	}

	function tearDown()
	{    
		parent::tearDown();

		if ($this->db) 
		{
			$this->db->drop();
		}
	} 
}