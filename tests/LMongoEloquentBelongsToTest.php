<?php

use Mockery as m;
use LMongo\Eloquent\Collection;
use LMongo\Eloquent\Relations\BelongsTo;

class LMongoEloquentBelongsToTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testUpdateMethodRetrievesModelAndUpdates()
	{
		$relation = $this->getRelation();
		$mock = m::mock('LMongo\Eloquent\Model');
		$mock->shouldReceive('fill')->once()->with(array('attributes'))->andReturn($mock);
		$mock->shouldReceive('save')->once()->andReturn(true);
		$relation->getQuery()->shouldReceive('first')->once()->andReturn($mock);

		$this->assertTrue($relation->update(array('attributes')));
	}


	public function testEagerConstraintsAreProperlyAdded()
	{
		$relation = $this->getRelation();
		$relation->getQuery()->shouldReceive('whereIn')->once()->with('_id', array(new MongoID('51116e8bd38e182e63000000'), new MongoID('51116e8bd38e182e63000001')));
		$models = array(new LMongoBelongsToModelStub, new LMongoBelongsToModelStub, new AnotherLMongoBelongsToModelStub);
		$relation->addEagerConstraints($models);
	}

	public function testRelationIsProperlyInitialized()
	{
		$relation = $this->getRelation();
		$model = m::mock('LMongo\Eloquent\Model');
		$model->shouldReceive('setRelation')->once()->with('foo', null);
		$models = $relation->initRelation(array($model), 'foo');

		$this->assertEquals(array($model), $models);
	}

	public function testModelsAreProperlyMatchedToParents()
	{
		$relation = $this->getRelation();
		$result1 = m::mock('stdClass');
		$result1->shouldReceive('getKey')->andReturn(1);
		$result2 = m::mock('stdClass');
		$result2->shouldReceive('getKey')->andReturn(2);
		$model1 = new LMongoBelongsToModelStub;
		$model1->foreign_key = 1;
		$model2 = new LMongoBelongsToModelStub;
		$model2->foreign_key = 2;
		$models = $relation->match(array($model1, $model2), new Collection(array($result1, $result2)), 'foo');

		$this->assertEquals(1, $models[0]->foo->getKey());
		$this->assertEquals(2, $models[1]->foo->getKey());
	}


	public function testAssociateMethodSetsForeignKeyOnModel()
	{
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getAttribute')->once()->with('foreign_key')->andReturn('500000000000000000000000');
		$relation = $this->getRelation($parent);
		$associate = m::mock('LMongo\Eloquent\Model');
		$associate->shouldReceive('getKey')->once()->andReturn('500000000000000000000000');
		$parent->shouldReceive('setAttribute')->once()->with('foreign_key', '500000000000000000000000');
		$parent->shouldReceive('setRelation')->once()->with('relation', $associate);

		$relation->associate($associate);
	}


	protected function getRelation($parent = null)
	{
		$builder = m::mock('LMongo\Eloquent\Builder');
		$builder->shouldReceive('where')->with('_id', 'MongoID');
		$related = m::mock('LMongo\Eloquent\Model');
		$related->shouldReceive('getKeyName')->andReturn('_id');
		$builder->shouldReceive('getModel')->andReturn($related);
		$parent = $parent ?: new LMongoBelongsToModelStub;
		return new BelongsTo($builder, $parent, 'foreign_key', 'relation');
	}

}

class LMongoBelongsToModelStub extends LMongo\Eloquent\Model {

	public $foreign_key = '51116e8bd38e182e63000000';

}

class AnotherLMongoBelongsToModelStub extends LMongo\Eloquent\Model {

	public $foreign_key = '51116e8bd38e182e63000001';

}