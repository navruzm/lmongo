<?php

use Mockery as m;
use LMongo\Eloquent\Relations\MorphOne;
use LMongo\Eloquent\Relations\MorphMany;

class LMongoEloquentMorphTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testMorphOneSetsProperConstraints()
	{
		$relation = $this->getOneRelation();
	}


	public function testMorphOneEagerConstraintsAreProperlyAdded()
	{
		$relation = $this->getOneRelation();
		$relation->getQuery()->shouldReceive('whereIn')->once()->with('morph_id', array(new MongoID('511241ccaa69274018000000'), new MongoID('511241ccaa69274018000001')));
		$relation->getQuery()->shouldReceive('where')->once()->with('morph_type', get_class($relation->getParent()));

		$model1 = new LMongoMorphResetModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoMorphResetModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$relation->addEagerConstraints(array($model1, $model2));
	}


	public function testMorphOneWhereClausesCanBeRemoved()
	{
		$builder = new LMongoMorphResetBuilderStub;
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('500000000000000000000000');
		$parent->shouldReceive('isSoftDeleting')->andReturn(false);
		$relation = new MorphOne($builder, $parent, 'morph_type', 'morph_id');
		$relation->where('foo', 'bar');
		$wheres = $relation->getAndResetWheres();

		$this->assertEquals('Basic', $wheres[0]['type']);
		$this->assertEquals('foo', $wheres[0]['column']);
		$this->assertEquals('bar', $wheres[0]['value']);
	}


	/**
	 * Note that the tests are the exact same for morph many because the classes share this code...
	 * Will still test to be safe.
	 */
	public function testMorphManySetsProperConstraints()
	{
		$relation = $this->getManyRelation();
	}


	public function testMorphManyEagerConstraintsAreProperlyAdded()
	{
		$relation = $this->getManyRelation();
		$relation->getQuery()->shouldReceive('whereIn')->once()->with('morph_id', array(new MongoID('511241ccaa69274018000000'), new MongoID('511241ccaa69274018000001')));
		$relation->getQuery()->shouldReceive('where')->once()->with('morph_type', get_class($relation->getParent()));

		$model1 = new LMongoMorphResetModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoMorphResetModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$relation->addEagerConstraints(array($model1, $model2));
	}


	public function testMorphManyWhereClausesCanBeRemoved()
	{
		$builder = new LMongoMorphResetBuilderStub;
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('500000000000000000000000');
		$relation = new MorphMany($builder, $parent, 'morph_type', 'morph_id');
		$relation->where('foo', 'bar');
		$wheres = $relation->getAndResetWheres();

		$this->assertEquals('Basic', $wheres[0]['type']);
		$this->assertEquals('foo', $wheres[0]['column']);
		$this->assertEquals('bar', $wheres[0]['value']);
	}


	public function testCreateFunctionOnMorph()
	{
		// Doesn't matter which relation type we use since they share the code...
		$relation = $this->getOneRelation();
		$created = m::mock('stdClass');
		$relation->getRelated()->shouldReceive('newInstance')->once()->with(array('name' => 'taylor', 'morph_id' => new MongoID('511241ccaa69274018000000'), 'morph_type' => get_class($relation->getParent())))->andReturn($created);
		$created->shouldReceive('save')->once()->andReturn(true);

		$this->assertEquals($created, $relation->create(array('name' => 'taylor')));
	}


	protected function getOneRelation()
	{
		$builder = m::mock('LMongo\Eloquent\Builder');
		$builder->shouldReceive('where')->once()->with('morph_id', 'MongoID');
		$related = m::mock('LMongo\Eloquent\Model');
		$builder->shouldReceive('getModel')->andReturn($related);
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('511241ccaa69274018000000');
		$builder->shouldReceive('where')->once()->with('morph_type', get_class($parent));
		return new MorphOne($builder, $parent, 'morph_type', 'morph_id');
	}


	protected function getManyRelation()
	{
		$builder = m::mock('LMongo\Eloquent\Builder');
		$builder->shouldReceive('where')->once()->with('morph_id', 'MongoID');
		$related = m::mock('LMongo\Eloquent\Model');
		$builder->shouldReceive('getModel')->andReturn($related);
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('500000000000000000000000');
		$builder->shouldReceive('where')->once()->with('morph_type', get_class($parent));
		return new MorphMany($builder, $parent, 'morph_type', 'morph_id');
	}

}


class LMongoMorphResetModelStub extends LMongo\Eloquent\Model {}


class LMongoMorphResetBuilderStub extends LMongo\Eloquent\Builder {
	public function __construct() { $this->query = new LMongoMorphQueryStub; }
	public function getModel() { return new LMongoMorphResetModelStub; }
	public function isSoftDeleting() { return false; }
}


class LMongoMorphQueryStub extends LMongo\Query\Builder {
	public function __construct() {}
}
