<?php

use Mockery as m;
use LMongo\Eloquent\Relations\HasOne;

class LMongoEloquentRelationTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testWhereClausesCanBeRemoved()
	{
		// For this test it doesn't matter what type of relationship we have, so we'll just use HasOne
		$builder = new LMongoRelationResetStub;
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('500000000000000000000000');
		$relation = new HasOne($builder, $parent, 'foreign_key');
		$relation->where('foo', 'bar');
		$wheres = $relation->getAndResetWheres();
		$this->assertEquals('Basic', $wheres[0]['type']);
		$this->assertEquals('foo', $wheres[0]['column']);
		$this->assertEquals('bar', $wheres[0]['value']);
	}

	public function testTouchMethodUpdatesRelatedTimestamps()
	{
		$builder = m::mock('LMongo\Eloquent\Builder');
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('500000000000000000000000');
		$builder->shouldReceive('getModel')->andReturn($related = m::mock('StdClass'));
		$builder->shouldReceive('where');
		$relation = new HasOne($builder, $parent, 'foreign_key');
		$related->shouldReceive('getCollection')->andReturn('collection');
		$related->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');
		$related->shouldReceive('freshTimestamp')->andReturn(1);
		$builder->shouldReceive('update')->once()->with(array('updated_at' => 1));

		$relation->touch();
	}

}

class LMongoRelationResetModelStub extends LMongo\Eloquent\Model {}

class LMongoRelationResetStub extends LMongo\Eloquent\Builder {
	public function __construct() { $this->query = new LMongoRelationQueryStub; }
	public function getModel() { return new LMongoRelationResetModelStub; }
}


class LMongoRelationQueryStub extends LMongo\Query\Builder {
	public function __construct() {}
}