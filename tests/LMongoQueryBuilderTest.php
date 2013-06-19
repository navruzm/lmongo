<?php

use Mockery as m;
use LMongo\Connection;
use LMongo\Query\Builder;
use LMongo\Query\Cursor;

class LMongoQueryBuilderTest extends PHPUnit_Framework_TestCase {

	private $conn;

	private $db;

	private $connection;

	public function setUp()
	{
		$this->conn = new Connection;
		$this->conn->connect(array('host' => 'localhost', 'port' => 27017, 'database' => 'lmongotestdb'));
		$this->db = $this->conn->getMongoDB();
	}

	public function tearDown()
	{
		if($this->db)
		{
			$this->db->drop();
		}
		m::close();
	}

	public function testBasicWheres()
	{
		$builder = $this->getBuilder();
		$builder->where('id', 1);
		$this->assertEquals(array('$and' => array(array('id' => 1))), $builder->compileWheres($builder));
	}

	public function testBasicAndWheres()
	{
		$builder = $this->getBuilder();
		$builder->where('id', 1)->andWhere('name', 'John');
		$this->assertEquals(array('$and' => array(array('id' => 1), array('name' => 'John'))), $builder->compileWheres($builder));
	}

	public function testBasicOrWheres()
	{
		$builder = $this->getBuilder();
		$builder->where('id', 1)->orWhere('name', 'John');
		$this->assertEquals(array('$or' => array(array('id' => 1), array('name' => 'John'))), $builder->compileWheres($builder));
	}

	public function testBasicNorWheres()
	{
		$builder = $this->getBuilder();
		$builder->where('id', 1)->norWhere('name', 'John');
		$this->assertEquals(array('$nor' => array(array('id' => 1), array('name' => 'John'))), $builder->compileWheres($builder));
	}

	public function testWhereAlls()
	{
		$builder = $this->getBuilder();
		$builder->whereAll('id', array(1, 2));
		$this->assertEquals(array('$and' => array(array('id' => array('$all' => array(1, 2))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereAll('id', array(3, 4));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$all' => array(3, 4))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereAll('id', array(3, 4));
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$all' => array(3, 4))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereAll('id', array(3, 4));
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$all' => array(3, 4))))), $builder->compileWheres($builder));
	}

	public function testWhereLts()
	{
		$builder = $this->getBuilder();
		$builder->whereLt('id', 10);
		$this->assertEquals(array('$and' => array(array('id' => array('$lt' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereLt('id', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$lt' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereLt('id', 10);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$lt' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereLt('id', 10);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$lt' => 10)))), $builder->compileWheres($builder));
	}

	public function testWhereLtes()
	{
		$builder = $this->getBuilder();
		$builder->whereLte('id', 10);
		$this->assertEquals(array('$and' => array(array('id' => array('$lte' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereLte('id', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$lte' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereLte('id', 10);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$lte' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereLte('id', 10);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$lte' => 10)))), $builder->compileWheres($builder));
	}

	public function testWhereGts()
	{
		$builder = $this->getBuilder();
		$builder->whereGt('id', 10);
		$this->assertEquals(array('$and' => array(array('id' => array('$gt' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereGt('id', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$gt' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereGt('id', 10);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$gt' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereGt('id', 10);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$gt' => 10)))), $builder->compileWheres($builder));
	}

	public function testWhereGtes()
	{
		$builder = $this->getBuilder();
		$builder->whereGte('id', 10);
		$this->assertEquals(array('$and' => array(array('id' => array('$gte' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereGte('id', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$gte' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereGte('id', 10);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$gte' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereGte('id', 10);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$gte' => 10)))), $builder->compileWheres($builder));
	}

	public function testWhereBetweens()
	{
		$builder = $this->getBuilder();
		$builder->whereBetween('id', 1, 5);
		$this->assertEquals(array('$and' => array(array('id' => array('$gt' => 1, '$lt' => 5)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereBetween('id', 1, 5);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$gt' => 1, '$lt' => 5)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereBetween('id', 1, 5);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$gt' => 1, '$lt' => 5)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereBetween('id', 1, 5);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$gt' => 1, '$lt' => 5)))), $builder->compileWheres($builder));
	}

	public function testWhereIns()
	{
		$builder = $this->getBuilder();
		$builder->whereIn('id', array(1, 2));
		$this->assertEquals(array('$and' => array(array('id' => array('$in' => array(1, 2))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereIn('id', array(3, 4));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$in' => array(3, 4))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereIn('id', array(3, 4));
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$in' => array(3, 4))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereIn('id', array(3, 4));
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$in' => array(3, 4))))), $builder->compileWheres($builder));
	}

	public function testWhereNins()
	{
		$builder = $this->getBuilder();
		$builder->whereNin('id', array(1, 2));
		$this->assertEquals(array('$and' => array(array('id' => array('$nin' => array(1, 2))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereNin('id', array(3, 4));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$nin' => array(3, 4))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereNin('id', array(3, 4));
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$nin' => array(3, 4))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereNin('id', array(3, 4));
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$nin' => array(3, 4))))), $builder->compileWheres($builder));
	}

	public function testWhereNes()
	{
		$builder = $this->getBuilder();
		$builder->whereNe('id', 10);
		$this->assertEquals(array('$and' => array(array('id' => array('$ne' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereNe('id', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$ne' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereNe('id', 10);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$ne' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereNe('id', 10);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$ne' => 10)))), $builder->compileWheres($builder));
	}

	public function testWhereExists()
	{
		$builder = $this->getBuilder();
		$builder->whereExists('updated_at');
		$this->assertEquals(array('$and' => array(array('updated_at' => array('$exists' => true)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereExists('updated_at');
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('updated_at' => array('$exists' => true)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereExists('updated_at');
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('updated_at' => array('$exists' => true)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereExists('updated_at');
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('updated_at' => array('$exists' => true)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereExists('updated_at', false);
		$this->assertEquals(array('$and' => array(array('updated_at' => array('$exists' => false)))), $builder->compileWheres($builder));
	}

	public function testWhereTypes()
	{
		$builder = $this->getBuilder();
		$builder->whereType('id', 10);
		$this->assertEquals(array('$and' => array(array('id' => array('$type' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereType('id', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('id' => array('$type' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereType('id', 10);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('id' => array('$type' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereType('id', 10);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('id' => array('$type' => 10)))), $builder->compileWheres($builder));
	}

	public function testWhereMods()
	{
		$builder = $this->getBuilder();
		$builder->whereMod('column', 5, 3);
		$this->assertEquals(array('$and' => array(array('column' => array('$mod' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereMod('column', 5, 3);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('column' => array('$mod' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereMod('column', 5, 3);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('column' => array('$mod' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereMod('column', 5, 3);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('column' => array('$mod' => array(5, 3))))), $builder->compileWheres($builder));
	}

	public function testWhereRegexs()
	{
		$builder = $this->getBuilder();
		$builder->whereRegex('name', '/John/i');
		$this->assertEquals(array('$and' => array(array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->andWhereRegex('name', '/John/i');
		$this->assertEquals(array('$and' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->orWhereRegex('name', '/John/i');
		$this->assertEquals(array('$or' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->norWhereRegex('name', '/John/i');
		$this->assertEquals(array('$nor' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereRegex('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$and' => array(array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->andWhereRegex('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$and' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->orWhereRegex('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$or' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->norWhereRegex('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$nor' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));
	}

	public function testWhereLikes()
	{
		$builder = $this->getBuilder();
		$builder->whereLike('name', 'John');
		$this->assertEquals(array('$and' => array(array('name' => new MongoRegex('/John/im')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->andWhereLike('name', 'John');
		$this->assertEquals(array('$and' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/im')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->orWhereLike('name', 'John');
		$this->assertEquals(array('$or' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/im')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->norWhereLike('name', 'John');
		$this->assertEquals(array('$nor' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/im')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereLike('name', 'John','i');
		$this->assertEquals(array('$and' => array(array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->andWhereLike('name', 'John','i');
		$this->assertEquals(array('$and' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->orWhereLike('name', 'John','i');
		$this->assertEquals(array('$or' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->norWhereLike('name', 'John','i');
		$this->assertEquals(array('$nor' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereLike('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$and' => array(array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->andWhereLike('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$and' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->orWhereLike('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$or' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('column', 'value')->norWhereLike('name', new MongoRegex('/John/i'));
		$this->assertEquals(array('$nor' => array(array('column' => 'value'), array('name' => new MongoRegex('/John/i')))), $builder->compileWheres($builder));
	}

	public function testWhereNears()
	{
		$builder = $this->getBuilder();
		$builder->whereNear('location', array(5, 3));
		$this->assertEquals(array('$and' => array(array('location' => array('$near' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereNear('location', array(5, 3), null, 10);
		$this->assertEquals(array('$and' => array(array('location' => array('$near' => array(5, 3), '$maxDistance' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereNear('location', array(5, 3), 'Polygon');
		$this->assertEquals(array('$and' => array(array('location' => array('$near' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(5, 3))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereNear('location', array(5, 3), 'Polygon', 10);
		$this->assertEquals(array('$and' => array(array('location' => array('$near' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(5, 3), '$maxDistance' => 10)))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereNear('location', array(5, 3));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('location' => array('$near' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereNear('location', array(5, 3));
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('location' => array('$near' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereNear('location', array(5, 3));
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('location' => array('$near' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andwhereNear('location', array(5, 3), 'Polygon', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'),array('location' => array('$near' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(5, 3), '$maxDistance' => 10)))))), $builder->compileWheres($builder));
	}

	public function testWhereNearSpheres()
	{
		$builder = $this->getBuilder();
		$builder->whereNearSphere('location', array(5, 3));
		$this->assertEquals(array('$and' => array(array('location' => array('$nearSphere' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereNearSphere('location', array(5, 3), null, 10);
		$this->assertEquals(array('$and' => array(array('location' => array('$nearSphere' => array(5, 3), '$maxDistance' => 10)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereNearSphere('location', array(5, 3), 'Polygon');
		$this->assertEquals(array('$and' => array(array('location' => array('$nearSphere' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(5, 3))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereNearSphere('location', array(5, 3), 'Polygon', 10);
		$this->assertEquals(array('$and' => array(array('location' => array('$nearSphere' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(5, 3), '$maxDistance' => 10)))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereNearSphere('location', array(5, 3));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('location' => array('$nearSphere' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereNearSphere('location', array(5, 3));
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('location' => array('$nearSphere' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereNearSphere('location', array(5, 3));
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('location' => array('$nearSphere' => array(5, 3))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andwhereNearSphere('location', array(5, 3), 'Polygon', 10);
		$this->assertEquals(array('$and' => array(array('name' => 'John'),array('location' => array('$nearSphere' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(5, 3), '$maxDistance' => 10)))))), $builder->compileWheres($builder));
	}

	public function testWhereGeoWithins()
	{
		$builder = $this->getBuilder();
		$builder->whereGeoWithin('location', '$box', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$and' => array(array('location' => array('$geoWithin' => array('$box' => array(array('100','120'), array('100','0'))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereGeoWithin('location', '$box', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('location' => array('$geoWithin' => array('$box' => array(array('100','120'), array('100','0'))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereGeoWithin('location', '$box', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('location' => array('$geoWithin' => array('$box' => array(array('100','120'), array('100','0'))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereGeoWithin('location', '$box', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('location' => array('$geoWithin' => array('$box' => array(array('100','120'), array('100','0'))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->whereGeoWithin('location', 'Polygon', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$and' => array(array('location' => array('$geoWithin' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(array('100','120'), array('100','0')))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereGeoWithin('location', 'Polygon', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('location' => array('$geoWithin' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(array('100','120'), array('100','0')))))))), $builder->compileWheres($builder));
	}

	public function testWhereGeoIntersects()
	{
		$builder = $this->getBuilder();
		$builder->whereGeoIntersects('location', 'Polygon', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$and' => array(array('location' => array('$geoIntersects' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(array('100','120'), array('100','0')))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereGeoIntersects('location', 'Polygon', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('location' => array('$geoIntersects' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(array('100','120'), array('100','0')))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereGeoIntersects('location', 'Polygon', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('location' => array('$geoIntersects' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(array('100','120'), array('100','0')))))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereGeoIntersects('location', 'Polygon', array(array('100','120'), array('100','0')));
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('location' => array('$geoIntersects' => array('$geometry' => array('type' => 'Polygon', 'coordinates' => array(array('100','120'), array('100','0')))))))), $builder->compileWheres($builder));
	}

	public function testWhereSizes()
	{
		$builder = $this->getBuilder();
		$builder->whereSize('tags', 3);
		$this->assertEquals(array('$and' => array(array('tags' => array('$size' => 3)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->andWhereSize('tags', 3);
		$this->assertEquals(array('$and' => array(array('name' => 'John'), array('tags' => array('$size' => 3)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->orWhereSize('tags', 3);
		$this->assertEquals(array('$or' => array(array('name' => 'John'), array('tags' => array('$size' => 3)))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('name', 'John')->norWhereSize('tags', 3);
		$this->assertEquals(array('$nor' => array(array('name' => 'John'), array('tags' => array('$size' => 3)))), $builder->compileWheres($builder));
	}

	public function testOrderBys()
	{
		$builder = $this->getBuilder();
		$builder->orderBy('email')->orderBy('age', 'desc')->orderBy('name', 1);
		$this->assertEquals(array('email' => 1, 'age' => -1, 'name' => 1), $builder->orders);
	}

	public function testLimitsAndOffsets()
	{
		$builder = $this->getBuilder();
		$builder->take(5);
		$this->assertEquals(5, $builder->limit);

		$builder = $this->getBuilder();
		$builder->skip(5);
		$this->assertEquals(5, $builder->offset);
	}

	public function testNestedWheres()
	{
		$builder = $this->getBuilder();
		$builder->where('email', 'foo@bar.com')
	            ->andWhere(function($query)
	            {
	                $query->where('age', 27)
	                      ->andWhere('name', 'John');
	            });
		$this->assertEquals(array('$and' => array(array('email' => 'foo@bar.com'), array('$and' => array(array('age' => 27), array('name' => 'John'))))), $builder->compileWheres($builder));

		$builder = $this->getBuilder();
		$builder->where('email', 'foo@bar.com')
	            ->orWhere(function($query)
	            {
	                $query->where('age', 27)
	                      ->norWhere('name', 'John');
	            });
		$this->assertEquals(array('$or' => array(array('email' => 'foo@bar.com'), array('$nor' => array(array('age' => 27), array('name' => 'John'))))), $builder->compileWheres($builder));
	}

	public function testInsertMethods()
	{
		$data = array(
			'name' => 'Mustafa',
			'no' => 1,
		);
		$builder = $this->getBuilder();
		$id = $builder->collection('test')->insert($data);
		$this->assertInstanceOf('MongoID', $id);

		$data = array(
			array(
				'name' => 'Fatih',
				'no' => 2,
			),
			array(
				'name' => 'Osman',
				'no' => 3,
			),
			array(
				'name' => 'Ali',
				'no' => 4,
		));
		$builder = $this->getBuilder();
		$ids = $builder->collection('test')->batchInsert($data);
		$this->assertInstanceOf('MongoID', current($ids));
	}

	public function testFirstMethodReturnsFirstResult()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->first(array('name','no'));
		$this->assertEquals(array('name' => 'Mustafa', 'no' => 1), $result);
	}

	public function testPluckMethodReturnsSingleColumn()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->pluck('no');
		$this->assertEquals(1, $result);
	}

	public function testAggregateFunctions()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->count();
		$this->assertEquals(4, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->sum('no');
		$this->assertEquals(10, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->min('no');
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->max('no');
		$this->assertEquals(4, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->avg('no');
		$this->assertEquals(2.5, $result);
	}

	public function testUpdateMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->update(array('no' => 4));
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 4)->update(array('no' => 5));
		$this->assertEquals(2, $result);
	}

	public function testSetFieldMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->setField('no', 4);
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 4)->setField(array('no' => 5));
		$this->assertEquals(2, $result);
	}

	public function testUnsetFieldMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->unsetField('no');
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->whereExists('no')->unsetField(array('no','name'));
		$this->assertEquals(3, $result);
	}

	public function testRenameFieldMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->renameField('no', 'id');
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->whereExists('id')->renameField(array('id' => 'no'));
		$this->assertEquals(1, $result);
	}

	public function testIncrementAndDecrementMethods()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->increment('no');
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->pluck('no');
		$this->assertEquals(2, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->increment('no', 2);
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->pluck('no');
		$this->assertEquals(4, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->decrement('no');
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->pluck('no');
		$this->assertEquals(3, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->decrement('no', 2);
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->pluck('no');
		$this->assertEquals(1, $result);
	}

	public function testPushMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 1)->push('tag', 'css');
		$this->assertEquals(1, $result);

		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array('php','html','css'), $result);
	}

	public function testPushAllMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 1)->pushAll('tag', array('css','mongodb'));
		$this->assertEquals(1, $result);

		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array('php','html','css','mongodb'), $result);
	}

	public function testaddToSetMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 1)->addToSet('tag', 'css');
		$this->assertEquals(1, $result);

		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array('php','html','css'), $result);

		$builder->collection('test')->where('no', 1)->addToSet('tag', 'php');
		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array('php','html','css'), $result);
	}

	public function testPullMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 1)->pull('tag', 'php');
		$this->assertEquals(1, $result);

		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array('html'), $result);
	}

	public function testPullAllMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 1)->pullAll('tag', array('php','html'));
		$this->assertEquals(1, $result);

		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array(), $result);
	}

	public function testPopMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('no', 1)->pop('tag');
		$this->assertEquals(1, $result);

		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array('php'), $result);

		$builder->collection('test')->where('no', 1)->pushAll('tag', array('css','mongodb'));
		$builder->collection('test')->where('no', 1)->pop('tag', -1);
		$result = $builder->collection('test')->where('no', 1)->pluck('tag');
		$this->assertEquals(array('css','mongodb'), $result);
	}


	public function testDeleteMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->delete();
		$this->assertEquals(1, $result);

		$builder = $this->getBuilder();
		$result = $builder->collection('test')->delete();
		$this->assertEquals(3, $result);
	}

	public function testTruncateMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->truncate();
		$this->assertEquals(true, $result);
	}

	public function testGroupMethod()
	{
		$this->insertData();
		$builder = $this->getBuilder();
		$initial = array('count' => 0);
		$reduce = 'function (obj, prev) { prev.count++; }';
		$result = $builder->collection('test')->whereGt('no', 2)->group($initial, $reduce);
		$this->assertEquals(2, $result[0]['count']);
	}

	public function testImplode()
	{
		$this->insertData();

		// Test without glue.
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->orWhere('name', 'Fatih')->implode('name');
		$this->assertEquals('MustafaFatih', $result);

		// Test with glue.
		$builder = $this->getBuilder();
		$result = $builder->collection('test')->where('name', 'Mustafa')->orWhere('name', 'Fatih')->implode('name', ',');
		$this->assertEquals('Mustafa,Fatih', $result);
	}

	public function testPaginateCorrectlyCreatesPaginatorInstance()
	{
		$connection = m::mock('LMongo\Connection');
		$builder = $this->getMock('LMongo\Query\Builder', array('getPaginationCount', 'forPage', 'get'), array($connection));
		$paginator = m::mock('Illuminate\Pagination\Environment');
		$paginator->shouldReceive('getCurrentPage')->once()->andReturn(1);
		$connection->shouldReceive('getPaginator')->once()->andReturn($paginator);
		$cursor = m::mock('LMongo\Query\Cursor');
		$cursor->shouldReceive('countAll')->once()->andReturn(10);
		$cursor->shouldReceive('toArray')->once()->andReturn(array('foo'));
		$builder->expects($this->once())->method('forPage')->with($this->equalTo(1), $this->equalTo(15))->will($this->returnValue($builder));
		$builder->expects($this->once())->method('get')->with($this->equalTo(array()))->will($this->returnValue($cursor));
		$paginator->shouldReceive('make')->once()->with(array('foo'), 10, 15)->andReturn(array('results'));

		$this->assertEquals(array('results'), $builder->paginate(15, array()));
	}

	public function testCaching()
	{
		$connection = m::mock('LMongo\Connection');
		$connection->shouldReceive('getName')->andReturn('connection_name');
		$connection->shouldReceive('getCacheManager')->once()->andReturn($cache = m::mock('StdClass'));

		$builder = $this->getMock('LMongo\Query\Builder', array('getFresh'), array($connection));
		$builder->expects($this->once())->method('getFresh')->with($this->equalTo(array()))->will($this->returnValue(array('results')));
		$query = $builder->collection('users')->where('email', 'foo@bar.com')->remember(5);

		$cache->shouldReceive('remember')
	                     ->once()
	                     ->with($query->getCacheKey(), 5, m::type('Closure'))
	                     ->andReturnUsing(function($key, $minutes, $callback) { return $callback(); });

		$this->assertEquals($query->get(), array('results'));
	}

	protected function insertData()
	{
		$data = array(
			array(
				'name' => 'Mustafa',
				'no' => 1,
				'tag' => array('php', 'html')
			),
			array(
				'name' => 'Fatih',
				'no' => 2,
			),
			array(
				'name' => 'Osman',
				'no' => 3,
			),
			array(
				'name' => 'Ali',
				'no' => 4,
		));

		$this->getBuilder()->collection('test')->batchInsert($data);
	}

	protected function getBuilder()
	{
		return new Builder($this->conn);
	}
}