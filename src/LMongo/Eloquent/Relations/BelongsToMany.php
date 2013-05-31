<?php namespace LMongo\Eloquent\Relations;

use MongoID;
use LMongo\Eloquent\Model;
use LMongo\Eloquent\Builder;
use LMongo\Eloquent\Collection;

class BelongsToMany extends HasOneOrMany {

	/**
	 * The foreign key of the parent model.
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * The associated key of the relation.
	 *
	 * @var string
	 */
	protected $otherKey;

	/**
	 * Create a new has many relationship instance.
	 *
	 * @param  \LMongo\Eloquent\Builder  $query
	 * @param  \LMongo\Eloquent\Model  $parent
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $foreignKey, $otherKey)
	{
		$this->otherKey = $otherKey;
		$this->foreignKey = $foreignKey;

		parent::__construct($query, $parent, $foreignKey);
	}

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	public function getResults()
	{
		return $this->query->get();
	}

	/**
	 * Initialize the relation on a set of models.
	 *
	 * @param  array   $models
	 * @param  string  $relation
	 * @return void
	 */
	public function initRelation(array $models, $relation)
	{
		foreach ($models as $model)
		{
			$model->setRelation($relation, $this->related->newCollection());
		}

		return $models;
	}

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  array   $models
	 * @param  \LMongo\Eloquent\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	public function match(array $models, Collection $results, $relation)
	{
		return $this->matchMany($models, $results, $relation);
	}

	/**
	 * Save a new model and attach it to the parent model.
	 *
	 * @param  \LMongo\Eloquent\Model  $model
	 * @return \LMongo\Eloquent\Model
	 */
	public function save(Model $model)
	{
		$model->save();

		$this->attach($model);

		return $model;
	}

	/**
	 * Save an array of new models and attach them to the parent model.
	 *
	 * @param  array  $models
	 * @return array
	 */
	public function saveMany(array $models)
	{
		foreach ($models as $key => $model)
		{
			$this->save($model);
		}

		return $models;
	}

	/**
	 * Create a new instance of the related model.
	 *
	 * @param  array  $attributes
	 * @param  array  $joining
	 * @return \LMongo\Eloquent\Model
	 */
	public function create(array $attributes)
	{
		$instance = $this->related->newInstance($attributes);

		// Once we save the related model, we need to attach it to the base model via
		// through intermediate table so we'll use the existing "attach" method to
		// accomplish this which will insert the record and any more attributes.
		$instance->save();

		$this->attach($instance);

		return $instance;
	}

	/**
	 * Create an array of new instances of the related models.
	 *
	 * @param  array  $attributes
	 * @return \LMongo\Eloquent\Model
	 */
	public function createMany(array $records)
	{
		$instances = array();

		foreach ($records as $key => $record)
		{
			$instances[] = $this->create($record);
		}

		return $instance;
	}

	/**
	 * Sync the intermediate tables with a list of IDs.
	 *
	 * @param  array  $ids
	 * @return void
	 */
	public function sync(array $ids)
	{
		$ids = array_map(function($value)
		{
			return (string) $value;
		}, $ids);

		// First we need to attach any of the associated models that are not currently
		// in this joining table. We'll spin through the given IDs, checking to see
		// if they exist in the array of current ones, and if not we will insert.
		$current = (array) $this->parent->getAttribute($this->otherKey);

		$current = array_map(function($value)
		{
			return (string) $value;
		}, $current);

		foreach ($ids as $id)
		{
			if ( ! in_array($id, $current))
			{
				$this->attach($id);
			}
		}

		// Next, we will take the differences of the currents and given IDs and detach
		// all of the entities that exist in the "current" array but are not in the
		// the array of the IDs given to the method which will complete the sync.
		$detach_ids = array_diff($current, $ids);

		foreach ($detach_ids as $detach)
		{
			$this->detach($detach);
		}
	}

	/**
	 * Attach a model to the parent.
	 *
	 * @param  mixed  $related
	 * @return void
	 */
	public function attach($related)
	{
		if ($related instanceof Model === false) $related = $this->related->where('_id', new MongoID($related))->first();

		$ids = array();

		foreach ((array) $related->getAttribute($this->foreignKey) as $value)
		{
			$ids[(string) $value] = new MongoID((string) $value);
		}

		$parent_id = $this->parent->getKey();

		$ids[$parent_id] = new MongoID($parent_id);

		$related->setAttribute($this->foreignKey, array_values($ids));

		$related->save();


		$ids = array();

		foreach ((array) $this->parent->getAttribute($this->otherKey) as $value)
		{
			$ids[(string) $value] = new MongoID((string) $value);
		}

		$ids[$related->getKey()] = new MongoID($related->getKey());

		$this->parent->setAttribute($this->otherKey, array_values($ids));

		$this->parent->save();
	}

	/**
	 * Detach models from the relationship.
	 *
	 * @param  int  $related
	 * @return void
	 */
	public function detach($related)
	{
		if ($related instanceof Model === false) $related = $this->related->where('_id', new MongoID($related))->first();

		$ids = array();

		$parent_id = $this->parent->getKey();

		foreach ((array) $related->getAttribute($this->foreignKey) as $value)
		{
			if((string) $value == $parent_id) continue;

			$ids[(string) $value] = new MongoID((string) $value);
		}

		$related->setAttribute($this->foreignKey, array_values($ids));

		$related->save();


		$ids = array();

		foreach ((array) $this->parent->getAttribute($this->otherKey) as $value)
		{
			if((string) $value == $related->getKey()) continue;

			$ids[(string) $value] = new MongoID((string) $value);
		}

		$this->parent->setAttribute($this->otherKey, array_values($ids));

		$this->parent->save();
	}

	/**
	 * Build model dictionary keyed by the relation's foreign key.
	 *
	 * @param  \LMongo\Eloquent\Collection  $results
	 * @return array
	 */
	protected function buildDictionary(Collection $results)
	{
		$dictionary = array();

		// First we will create a dictionary of models keyed by the foreign key of the
		// relationship as this will allow us to quickly access all of the related
		// models without having to do nested looping which will be quite slow.
		foreach ($results as $result)
		{
			foreach ($result->{$this->foreignKey} as $key)
			{
				$dictionary[(string)$key][] = $result;
			}
		}

		return $dictionary;
	}

	/**
	 * Get the other key for the relationship.
	 *
	 * @return string
	 */
	public function getOtherKey()
	{
		return $this->otherKey;
	}
}