<?php namespace LMongo\Eloquent\Relations;

use MongoDate;
use LMongo\Eloquent\Model;
use LMongo\Eloquent\Builder;
use LMongo\Eloquent\Collection;

abstract class Relation {

	/**
	 * The Eloquent query builder instance.
	 *
	 * @var \LMongo\Eloquent\Builder
	 */
	protected $query;

	/**
	 * The parent model instance.
	 *
	 * @var \LMongo\Eloquent\Model
	 */
	protected $parent;

	/**
	 * The related model instance.
	 *
	 * @var \LMongo\Eloquent\Model
	 */
	protected $related;

	/**
	 * Create a new relation instance.
	 *
	 * @param  \LMongo\Eloquent\Builder
	 * @param  \LMongo\Eloquent\Model
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent)
	{
		$this->query = $query;
		$this->parent = $parent;
		$this->related = $query->getModel();

		$this->addConstraints();
	}

	/**
	 * Set the base constraints on the relation query.
	 *
	 * @return void
	 */
	abstract public function addConstraints();

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	abstract public function addEagerConstraints(array $models);

	/**
	 * Initialize the relation on a set of models.
	 *
	 * @param  array   $models
	 * @param  string  $relation
	 * @return void
	 */
	abstract public function initRelation(array $models, $relation);

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  array   $models
	 * @param  \LMongo\Eloquent\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	abstract public function match(array $models, Collection $results, $relation);

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	abstract public function getResults();

	/**
	 * Touch all of the related models for the relationship.
	 *
	 * @return void
	 */
	public function touch()
	{
		$column = $this->getRelated()->getUpdatedAtColumn();

		$this->rawUpdate(array($column => $this->getRelated()->freshTimestamp()));
	}

	/**
	 * Restore all of the soft deleted related models.
	 *
	 * @return int
	 */
	public function restore()
	{
		return $this->query->withTrashed()->restore();
	}

	/**
	 * Run a raw update against the base query.
	 *
	 * @param  array  $attributes
	 * @return int
	 */
	public function rawUpdate(array $attributes = array())
	{
		return $this->query->update($attributes);
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
		// When a model is "soft deleting", the "deleted at" where clause will be the
		// first where clause on the relationship query, so we will actually clear
		// the second where clause as that is the lazy loading relations clause.
		if ($this->query->getModel()->isSoftDeleting())
		{
			$this->removeSecondWhereClause();
		}

		// When the model isn't soft deleting the where clause added by the lazy load
		// relation query will be the first where clause on this query, so we will
		// remove that to make room for the eager load constraints on the query.
		else
		{
			$this->removeFirstWhereClause();
		}

		return $this->getBaseQuery()->getAndResetWheres();
	}

	/**
	 * Remove the first where clause from the relationship query.
	 *
	 * @return void
	 */
	protected function removeFirstWhereClause()
	{
		array_shift($this->getBaseQuery()->wheres);
	}

	/**
	 * Remove the second where clause from the relationship query.
	 *
	 * @return void
	 */
	protected function removeSecondWhereClause()
	{
		$wheres =& $this->getBaseQuery()->wheres;

		// We'll grab the second where clause off of the set of wheres, and then reset
		// the where clause keys so there are no gaps in the numeric keys. Then we
		// remove the binding from the query so it doesn't mess things when run.
		$second = $wheres[1]; unset($wheres[1]);

		$wheres = array_values($wheres);
	}

	/**
	 * Get all of the primary keys for an array of models.
	 *
	 * @param  array  $models
	 * @return array
	 */
	protected function getKeys(array $models)
	{
		return array_values(array_map(function($value)
		{
			return $value->getKey();

		}, $models));
	}

	/**
	 * Get the underlying query for the relation.
	 *
	 * @return \LMongo\Eloquent\Builder
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * Get the base query builder driving the Eloquent builder.
	 *
	 * @return \LMongo\Query\Builder
	 */
	public function getBaseQuery()
	{
		return $this->query->getQuery();
	}

	/**
	 * Get the parent model of the relation.
	 *
	 * @return \LMongo\Eloquent\Model
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Get the related model of the relation.
	 *
	 * @return \LMongo\Eloquent\Model
	 */
	public function getRelated()
	{
		return $this->related;
	}

	/**
	 * Get the name of the "created at" column.
	 *
	 * @return string
	 */
	public function createdAt()
	{
		return $this->parent->getCreatedAtColumn();
	}

	/**
	 * Get the name of the "updated at" column.
	 *
	 * @return string
	 */
	public function updatedAt()
	{
		return $this->parent->getUpdatedAtColumn();
	}

	/**
	 * Get the name of the related model's "updated at" column.
	 *
	 * @return string
	 */
	public function relatedUpdatedAt()
	{
		return $this->related->getUpdatedAtColumn();
	}

	/**
	 * Handle dynamic method calls to the relationship.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$result = call_user_func_array(array($this->query, $method), $parameters);

		if ($result === $this->query) return $this;

		return $result;
	}

}