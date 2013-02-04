<?php namespace LMongo\Query;

use Closure;
use MongoRegex;
use MongoCode;
use MongoID;

class Builder {

	/**
	 * The database connection instance.
	 *
	 * @var LMongo\Database
	 */
	protected $connection;

	/**
	 * The database collection
	 *
	 * @var string
	 */
	protected $collection;

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
	 * Create a new query builder instance.
	 *
	 * @param  \LMongo\Database $connection
	 * @return void
	 */
	public function __construct(\LMongo\Database $connection)
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
	 * @return LMongo\Query\Builder
	 */
	public function whereExists($column)
	{
		return $this->where($column, array('$exists' => true), 'first');
	}

	/**
	 * Add an "$exists element operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @return LMongo\Query\Builder
	 */
	public function andWhereExists($column)
	{
		return $this->where($column, array('$exists' => true), '$and');
	}

	/**
	 * Add an "$exists element operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @return LMongo\Query\Builder
	 */
	public function orWhereExists($column)
	{
		return $this->where($column, array('$exists' => true), '$or');
	}

	/**
	 * Add an "$exists element operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @return LMongo\Query\Builder
	 */
	public function norWhereExists($column)
	{
		return $this->where($column, array('$exists' => true), '$nor');
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
	 * @param  float   $lon
	 * @param  float   $lat
	 * @return LMongo\Query\Builder
	 */
	public function whereNear($column, $lon, $lat)
	{
		return $this->where($column, array('$near' => array($lon, $lat)), 'first');
	}

	/**
	 * Add an "$near geospatial operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  float   $lon
	 * @param  float   $lat
	 * @return LMongo\Query\Builder
	 */
	public function andWhereNear($column, $lon, $lat)
	{
		return $this->where($column, array('$near' => array($lon, $lat)), '$and');
	}

	/**
	 * Add an "$near geospatial operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  float   $lon
	 * @param  float   $lat
	 * @return LMongo\Query\Builder
	 */
	public function orWhereNear($column, $lon, $lat)
	{
		return $this->where($column, array('$near' => array($lon, $lat)), '$or');
	}

	/**
	 * Add an "$near geospatial operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  float   $lon
	 * @param  float   $lat
	 * @return LMongo\Query\Builder
	 */
	public function norWhereNear($column, $lon, $lat)
	{
		return $this->where($column, array('$near' => array($lon, $lat)), '$nor');
	}

	/**
	 * Add an "$within geospatial operation" clause to logical operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function whereWithin($column, $shape, array $coords)
	{
		return $this->where($column, array('$within' => array('$'.$shape => $coords)), 'first');
	}

	/**
	 * Add an "$within geospatial operation" clause to logical $and operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function andWhereWithin($column, $shape, array $coords)
	{
		return $this->where($column, array('$within' => array('$'.$shape => $coords)), '$and');
	}

	/**
	 * Add an "$within geospatial operation" clause to logical $or operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function orWhereWithin($column, $shape, array $coords)
	{
		return $this->where($column, array('$within' => array('$'.$shape => $coords)), '$or');
	}

	/**
	 * Add an "$within geospatial operation" clause to logical $nor operation.
	 *
	 * @param  string  $column
	 * @param  string  $shape
	 * @param  array   $coords
	 * @return LMongo\Query\Builder
	 */
	public function norWhereWithin($column, $shape, array $coords)
	{
		return $this->where($column, array('$within' => array('$'.$shape => $coords)), '$nor');
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

		$this->wheres[] = compact('type', 'query', 'logic');

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
	 * @return Illuminate\Database\Query\Builder
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
		if (is_null($this->columns))
		{
			$this->columns = $columns;
		}

		$results = $this->connection->{$this->collection}
							->find($this->compileWheres($this), $this->prepareColumns());

		if(!is_null($this->orders))
		{
			$results = $results->sort($this->orders);
		}

		if(!is_null($this->offset))
		{
			$results = $results->skip($this->offset);
		}

		if(!is_null($this->limit))
		{
			$results = $results->limit($this->limit);
		}

		return new Cursor($results);
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

		return count($result) > 0 ? end($result) : null;
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

		return current($results);
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
	 * Update a document in the database.
	 *
	 * @param  array  $data
	 * @return int
	 */
	public function update(array $data)
	{
		$update = array('$set' => $data);

		$result = $this->connection->{$this->collection}->update($this->compileWheres($this), $update, array('multiple' => true));

		if(1 == (int) $result['ok'])
		{
			return $result['n'];
		}

		return 0;
	}

	/**
	 * Increment a column's value by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @return int
	 */
	public function increment($column, $amount = 1)
	{
		$update = array('$inc' => array($column => $amount));

		$result = $this->connection->{$this->collection}->update($this->compileWheres($this), $update, array('multiple' => true));

		if(1 == (int) $result['ok'])
		{
			return $result['n'];
		}

		return 0;
	}

	/**
	 * Decrement a column's value by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @return int
	 */
	public function decrement($column, $amount = 1)
	{
		$update = array('$inc' => array($column => -$amount));

		$result = $this->connection->{$this->collection}->update($this->compileWheres($this), $update, array('multiple' => true));

		if(1 == (int) $result['ok'])
		{
			return $result['n'];
		}

		return 0;
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
	public function setCollection($collection)
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
	 * @return \LMongo\Database
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Set the database connection instance.
	 *
	 * @return \LMongo\Database
	 */
	public function setConnection(\LMongo\Database $connection)
	{
		$this->connection = $connection;
	}
}