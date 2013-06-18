<?php

use Mockery as m;
use LMongo\Eloquent\Collection;
use LMongo\Eloquent\Relations\HasMany;

class LMongoEloquentHasManyTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testCreateMethodProperlyCreatesNewModel()
	{
		$relation = $this->getRelation();
		$created = $this->getMock('LMongo\Eloquent\Model', array('save', 'getKey', 'setRawAttributes'));
		$created->expects($this->once())->method('save')->will($this->returnValue(true));
		$relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($created);
		$created->expects($this->once())->method('setRawAttributes')->with($this->equalTo(array('name' => 'taylor', 'foreign_key' => new MongoID('511241ccaa69274018000000'))));

		$this->assertEquals($created, $relation->create(array('name' => 'taylor')));
	}

	public function testUpdateMethodUpdatesModelsWithTimestamps()
	{
		$relation = $this->getRelation();
		$relation->getRelated()->shouldReceive('usesTimestamps')->once()->andReturn(true);
		$relation->getRelated()->shouldReceive('freshTimestamp')->once()->andReturn(100);
		$relation->getRelated()->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');
		$relation->getQuery()->shouldReceive('update')->once()->with(array('foo' => 'bar', 'updated_at' => 100))->andReturn('results');

		$this->assertEquals('results', $relation->update(array('foo' => 'bar')));
	}


	public function testRelationIsProperlyInitialized()
	{
		$relation = $this->getRelation();
		$model = m::mock('LMongo\Eloquent\Model');
		$relation->getRelated()->shouldReceive('newCollection')->andReturnUsing(function($array = array()) { return new Collection($array); });
		$model->shouldReceive('setRelation')->once()->with('foo', m::type('LMongo\Eloquent\Collection'));
		$models = $relation->initRelation(array($model), 'foo');

		$this->assertEquals(array($model), $models);
	}


	public function testEagerConstraintsAreProperlyAdded()
	{
		$relation = $this->getRelation();
		$relation->getQuery()->shouldReceive('whereIn')->once()->with('foreign_key', array(new MongoID('511241ccaa69274018000000'), new MongoID('511241ccaa69274018000001')));
		$model1 = new LMongoHasManyModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoHasManyModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$relation->addEagerConstraints(array($model1, $model2));
	}


	public function testModelsAreProperlyMatchedToParents()
	{
		$relation = $this->getRelation();

		$result1 = new LMongoHasManyModelStub;
		$result1->foreign_key = '511241ccaa69274018000000';
		$result2 = new LMongoHasManyModelStub;
		$result2->foreign_key = '511241ccaa69274018000001';
		$result3 = new LMongoHasManyModelStub;
		$result3->foreign_key = '511241ccaa69274018000001';

		$model1 = new LMongoHasManyModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoHasManyModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$model3 = new LMongoHasManyModelStub;
		$model3->_id = '511241ccaa69274018000002';

		$relation->getRelated()->shouldReceive('newCollection')->andReturnUsing(function($array) { return new Collection($array); });
		$models = $relation->match(array($model1, $model2, $model3), new Collection(array($result1, $result2, $result3)), 'foo');
		$this->assertEquals('511241ccaa69274018000000', $models[0]->foo[0]->foreign_key);
		$this->assertEquals(1, count($models[0]->foo));
		$this->assertEquals('511241ccaa69274018000001', $models[1]->foo[0]->foreign_key);
		$this->assertEquals('511241ccaa69274018000001', $models[1]->foo[1]->foreign_key);
		$this->assertEquals(2, count($models[1]->foo));
		$this->assertEquals(0, count($models[2]->foo));
	}


	protected function getRelation()
	{
		$builder = m::mock('LMongo\Eloquent\Builder');
		$builder->shouldReceive('where')->with('foreign_key', 'MongoID');
		$related = m::mock('LMongo\Eloquent\Model');
		$builder->shouldReceive('getModel')->andReturn($related);
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('511241ccaa69274018000000');
		$parent->shouldReceive('getCreatedAtColumn')->andReturn('created_at');
		$parent->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');
		return new HasMany($builder, $parent, 'foreign_key');
	}

}

class LMongoHasManyModelStub extends LMongo\Eloquent\Model {
	public $foreign_key = 'foreign.value';
}