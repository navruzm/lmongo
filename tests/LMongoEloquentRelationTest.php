<?php

use Mockery as m;
use LMongo\Eloquent\Relations\HasOne;

class LMongoModelRelationTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testWhereClausesCanBeRemoved()
	{
		// For this test it doesn't matter what type of relationship we have, so we'll just use HasOne
		$builder = new LMongoRelationResetStub;
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn(1);
		$relation = new HasOne($builder, $parent, 'foreign_key');
		$relation->where('foo', 'bar');
		$wheres = $relation->getAndResetWheres();
		$this->assertEquals('Basic', $wheres[0]['type']);
		$this->assertEquals('foo', $wheres[0]['column']);
		$this->assertEquals('bar', $wheres[0]['value']);
	}

}


class LMongoRelationResetStub extends LMongo\Eloquent\Builder {
	public function __construct() { $this->query = new LMongoRelationQueryStub; }
	public function getModel() {}
}


class LMongoRelationQueryStub extends LMongo\Query\Builder {
	public function __construct() {}
}