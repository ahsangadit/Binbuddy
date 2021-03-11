<?php

namespace BookneticApp\Providers;

/**
 * Class Collection
 * @package BookneticApp\Providers
 */
class Collection implements \ArrayAccess, \JsonSerializable
{

	/**
	 * @var Model
	 */
	private $model;

	/**
	 * @var array
	 */
	private $container = [];

	/**
	 * Collection constructor.
	 * @param array $array
	 */
	public function __construct( $array, $model )
	{
		$this->container = $array;
		$this->model = $model;
	}

	/**
	 * @param $offset
	 * @param $value
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset))
		{
			$this->container[] = $value;
		}
		else
		{
			$this->container[$offset] = $value;
		}
	}

	/**
	 * @param $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->container[$offset]);
	}

	/**
	 * @param $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->container[$offset]);
	}

	/**
	 * @param $offset
	 * @return mixed|null
	 */
	public function offsetGet($offset)
	{
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}

	public function __get( $name )
	{
		return $this->offsetGet( $name );
	}

	public function __isset( $name )
	{
		return $this->offsetExists( $name );
	}

	public function __call($name, $arguments)
	{
		$model = $this->model;

		$relations = $model::$relations;

		if( isset( $relations[ $name ] ) )
		{
			/**
			 * @var Model $rModel
			 */
			$rModel = $relations[ $name ][0];

			if( isset( $relations[ $name ][1] ) )
			{
				$relationFieldName = $relations[ $name ][1];
			}
			else
			{
				$model = $this->model;

				$relationFieldName = rtrim( $model::getTableName(), 's' ) . '_id';
			}

			if( isset( $relations[ $name ][2] ) )
			{
				$idFieldName = $relations[ $name ][2];
			}
			else
			{
				$idFieldName = 'id';
			}

			return $rModel::where( [ $relationFieldName => $this->{$idFieldName} ] );
		}
		else if( method_exists( $this->model, $name ) )
		{
			return call_user_func_array( [ $this->model, $name ], array_merge( [ $this ], $arguments ) );
		}

		return null;
	}

	public function toArray()
	{
		return $this->container;
	}

	public function jsonSerialize()
	{
		return $this->toArray();
	}

}
