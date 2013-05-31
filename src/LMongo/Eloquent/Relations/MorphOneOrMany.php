<?php namespace LMongo\Eloquent\Relations;

use MongoID;
use LMongo\Eloquent\Model;
use LMongo\Eloquent\Builder;

abstract class MorphOneOrMany extends HasOneOrMany {

	/**
	 * The foreign key type for the relationship.
	 *
	 * @var string
	 */
	protected $morphType;

	/**
	 * The class name of the parent model.
	 *
	 * @var string
	 */
	protected $morphClass;

	/**
	 * Create a new has many relationship instance.
	 *
	 * @param  \LMongo\Eloquent\Builder  $query
	 * @param  \LMongo\Eloquent\Model  $parent
	 * @param  string  $type
	 * @param  string  $id
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $type, $id)
	{
		$this->morphType = $type;

		$this->morphClass = get_class($parent);

		parent::__construct($query, $parent, $id);
	}

	/**
	 * Set the base constraints on the relation query.
	 *
	 * @return void
	 */
	public function addConstraints()
	{
		parent::addConstraints();

		$this->query->where($this->morphType, $this->morphClass);
	}

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	public function addEagerConstraints(array $models)
	{
		parent::addEagerConstraints($models);

		$this->query->where($this->morphType, $this->morphClass);
	}

	/**
	 * Remove the original where clause set by the relationship.
	 *
	 * The remaining constraints on the query will be reset and returned.
	 *
	 * @return array
	 */
	public function getAndResetWheres()
	{
		// We actually need to remove two where clauses from polymorphic queries so we
		// will make an extra call to clear the second where clause here so that it
		// will not get in the way. This parent method will remove the other one.
		$this->removeSecondWhereClause();

		return parent::getAndResetWheres();
	}

	/**
	 * Attach a model instance to the parent model.
	 *
	 * @param  \LMongo\Eloquent\Model  $model
	 * @return \LMongo\Eloquent\Model
	 */
	public function save(Model $model)
	{
		$model->setAttribute($this->morphType, $this->morphClass);

		return parent::save($model);
	}

	/**
	 * Create a new instance of the related model.
	 *
	 * @param  array  $attributes
	 * @return \LMongo\Eloquent\Model
	 */
	public function create(array $attributes)
	{
		$foreign = array($this->foreignKey => new MongoID($this->parent->getKey()));

		// When saving a polymorphic relationship, we need to set not only the foreign
		// key, but also the foreign key type, which is typically the class name of
		// the parent model. This makes the polymorphic item unique in the table.
		$foreign[$this->morphType] = $this->morphClass;

		$attributes = array_merge($attributes, $foreign);

		$instance = $this->related->newInstance($attributes);

		$instance->save();

		return $instance;
	}

	/**
	 * Get the foreign key "type" name.
	 *
	 * @return string
	 */
	public function getMorphType()
	{
		return $this->morphType;
	}

	/**
	 * Get the class name of the parent model.
	 *
	 * @return string
	 */
	public function getMorphClass()
	{
		return $this->morphClass;
	}

}