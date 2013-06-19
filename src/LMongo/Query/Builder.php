<?php namespace LMongo\Query;

use Closure;
use MongoRegex;
use MongoCode;
use MongoID;

class Builder {

	/**
	 * The database connection instance.
	 *
	 * @var LMongo\Connection
	 */
	protected $connection;

	/**
	 * The database collection
	 *
	 * @var string
	 */
	public $collection;

	/**
	 * The where constraints for the query.
	 *
	 * @var array
	 */
	public $wheres = array();

	/**
	 * The columns that should be returned.
	 *
	 * @var array
	 */
	public $columns;

	/**
	 * The orderings for the query.
	 *
	 * @var array
	 */
	public $orders;

	/**
	 * The maximum number of documents to return.
	 *
	 * @var int
	 */
	public $limit;

	/**
	 * The number of documents to skip.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * Indicates if the first where set or not.
	 *
	 * @var bool
	 */
	protected $first;

	/**
	 * The key that should be used when caching the query.
	 *
	 * @var string
	 */
	protected $cacheKey;

	/**
	 * The number of minutes to cache the query.
	 *
	 * @var int
	 */
	protected $cacheMinutes;

	/**
	 * Create a new query builder instance.
	 *
	 * @param  \LMongo\Connection $connection
	 * @return void
	 */
	public function __construct(\LMongo\Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Add a basic where clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @param  string  $logic
	 * @return LMongo\Query\Builder
	 */
	public function where($column, $value = null, $logic = 'first')
	{
		if ($column instanceof Closure)
		{
			return $this->whereNested($column, $logic);
		}

		$type = 'Basic';

		if('first' == $logic)
		{
			if(is_null($this->first))
			{
				$this->first = true;
			}
			else
			{
				$logic = '$and';
			}
		}

		$this->wheres[] = compact('type', 'column', 'value', 'logic');

		return $this;
	}

	/**
	 * Add an "$and logical operation" clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhere($column, $value = null)
	{
		return $this->where($column, $value, '$and');
	}

	/**
	 * Add an "$or logical operation" clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhere($column, $value = null)
	{
		return $this->where($column, $value, '$or');
	}

	/**
	 * Add an "$nor logical operation" clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhere($column, $value = null)
	{
		return $this->where($column, $value, '$nor');
	}

	/**
	 * Add an "$all comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function whereAll($column, array $value)
	{
		return $this->where($column, array('$all' => array_values($value)), 'first');
	}

	/**
	 * Add an "$all comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereAll($column, array $value)
	{
		return $this->where($column, array('$all' => array_values($value)), '$and');
	}

	/**
	 * Add an "$all comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereAll($column, array $value)
	{
		return $this->where($column, array('$all' => array_values($value)), '$or');
	}

	/**
	 * Add an "$all comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereAll($column, array $value)
	{
		return $this->where($column, array('$all' => array_values($value)), '$nor');
	}

	/**
	 * Add an "$lt comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function whereLt($column, $value)
	{
		return $this->where($column, array('$lt' => $value), 'first');
	}

	/**
	 * Add an "$lt comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereLt($column, $value)
	{
		return $this->where($column, array('$lt' => $value), '$and');
	}

	/**
	 * Add an "$lt comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereLt($column, $value)
	{
		return $this->where($column, array('$lt' => $value), '$or');
	}

	/**
	 * Add an "$lt comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereLt($column, $value)
	{
		return $this->where($column, array('$lt' => $value), '$nor');
	}

	/**
	 * Add an "$lte comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function whereLte($column, $value)
	{
		return $this->where($column, array('$lte' => $value), 'first');
	}

	/**
	 * Add an "$lte comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereLte($column, $value)
	{
		return $this->where($column, array('$lte' => $value), '$and');
	}

	/**
	 * Add an "$lte comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereLte($column, $value)
	{
		return $this->where($column, array('$lte' => $value), '$or');
	}

	/**
	 * Add an "$lte comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereLte($column, $value)
	{
		return $this->where($column, array('$lte' => $value), '$nor');
	}

	/**
	 * Add an "$gt comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function whereGt($column, $value)
	{
		return $this->where($column, array('$gt' => $value), 'first');
	}

	/**
	 * Add an "$gt comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereGt($column, $value)
	{
		return $this->where($column, array('$gt' => $value), '$and');
	}

	/**
	 * Add an "$gt comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereGt($column, $value)
	{
		return $this->where($column, array('$gt' => $value), '$or');
	}

	/**
	 * Add an "$gt comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereGt($column, $value)
	{
		return $this->where($column, array('$gt' => $value), '$nor');
	}

	/**
	 * Add an "$gte comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function whereGte($column, $value)
	{
		return $this->where($column, array('$gte' => $value), 'first');
	}

	/**
	 * Add an "$gte comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereGte($column, $value)
	{
		return $this->where($column, array('$gte' => $value), '$and');
	}

	/**
	 * Add an "$gte comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereGte($column, $value)
	{
		return $this->where($column, array('$gte' => $value), '$or');
	}

	/**
	 * Add an "$gte comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereGte($column, $value)
	{
		return $this->where($column, array('$gte' => $value), '$nor');
	}

	/**
	 * Add a where between statement to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $min
	 * @param  int     $max
	 * @return LMongo\Query\Builder
	 */
	public function whereBetween($column, $min, $max)
	{
		return $this->where($column, array('$gt' => $min, '$lt' => $max), 'first');
	}

	/**
	 * Add a where between statement to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $min
	 * @param  int     $max
	 * @return LMongo\Query\Builder
	 */
	public function andWhereBetween($column, $min, $max)
	{
		return $this->where($column, array('$gt' => $min, '$lt' => $max), '$and');
	}

	/**
	 * Add a where between statement to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $min
	 * @param  int     $max
	 * @return LMongo\Query\Builder
	 */
	public function orWhereBetween($column, $min, $max)
	{
		return $this->where($column, array('$gt' => $min, '$lt' => $max), '$or');
	}

	/**
	 * Add a where between statement to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $min
	 * @param  int     $max
	 * @return LMongo\Query\Builder
	 */
	public function norWhereBetween($column, $min, $max)
	{
		return $this->where($column, array('$gt' => $min, '$lt' => $max), '$nor');
	}

	/**
	 * Add an "$in comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function whereIn($column, array $value)
	{
		return $this->where($column, array('$in' => array_values($value)), 'first');
	}

	/**
	 * Add an "$in comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereIn($column, array $value)
	{
		return $this->where($column, array('$in' => array_values($value)), '$and');
	}

	/**
	 * Add an "$in comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereIn($column, array $value)
	{
		return $this->where($column, array('$in' => array_values($value)), '$or');
	}

	/**
	 * Add an "$in comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereIn($column, array $value)
	{
		return $this->where($column, array('$in' => array_values($value)), '$nor');
	}

	/**
	 * Add an "$nin comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function whereNin($column, array $value)
	{
		return $this->where($column, array('$nin' => array_values($value)), 'first');
	}

	/**
	 * Add an "$nin comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereNin($column, array $value)
	{
		return $this->where($column, array('$nin' => array_values($value)), '$and');
	}

	/**
	 * Add an "$nin comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereNin($column, array $value)
	{
		return $this->where($column, array('$nin' => array_values($value)), '$or');
	}

	/**
	 * Add an "$nin comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  array   $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereNin($column, array $value)
	{
		return $this->where($column, array('$nin' => array_values($value)), '$nor');
	}

	/**
	 * Add an "$ne comparison operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  string  $value
	 * @return LMongo\Query\Builder
	 */
	public function whereNe($column, $value)
	{
		return $this->where($column, array('$ne' => $value), 'first');
	}

	/**
	 * Add an "$ne comparison operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  string  $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereNe($column, $value)
	{
		return $this->where($column, array('$ne' => $value), '$and');
	}

	/**
	 * Add an "$ne comparison operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  string  $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereNe($column, $value)
	{
		return $this->where($column, array('$ne' => $value), '$or');
	}

	/**
	 * Add an "$ne comparison operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  string  $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereNe($column, $value)
	{
		return $this->where($column, array('$ne' => $value), '$nor');
	}

	/**
	 * Add an "$exists element operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  boolean  $exists
	 * @return LMongo\Query\Builder
	 */
	public function whereExists($column, $exists = true)
	{
		return $this->where($column, array('$exists' => $exists), 'first');
	}

	/**
	 * Add an "$exists element operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  boolean  $exists
	 * @return LMongo\Query\Builder
	 */
	public function andWhereExists($column, $exists = true)
	{
		return $this->where($column, array('$exists' => $exists), '$and');
	}

	/**
	 * Add an "$exists element operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  boolean  $exists
	 * @return LMongo\Query\Builder
	 */
	public function orWhereExists($column, $exists = true)
	{
		return $this->where($column, array('$exists' => $exists), '$or');
	}

	/**
	 * Add an "$exists element operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  boolean  $exists
	 * @return LMongo\Query\Builder
	 */
	public function norWhereExists($column, $exists = true)
	{
		return $this->where($column, array('$exists' => $exists), '$nor');
	}

	/**
	 * Add an "$type element operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $type
	 * @return LMongo\Query\Builder
	 */
	public function whereType($column, $type)
	{
		return $this->where($column, array('$type' => $type), 'first');
	}

	/**
	 * Add an "$type element operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $type
	 * @return LMongo\Query\Builder
	 */
	public function andWhereType($column, $type)
	{
		return $this->where($column, array('$type' => $type), '$and');
	}

	/**
	 * Add an "$type element operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $type
	 * @return LMongo\Query\Builder
	 */
	public function orWhereType($column, $type)
	{
		return $this->where($column, array('$type' => $type), '$or');
	}

	/**
	 * Add an "$type element operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $type
	 * @return LMongo\Query\Builder
	 */
	public function norWhereType($column, $type)
	{
		return $this->where($column, array('$type' => $type), '$nor');
	}

	/**
	 * Add an "$mod element operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $divisor
	 * @param  int     $remainder
	 * @return LMongo\Query\Builder
	 */
	public function whereMod($column, $divisor, $remainder)
	{
		return $this->where($column, array('$mod' => array($divisor, $remainder)), 'first');
	}

	/**
	 * Add an "$mod element operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $divisor
	 * @param  int     $remainder
	 * @return LMongo\Query\Builder
	 */
	public function andWhereMod($column, $divisor, $remainder)
	{
		return $this->where($column, array('$mod' => array($divisor, $remainder)), '$and');
	}

	/**
	 * Add an "$mod element operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $divisor
	 * @param  int     $remainder
	 * @return LMongo\Query\Builder
	 */
	public function orWhereMod($column, $divisor, $remainder)
	{
		return $this->where($column, array('$mod' => array($divisor, $remainder)), '$or');
	}

	/**
	 * Add an "$mod element operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $divisor
	 * @param  int     $remainder
	 * @return LMongo\Query\Builder
	 */
	public function norWhereMod($column, $divisor, $remainder)
	{
		return $this->where($column, array('$mod' => array($divisor, $remainder)), '$nor');
	}

	/**
	 * Add an "$regex JavaScript operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return LMongo\Query\Builder
	 */
	public function whereRegex($column, $value)
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex($value);
		}

		return $this->where($column, $value, 'first');
	}

	/**
	 * Add an "$regex JavaScript operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereRegex($column, $value)
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex($value);
		}

		return $this->where($column, $value, '$and');
	}

	/**
	 * Add an "$regex JavaScript operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereRegex($column, $value)
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex($value);
		}

		return $this->where($column, $value, '$or');
	}

	/**
	 * Add an "$regex JavaScript operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereRegex($column, $value)
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex($value);
		}

		return $this->where($column, $value, '$nor');
	}

	/**
	 * Add a like statement with $regex operation to logical operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @param  string  $flags
	 * @return LMongo\Query\Builder
	 */
	public function whereLike($column, $value, $flags = 'im')
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex('/'.$value.'/'.$flags);
		}

		return $this->where($column, $value, 'first');
	}

	/**
	 * Add a like statement with $regex operation to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @param  string  $flags
	 * @return LMongo\Query\Builder
	 */
	public function andWhereLike($column, $value, $flags = 'im')
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex('/'.$value.'/'.$flags);
		}

		return $this->where($column, $value, '$and');
	}

	/**
	 * Add a like statement with $regex operation to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @param  string  $flags
	 * @return LMongo\Query\Builder
	 */
	public function orWhereLike($column, $value, $flags = 'im')
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex('/'.$value.'/'.$flags);
		}

		return $this->where($column, $value, '$or');
	}

	/**
	 * Add a like statement with $regex operation to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @param  string  $flags
	 * @return LMongo\Query\Builder
	 */
	public function norWhereLike($column, $value, $flags = 'im')
	{
		if ( ! $value instanceof MongoRegex)
		{
			$value = new MongoRegex('/'.$value.'/'.$flags);
		}

		return $this->where($column, $value, '$nor');
	}

	/**
	 * Add an "$near geospatial operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @param  string  $boolean
	 * @return LMongo\Query\Builder
	 */
	public function whereNear($column, array $coords, $geometry = null, $maxDistance = null, $boolean = 'first')
	{
		if(is_null($geometry))
		{
			$value = array('$near' => $coords);

			if( ! is_null($maxDistance))
			{
				$value['$maxDistance'] = $maxDistance;
			}
		}
		else
		{
			$value = array('$near' => array('$geometry' => array('type' => $geometry, 'coordinates' => $coords)));

			if( ! is_null($maxDistance))
			{
				$value['$near']['$geometry']['$maxDistance'] = $maxDistance;
			}
		}

		return $this->where($column, $value, $boolean);
	}

	/**
	 * Add an "$near geospatial operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @return LMongo\Query\Builder
	 */
	public function andWhereNear($column, array $coords, $geometry = null, $maxDistance = null)
	{
		return $this->whereNear($column, $coords, $geometry, $maxDistance, '$and');
	}

	/**
	 * Add an "$near geospatial operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @return LMongo\Query\Builder
	 */
	public function orWhereNear($column, array $coords, $geometry = null, $maxDistance = null)
	{
		return $this->whereNear($column, $coords, $geometry, $maxDistance, '$or');
	}

	/**
	 * Add an "$near geospatial operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @return LMongo\Query\Builder
	 */
	public function norWhereNear($column, array $coords, $geometry = null, $maxDistance = null)
	{
		return $this->whereNear($column, $coords, $geometry, $maxDistance, '$nor');
	}

	/**
	 * Add an "$nearSphere geospatial operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @param  string  $boolean
	 * @return LMongo\Query\Builder
	 */
	public function whereNearSphere($column, array $coords, $geometry = null, $maxDistance = null, $boolean = 'first')
	{
		if(is_null($geometry))
		{
			$value = array('$nearSphere' => $coords);

			if( ! is_null($maxDistance))
			{
				$value['$maxDistance'] = $maxDistance;
			}
		}
		else
		{
			$value = array('$nearSphere' => array('$geometry' => array('type' => $geometry, 'coordinates' => $coords)));

			if( ! is_null($maxDistance))
			{
				$value['$nearSphere']['$geometry']['$maxDistance'] = $maxDistance;
			}
		}

		return $this->where($column, $value, $boolean);
	}

	/**
	 * Add an "$nearSphere geospatial operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @return LMongo\Query\Builder
	 */
	public function andWhereNearSphere($column, array $coords, $geometry = null, $maxDistance = null)
	{
		return $this->whereNearSphere($column, $coords, $geometry, $maxDistance, '$and');
	}

	/**
	 * Add an "$nearSphere geospatial operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @return LMongo\Query\Builder
	 */
	public function orWhereNearSphere($column, array $coords, $geometry = null, $maxDistance = null)
	{
		return $this->whereNearSphere($column, $coords, $geometry, $maxDistance, '$or');
	}

	/**
	 * Add an "$nearSphere geospatial operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  array   $coords
	 * @param  mixed   $geometry
	 * @param  mixed   $maxDistance
	 * @return LMongo\Query\Builder
	 */
	public function norWhereNearSphere($column, array $coords, $geometry = null, $maxDistance = null)
	{
		return $this->whereNearSphere($column, $coords, $geometry, $maxDistance, '$nor');
	}

	/**
	 * Add an "$geoWithin geospatial operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @param  string  $boolean
	 * @return LMongo\Query\Builder
	 */
	public function whereGeoWithin($column, $shape, array $coords, $boolean = 'first')
	{
		if('$' == $shape[0])
		{
			$value = array('$geoWithin' => array($shape => $coords));
		}
		else
		{
			$value = array('$geoWithin' => array('$geometry' => array('type' => $shape, 'coordinates' => $coords)));
		}

		return $this->where($column, $value, $boolean);
	}

	/**
	 * Add an "$geoWithin geospatial operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function andWhereGeoWithin($column, $shape, array $coords)
	{
		return $this->whereGeoWithin($column, $shape, $coords, '$and');
	}

	/**
	 * Add an "$geoWithin geospatial operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function orWhereGeoWithin($column, $shape, array $coords)
	{
		return $this->whereGeoWithin($column, $shape, $coords, '$or');
	}

	/**
	 * Add an "$geoWithin geospatial operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function norWhereGeoWithin($column, $shape, array $coords)
	{
		return $this->whereGeoWithin($column, $shape, $coords, '$nor');
	}

	/**
	 * Add an "$geoIntersects geospatial operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  string  $geometry
	 * @param  array   $coords
	 * @param  string  $boolean
	 * @return LMongo\Query\Builder
	 */
	public function whereGeoIntersects($column, $geometry, array $coords, $boolean = 'first')
	{
		$value = array('$geoIntersects' => array('$geometry' => array('type' => $geometry, 'coordinates' => $coords)));

		return $this->where($column, $value, $boolean);
	}

	/**
	 * Add an "$geoIntersects geospatial operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  string  $geometry
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function andWhereGeoIntersects($column, $geometry, array $coords)
	{
		return $this->whereGeoIntersects($column, $geometry, $coords, '$and');
	}

	/**
	 * Add an "$geoIntersects geospatial operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  string  $geometry
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function orWhereGeoIntersects($column, $geometry, array $coords)
	{
		return $this->whereGeoIntersects($column, $geometry, $coords, '$or');
	}

	/**
	 * Add an "$geoIntersects geospatial operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  string  $geometry
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function norWhereGeoIntersects($column, $geometry, array $coords)
	{
		return $this->whereGeoIntersects($column, $geometry, $coords, '$nor');
	}

	/**
	 * Add an "$size array operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function whereSize($column, $value)
	{
		return $this->where($column, array('$size' => $value), 'first');
	}

	/**
	 * Add an "$size array operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function andWhereSize($column, $value)
	{
		return $this->where($column, array('$size' => $value), '$and');
	}

	/**
	 * Add an "$size array operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function orWhereSize($column, $value)
	{
		return $this->where($column, array('$size' => $value), '$or');
	}

	/**
	 * Add an "$size array operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  int     $value
	 * @return LMongo\Query\Builder
	 */
	public function norWhereSize($column, $value)
	{
		return $this->where($column, array('$size' => $value), '$nor');
	}

	/**
	 * Add a nested where statement to the query.
	 *
	 * @param  Closure  $callback
	 * @param  string   $logic
	 * @return LMongo\Query\Builder
	 */
	public function whereNested(Closure $callback, $logic = '$and')
	{
		$type = 'Nested';

		$query = $this->newQuery();

		call_user_func($callback, $query);

		if (count($query->wheres))
		{
			$this->wheres[] = compact('type', 'query', 'logic');
		}

		return $this;
	}

	/**
	 * Compile the query
	 *
	 * @param  Builder $query
	 * @return array
	 */
	public function compileWheres(Builder $query)
	{
		$wheres = array();

		foreach ($query->wheres as $where)
		{
			if('first' == $where['logic'])
			{
				$first = $where;

				//We'll handle this later
				continue;
			}

			$method = "compileWhere{$where['type']}";

			$wheres[$where['logic']][] = $query->$method($query, $where);
		}

		//Handle first item
		if(isset($first))
		{
			$method = "compileWhere{$first['type']}";

			$where = $query->$method($query, $first);

			if(1 >= count($wheres))
			{
				$key = key($wheres)?: '$and';
				isset($wheres[$key]) OR $wheres[$key] = array();
				array_unshift($wheres[$key], $where);
			}
			else
			{
				throw new \Exception('More then one logical operator found on root');
			}
		}

		return $wheres;
	}

	/**
	 * Compile a basic operation clause.
	 *
	 * @param  Builder  $query
	 * @param  array    $where
	 * @return array
	 */
	protected function compileWhereBasic(Builder $query, $where)
	{
		return array($where['column'] => $where['value']);
	}

	/**
	 * Compile a nested operation clause.
	 *
	 * @param  Builder  $query
	 * @param  array    $where
	 * @return array
	 */
	protected function compileWhereNested(Builder $query, $where)
	{
		$nested = $where['query'];

		return $this->compileWheres($nested);
	}

	/**
	 * Reset the query
	 *
	 * @return void
	 */
	public function resetQuery()
	{
		$this->wheres = array();
		$this->first = null;
		$this->columns = null;
		$this->orders = null;
		$this->limit = null;
		$this->offset = null;
	}

	/**
	 * Add an "order by" clause to the query.
	 *
	 * @param  string $column
	 * @param  string $direction
	 * @return LMongo\Query\Builder
	 */
	public function orderBy($column, $direction = 'asc')
	{
		$this->orders[$column] = $this->prepareOrderBy($direction);

		return $this;
	}

	/**
	 * Set the "offset" value of the query.
	 *
	 * @param  int  $value
	 * @return LMongo\Query\Builder
	 */
	public function skip($value)
	{
		$this->offset = $value;

		return $this;
	}

	/**
	 * Set the "limit" value of the query.
	 *
	 * @param  int  $value
	 * @return LMongo\Query\Builder
	 */
	public function take($value)
	{
		$this->limit = $value;

		return $this;
	}

	/**
	 * Set the limit and offset for a given page.
	 *
	 * @param  int  $page
	 * @param  int  $perPage
	 * @return LMongo\Query\Builder
	 */
	public function forPage($page, $perPage = 15)
	{
		return $this->skip(($page - 1) * $perPage)->take($perPage);
	}

	/**
	 * Execute the query.
	 *
	 * @param  array  $columns
	 * @return LMongo\Query\Cursor
	 */
	public function get($columns = array())
	{
		if ( ! is_null($this->cacheMinutes)) return $this->getCached($columns);

		return $this->getFresh($columns, true);
	}

	/**
	 * Execute fresh query.
	 *
	 * @param  array  $columns
	 * @return array
	 */
	public function getFresh($columns = array(), $cursor = false)
	{
		if (is_null($this->columns))
		{
			$this->columns = $columns;
		}

		$results = $this->connection->{$this->collection}
							->find($this->compileWheres($this), $this->prepareColumns());

		if( ! is_null($this->orders))
		{
			$results = $results->sort($this->orders);
		}

		if( ! is_null($this->offset))
		{
			$results = $results->skip($this->offset);
		}

		if( ! is_null($this->limit))
		{
			$results = $results->limit($this->limit);
		}

		if($cursor)
		{
			return new Cursor($results);
		}
		else
		{
			return iterator_to_array($results);
		}
	}

	/**
	 * Execute cached query.
	 *
	 * @param  array  $columns
	 * @return array
	 */
	public function getCached($columns = array())
	{
		list($key, $minutes) = $this->getCacheInfo();

		// If the query is requested ot be cached, we will cache it using a unique key
		// for this database connection and query statement, including the bindings
		// that are used on this query, providing great convenience when caching.
		$cache = $this->connection->getCacheManager();

		$callback = $this->getCacheCallback($columns);

		return $cache->remember($key, $minutes, $callback);
	}

	/**
	 * Get the cache key and cache minutes as an array.
	 *
	 * @return array
	 */
	protected function getCacheInfo()
	{
		return array($this->getCacheKey(), $this->cacheMinutes);
	}

	/**
	 * Get a unique cache key for the complete query.
	 *
	 * @return string
	 */
	public function getCacheKey()
	{
		return $this->cacheKey ?: $this->generateCacheKey();
	}

	/**
	 * Generate the unique cache key for the query.
	 *
	 * @return string
	 */
	public function generateCacheKey()
	{
		$name = $this->connection->getName();

		$key = array();
		$key[] = serialize($this->compileWheres($this));

		if (is_null($this->columns))
		{
			$key[] = serialize($this->columns);
		}

		if( ! is_null($this->orders))
		{
			$key[] = serialize($this->orders);
		}

		if( ! is_null($this->offset))
		{
			$key[] = 'skip'.$this->offset;
		}

		if( ! is_null($this->limit))
		{
			$key[] = 'limit'.$this->limit;
		}

		return md5($name . implode(',', $key));
	}

	/**
	 * Get the Closure callback used when caching queries.
	 *
	 * @param  array  $columns
	 * @return \Closure
	 */
	protected function getCacheCallback($columns)
	{
		$me = $this;

		return function() use ($me, $columns) { return $me->getFresh($columns); };
	}

	/**
	 * Concatenate values of a given column as a string.
	 *
	 * @param  string  $column
	 * @param  string  $glue
	 * @return string
	 */
	public function implode($column, $glue = null)
	{
		$values = $this->distinct($column);

		if (is_null($glue)) return implode($values);

		return implode($glue, $values);
	}

	/**
	 * Indicate that the query results should be cached.
	 *
	 * @param  int  $minutes
	 * @param  string  $key
	 * @return \LMongo\Query\Builder
	 */
	public function remember($minutes, $key = null)
	{
		list($this->cacheMinutes, $this->cacheKey) = array($minutes, $key);

		return $this;
	}

	/**
	 * Execute a query for a single record by _id.
	 *
	 * @param  int    $id
	 * @param  array  $columns
	 * @return mixed
	 */
	public function find($id, $columns = array())
	{
		$id = new MongoID((string) $id);

		return $this->where('_id', $id)->first($columns);
	}

	/**
	 * Pluck a single column from the database.
	 *
	 * @param  string  $column
	 * @return mixed
	 */
	public function pluck($column)
	{
		$result = (array) $this->first(array($column));

		return count($result) > 0 ? reset($result) : null;
	}

	/**
	 * Execute the query and get the first result.
	 *
	 * @param  array  $columns
	 * @return mixed
	 */
	public function first($columns = array())
	{
		$results = $this->take(1)->get($columns)->toArray();

		return count($results) > 0 ? reset($results) : null;
	}

	/**
	 * Execute the query to only return distinct results.
	 *
	 * @param  string $column
	 * @param  array  $query
	 * @return array
	 */
	public function distinct($column, $query = array())
	{
		if (0 == count($query))
		{
			$query = $this->compileWheres($this);
		}

		$results = $this->connection->{$this->collection}->distinct($column, $query);

		return $results;
	}

	/**
	 * Get a paginator.
	 *
	 * @param  int    $perPage
	 * @param  array  $columns
	 * @return Illuminate\Pagination\Paginator
	 */
	public function paginate($perPage = 15, $columns = array())
	{
		$paginator = $this->connection->getPaginator();

		$page = $paginator->getCurrentPage();

		$results = $this->forPage($page, $perPage)->get($columns);

		$total = $results->countAll();

		return $paginator->make($results->toArray(), $total, $perPage);
	}

	/**
	 * Determine if any document exist for the current query.
	 *
	 * @return bool
	 */
	public function exists()
	{
		return $this->count() > 0;
	}

	/**
	 * Execute the query to return count.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->get()->count();
	}

	/**
	 * Retrieve the sum of the values of a given column.
	 *
	 * @param  string  $column
	 * @param  array   $query
	 * @return mixed
	 */
	public function sum($column, $query = array())
	{
		return $this->aggregate(__FUNCTION__, $column, $query = array());
	}

	/**
	 * Retrieve the avg of the values of a given column.
	 *
	 * @param  string  $column
	 * @param  array   $query
	 * @return mixed
	 */
	public function avg($column, $query = array())
	{
		return $this->aggregate(__FUNCTION__, $column, $query = array());
	}

	/**
	 * Retrieve the maximum value of a given column.
	 *
	 * @param  string  $column
	 * @param  array   $query
	 * @return mixed
	 */
	public function max($column, $query = array())
	{
		return $this->aggregate(__FUNCTION__, $column, $query = array());
	}

	/**
	 * Retrieve the minimum value of a given column.
	 *
	 * @param  string  $column
	 * @param  array   $query
	 * @return mixed
	 */
	public function min($column, $query = array())
	{
		return $this->aggregate(__FUNCTION__, $column, $query = array());
	}

	/**
	 * Execute an aggregate function on the database.
	 * @param  string $function
	 * @param  string $column
	 * @param  array  $query
	 * @return mixed
	 */
	public function aggregate($function, $column, $query = array())
	{
		if (0 == count($query))
		{
			$query = $this->compileWheres($this);
		}

		$pipeline = array();

		if(count($query))
		{
			$pipeline[] = array('$match' => $query);
		}

		$pipeline[] = array('$group' => array('_id' => 0, $function => array('$'.$function => '$'.$column)));

		$result = $this->connection->{$this->collection}->aggregate($pipeline);

		if(1 == (int) $result['ok'] AND isset($result['result'][0][$function]))
		{
			return $result['result'][0][$function];
		}

		return 0;
	}

	/**
	 * Execute a group command on the database.
	 *
	 * @param  array  $initial
	 * @param  mixed  $reduce
	 * @param  mixed  $columns
	 * @param  array  $options
	 * @return LMongo\Query\Cursor
	 */
	public function group(array $initial, $reduce, $columns = array(), array $options = array())
	{
		if (is_null($this->columns))
		{
			$this->columns = $columns;
		}

		if (is_string($this->columns))
		{
			$this->columns = new \MongoCode($this->columns);
		}

		if (is_string($reduce))
		{
			$reduce = new \MongoCode($reduce);
		}

		$conditions = $this->compileWheres($this);

		if(count($conditions))
		{
			$options['condition'] = $conditions;
		}

		if (isset($options['finalize']) and is_string($options['finalize']))
		{
			$options['finalize'] = new \MongoCode($options['finalize']);
		}

		if(empty($options))
		{
			$result = $this->connection->{$this->collection}->group($this->columns, $initial, $reduce);
		}
		else
		{
			$result = $this->connection->{$this->collection}->group($this->columns, $initial, $reduce, $options);
		}

		if ( ! $result['ok'])
		{
			throw new \MongoException($result['errmsg']);
		}

		return $result['retval'];
	}

	/**
	 * Insert a new document into the database.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function insert($data)
	{
		$result = $this->connection->{$this->collection}->insert($data);

		if(1 == (int) $result['ok'])
		{
			return $data['_id'];
		}

		return false;
	}

	/**
	 * Insert a new documents into the database.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function batchInsert($data)
	{
		if ( ! is_array(reset($data)))
		{
			$data = array($data);
		}

		$result = $this->connection->{$this->collection}->batchInsert($data);

		if(1 == (int) $result['ok'])
		{
			return array_map(function($document)
			{
			    return $document['_id'];
			}, $data);
		}

		return false;
	}

	/**
	 * Save the document. Insert into the database if its not exists.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function save($data)
	{
		$this->connection->{$this->collection}->save($data);

		if(isset($data['_id']))
		{
			return $data['_id'];
		}

		return false;
	}

	/**
	 * Perform update.
	 *
	 * @param  array  $query
	 * @return int
	 */
	protected function performUpdate(array $query)
	{
		$result = $this->connection->{$this->collection}->update($this->compileWheres($this), $query, array('multiple' => true));

		if(1 == (int) $result['ok'])
		{
			return $result['n'];
		}

		return 0;
	}

	/**
	 * Update a document in the database.
	 *
	 * @param  array  $data
	 * @return int
	 */
	public function update(array $data)
	{
		$update = array('$set' => $data);

		return $this->performUpdate($update);
	}

	/**
	 * Set the values for the keys.
	 *
	 * @param  mixed  $column
	 * @param  mixed  $value
	 * @return int
	 */
	public function setField($column, $value = null)
	{
		if(is_array($column))
		{
			$update = array('$set' => $column);
		}
		else
		{
			$update = array('$set' => array($column => $value));
		}

		return $this->performUpdate($update);
	}

	/**
	 * Unset or remove the given keys.
	 *
	 * @param  mixed  $column
	 * @return int
	 */
	public function unsetField($column)
	{
		$columns = array();

		foreach ((array) $column as $key)
		{
			$columns[$key] = true;
		}

		$update = array('$unset' => $columns);

		return $this->performUpdate($update);
	}

	/**
	 * Renames a field.
	 *
	 * @param  mixed  $old
	 * @param  mixed  $new
	 * @return int
	 */
	public function renameField($old, $new = null)
	{
		if(is_array($old))
		{
			$update = array('$rename' => $old);
		}
		else
		{
			$update = array('$rename' => array($old => $new));
		}

		return $this->performUpdate($update);
	}

	/**
	 * Increment a column's value by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @param  array   $extra
	 * @return int
	 */
	public function increment($column, $amount = 1, array $extra = array())
	{
		$update = array('$inc' => array($column => $amount));

		if(count($extra))
		{
			$update['$set'] = $extra;
		}

		return $this->performUpdate($update);
	}

	/**
	 * Decrement a column's value by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @param  array   $extra
	 * @return int
	 */
	public function decrement($column, $amount = 1, array $extra = array())
	{
		$update = array('$inc' => array($column => -$amount));

		if(count($extra))
		{
			$update['$set'] = $extra;
		}

		return $this->performUpdate($update);
	}

	/**
	 * Append one value to the array key.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return int
	 */
	public function push($column, $value = null)
	{
		if(is_array($column))
		{
			$update = array('$push' => $column);
		}
		else
		{
			$update = array('$push' => array($column => $value));
		}

		return $this->performUpdate($update);
	}

	/**
	 * Append several value to the array key.
	 *
	 * @param  string  $column
	 * @param  mixed   $values
	 * @return int
	 */
	public function pushAll($column, array $values)
	{
		$update = array('$push' => array($column => array('$each' => $values)));

		return $this->performUpdate($update);
	}

	/**
	 * Append one unique value to the array key.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return int
	 */
	public function addToSet($column, $value = null)
	{
		if(is_array($column))
		{
			$update = array('$addToSet' => $column);
		}
		else
		{
			$update = array('$addToSet' => array($column => $value));
		}

		return $this->performUpdate($update);
	}

	/**
	 * Remove one value from the array key.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return int
	 */
	public function pull($column, $value = null)
	{
		if(is_array($column))
		{
			$update = array('$pull' => $column);
		}
		else
		{
			$update = array('$pull' => array($column => $value));
		}

		return $this->performUpdate($update);
	}

	/**
	 * Remove several value from the array key.
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @return int
	 */
	public function pullAll($column, $value = null)
	{
		if(is_array($column))
		{
			$update = array('$pullAll' => $column);
		}
		else
		{
			$update = array('$pullAll' => array($column => $value));
		}

		return $this->performUpdate($update);
	}

	/**
	 * Remove the last element from the array key.
	 *
	 * @param  string  $column
	 * @param  int     $type
	 * @return int
	 */
	public function pop($column, $type = 1)
	{
		$update = array('$pop' => array($column => $type));

		return $this->performUpdate($update);
	}

	/**
	 * Delete a document from the database.
	 *
	 * @return int
	 */
	public function delete()
	{
		$query = $this->compileWheres($this);

		$result = $this->connection->{$this->collection}->remove($query);

		if(1 == (int) $result['ok'])
		{
			return $result['n'];
		}

		return 0;
	}

	/**
	 * Drop the collection.
	 *
	 * @return void
	 */
	public function truncate()
	{
		$result = $this->connection->{$this->collection}->drop();

		if(1 == (int) $result['ok'])
		{
			return true;
		}

		return false;
	}

	/**
	 * Alias of delete.
	 *
	 * @return int
	 */
	public function remove()
	{
		return $this->delete();
	}

	/**
	 * Set the collection which the query is targeting.
	 *
	 * @param  string  $collection
	 * @return LMongo\Query\Builder
	 */
	public function collection($collection)
	{
		$this->collection = $collection;

		$this->resetQuery();

		return $this;
	}

	/**
	 * Transform sql style order statement to mongodb style.
	 *
	 * @param  mixed $direction
	 * @return int
	 */
	private function prepareOrderBy($direction)
	{
		if('asc' == $direction)
		{
			$direction = 1;
		}
		elseif('desc' == $direction)
		{
			$direction = -1;
		}

		return $direction;
	}

	/**
	 * Retrieve the select statement.
	 *
	 * @return array
	 */
	private function prepareColumns()
	{
		$columns = array();

		foreach ($this->columns as $column)
		{
			$columns[$column] = 1;
		}

		if(count($columns) AND ! isset($columns['_id']))
		{
			$columns['_id'] = false;
		}

		return $columns;
	}

	/**
	 * Get a copy of the where clauses and reset.
	 *
	 * @return array
	 */
	public function getAndResetWheres()
	{
		$values = $this->wheres;

		$this->wheres = array();

		return $values;
	}

	/**
	 * new Query Builder instance
	 *
	 * @return Builder
	 */
	public function newQuery()
	{
		return new Builder($this->connection);
	}

	/**
	 * Get the database connection instance.
	 *
	 * @return \LMongo\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Set the database connection instance.
	 *
	 * @return \LMongo\Connection
	 */
	public function setConnection(\LMongo\Connection $connection)
	{
		$this->connection = $connection;
	}
}