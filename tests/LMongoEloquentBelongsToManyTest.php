<?php

use Mockery as m;
use LMongo\Eloquent\Collection;
use LMongo\Eloquent\Relations\BelongsToMany;

class LMongoEloquentBelongsToManyTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testCreateMethodProperlyCreatesNewModel()
	{
		$relation = $this->getRelation();
		$created = $this->getMock('LMongo\Eloquent\Model', array('save', 'getKey'));
		$created->expects($this->any())->method('save')->will($this->returnValue(true));
		$relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($created);
		$relation->getParent()->shouldReceive('getAttribute')->once()->andReturn('500000000000000000000000');
		$relation->getParent()->shouldReceive('setAttribute')->once()->andReturn('500000000000000000000000');
		$relation->getParent()->shouldReceive('save')->once()->andReturn('500000000000000000000000');

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
		$model1 = new LMongoBelongsToManyModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoBelongsToManyModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$relation->addEagerConstraints(array($model1, $model2));
	}


	public function testModelsAreProperlyMatchedToParents()
	{
		$relation = $this->getRelation();

		$result1 = new LMongoBelongsToManyModelStub;
		$result1->foreign_key = array('511241ccaa69274018000000');
		$result2 = new LMongoBelongsToManyModelStub;
		$result2->foreign_key = array('511241ccaa69274018000001','511241ccaa69274018000000');
		$result3 = new LMongoBelongsToManyModelStub;
		$result3->foreign_key = array('511241ccaa69274018000001');

		$model1 = new LMongoBelongsToManyModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoBelongsToManyModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$model3 = new LMongoBelongsToManyModelStub;
		$model3->_id = '511241ccaa69274018000002';

		$relation->getRelated()->shouldReceive('newCollection')->andReturnUsing(function($array) { return new Collection($array); });
		$models = $relation->match(array($model1, $model2, $model3), new Collection(array($result1, $result2, $result3)), 'foo');
		$this->assertEquals('511241ccaa69274018000000', $models[0]->foo[0]->foreign_key[0]);
		$this->assertEquals(2, count($models[0]->foo));
		$this->assertEquals('511241ccaa69274018000001', $models[1]->foo[0]->foreign_key[0]);
		$this->assertEquals('511241ccaa69274018000000', $models[1]->foo[0]->foreign_key[1]);
		$this->assertEquals('511241ccaa69274018000001', $models[1]->foo[1]->foreign_key[0]);
		$this->assertEquals(2, count($models[1]->foo));
		$this->assertEquals(0, count($models[2]->foo));
	}

	public function testDetachRemovesRelationWithModel()
	{
		$relation = $this->getRelation();
		$relation->getRelated()->shouldReceive('getKey')->once()->andReturn('511241ccaa69274018000001');
		$relation->getRelated()->shouldReceive('getAttribute')->andReturn(array('511241ccaa69274018000000'));
		$relation->getRelated()->shouldReceive('setAttribute')->with('foreign_key', array())->andReturn(1);
		$relation->getRelated()->shouldReceive('save')->once()->andReturn(1);
		$relation->getParent()->shouldReceive('getAttribute')->andReturn(array('511241ccaa69274018000001'));
		$relation->getParent()->shouldReceive('setAttribute')->with('other_key', array())->andReturn(1);
		$relation->getParent()->shouldReceive('save')->once()->andReturn(1);
		$relation->detach($relation->getRelated());
	}

	public function testDetachRemovesRelationWithID()
	{
		$relation = $this->getRelation();
		$relation->getRelated()->shouldReceive('where')->once()->andReturn($relation->getRelated());
		$relation->getRelated()->shouldReceive('first')->once()->andReturn($relation->getRelated());
		$relation->getRelated()->shouldReceive('getKey')->once()->andReturn('511241ccaa69274018000001');
		$relation->getRelated()->shouldReceive('getAttribute')->andReturn(array('511241ccaa69274018000000'));
		$relation->getRelated()->shouldReceive('setAttribute')->with('foreign_key', array())->andReturn(1);
		$relation->getRelated()->shouldReceive('save')->once()->andReturn(1);
		$relation->getParent()->shouldReceive('getAttribute')->andReturn(array('511241ccaa69274018000001'));
		$relation->getParent()->shouldReceive('setAttribute')->with('other_key', array())->andReturn(1);
		$relation->getParent()->shouldReceive('save')->once()->andReturn(1);
		$relation->detach('511241ccaa69274018000001');
	}

	public function testAttachCreateRelationWithModel()
	{
		$relation = $this->getRelation();
		$relation->getRelated()->shouldReceive('getKey')->andReturn('511241ccaa69274018000001');
		$relation->getRelated()->shouldReceive('getAttribute')->andReturn(array());
		$relation->getRelated()->shouldReceive('setAttribute')->with('foreign_key', array(new MongoID('511241ccaa69274018000000')))->andReturn(1);
		$relation->getRelated()->shouldReceive('save')->once()->andReturn(1);
		$relation->getParent()->shouldReceive('getAttribute')->andReturn(array());
		$relation->getParent()->shouldReceive('setAttribute')->with('other_key', array(new MongoID('511241ccaa69274018000001')))->andReturn(1);
		$relation->getParent()->shouldReceive('save')->once()->andReturn(1);
		$relation->attach($relation->getRelated());
	}

	public function testAttachCreateRelationWithID()
	{
		$relation = $this->getRelation();
		$relation->getRelated()->shouldReceive('where')->once()->andReturn($relation->getRelated());
		$relation->getRelated()->shouldReceive('first')->once()->andReturn($relation->getRelated());
		$relation->getRelated()->shouldReceive('getKey')->andReturn('511241ccaa69274018000001');
		$relation->getRelated()->shouldReceive('getAttribute')->andReturn(array());
		$relation->getRelated()->shouldReceive('setAttribute')->with('foreign_key', array(new MongoID('511241ccaa69274018000000')))->andReturn(1);
		$relation->getRelated()->shouldReceive('save')->once()->andReturn(1);
		$relation->getParent()->shouldReceive('getAttribute')->andReturn(array());
		$relation->getParent()->shouldReceive('setAttribute')->with('other_key', array(new MongoID('511241ccaa69274018000001')))->andReturn(1);
		$relation->getParent()->shouldReceive('save')->once()->andReturn(1);
		$relation->attach('511241ccaa69274018000001');
	}

	public function testSyncMethodSyncsRelationWithGivenArray()
	{
		$relation = $this->getMock('LMongo\Eloquent\Relations\BelongsToMany', array('attach', 'detach'), $this->getRelationArguments());
		$relation->getParent()->shouldReceive('getAttribute')->andReturn(array(1,2,3));
		$relation->expects($this->once())->method('attach')->with($this->equalTo(4));
		$relation->expects($this->once())->method('detach')->with($this->equalTo(1));

		$relation->sync(array(2, 3, 4));
	}

	public function getRelation()
	{
		list($builder, $parent) = $this->getRelationArguments();

		return new BelongsToMany($builder, $parent, 'foreign_key', 'other_key');
	}

	public function getRelationArguments()
	{
		$builder = m::mock('LMongo\Eloquent\Builder');
		$builder->shouldReceive('where')->with('foreign_key', 'MongoID');
		$related = m::mock('LMongo\Eloquent\Model');
		$builder->shouldReceive('getModel')->andReturn($related);
		$parent = m::mock('LMongo\Eloquent\Model');
		$parent->shouldReceive('getKey')->andReturn('511241ccaa69274018000000');
		$parent->shouldReceive('getCreatedAtColumn')->andReturn('created_at');
		$parent->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');

		return array($builder, $parent, 'foreign_key', 'other_key');
	}

}

class LMongoBelongsToManyModelStub extends LMongo\Eloquent\Model {}