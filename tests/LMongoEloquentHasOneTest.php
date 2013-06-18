<?php

use Mockery as m;
use LMongo\Eloquent\Collection;
use LMongo\Eloquent\Relations\HasOne;

class LMongoEloquentHasOneTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testSaveMethodSetsForeignKeyOnModel()
	{
		$relation = $this->getRelation();
		$mockModel = $this->getMock('LMongo\Eloquent\Model', array('save'));
		$mockModel->expects($this->once())->method('save')->will($this->returnValue(true));
		$result = $relation->save($mockModel);

		$attributes = $result->getAttributes();
		$this->assertEquals(new MongoID('511241ccaa69274018000000'), $attributes['foreign_key']);
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
		$model->shouldReceive('setRelation')->once()->with('foo', null);
		$models = $relation->initRelation(array($model), 'foo');

		$this->assertEquals(array($model), $models);
	}


	public function testEagerConstraintsAreProperlyAdded()
	{
		$relation = $this->getRelation();
		$relation->getQuery()->shouldReceive('whereIn')->once()->with('foreign_key', array(new MongoID('511241ccaa69274018000000'), new MongoID('511241ccaa69274018000001')));
		$model1 = new LMongoHasOneModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoHasOneModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$relation->addEagerConstraints(array($model1, $model2));
	}


	public function testModelsAreProperlyMatchedToParents()
	{
		$relation = $this->getRelation();

		$result1 = new LMongoHasOneModelStub;
		$result1->foreign_key = '511241ccaa69274018000000';
		$result2 = new LMongoHasOneModelStub;
		$result2->foreign_key = '511241ccaa69274018000001';

		$model1 = new LMongoHasOneModelStub;
		$model1->_id = '511241ccaa69274018000000';
		$model2 = new LMongoHasOneModelStub;
		$model2->_id = '511241ccaa69274018000001';
		$model3 = new LMongoHasOneModelStub;
		$model3->_id = '511241ccaa69274018000002';

		$models = $relation->match(array($model1, $model2, $model3), new Collection(array($result1, $result2)), 'foo');

		$this->assertEquals('511241ccaa69274018000000', $models[0]->foo->foreign_key);
		$this->assertEquals('511241ccaa69274018000001', $models[1]->foo->foreign_key);
		$this->assertNull($models[2]->foo);
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
		return new HasOne($builder, $parent, 'foreign_key');
	}

}

class LMongoHasOneModelStub extends LMongo\Eloquent\Model {
	public $foreign_key = 'foreign.value';
}