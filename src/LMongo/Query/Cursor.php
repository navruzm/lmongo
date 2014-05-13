<?php namespace LMongo\Query;

use MongoCursor;
use Countable;
use IteratorAggregate;

class Cursor implements Countable, IteratorAggregate {

	/**
	 * Instance of MongoCursor
	 *
	 * @var MongoCursor
	 */
	protected $cursor;

	/**
	 * Constructor, sets the cursor
	 *
	 * @param MongoCursor $cursor
	 */
	public function __construct($cursor)
	{
		$this->cursor = $cursor;
	}

	/**
	 * Return the MongoCursor instance
	 *
	 * @return MongoCursor
	 */
	public function getCursor()
	{
		return $this->cursor;
	}

	/**
	 * Implement IteratorAggregate
	 *
	 * @return Iterator
	 */
	public function getIterator()
	{
		return $this->cursor;
	}

	/**
	 * Implement Countable
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->cursor->count(true);
	}

	/**
	 * Counts all query results
	 *
	 * @return int
	 */
	public function countAll()
	{
		return $this->cursor->count();
	}

	/**
	 * Get the items as array.
	 *
	 * @return  array
	 */
	public function toArray()
	{
		return iterator_to_array($this->getIterator());
	}

	/**
	 * Get the items as JSON.
	 *
	 * @param  int  $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Get the items as an object.
	 *
	 * @return object
	 */
	public function toObject()
	{
		return json_decode(json_encode($this->toArray()), FALSE);
	}

	/**
	 * Convert the cursor to string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}

	/**
	 * Route the original MongoCursor method
	 *
	 * @param  string $method
	 * @param  array $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments)
    {
        $return = call_user_func_array(array($this->cursor, $method), $arguments);

        if($return instanceof MongoCursor)
        {
            return $this;
        }

        return $return;
    }
}
