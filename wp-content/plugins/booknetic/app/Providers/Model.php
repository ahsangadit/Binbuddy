<?php

namespace BookneticApp\Providers;

/**
 * Class Model
 * @package BookneticApp\Providers
 * @method static Collection get( $id = null )
 * @method static Collection insert( $data )
 * @method static Collection update( $data )
 * @method static Collection delete()
 * @method static QueryBuilder where( $arr, $value = false, $valueSt2 = false )
 * @method static int count()
 * @method static int sum()
 * @method static QueryBuilder orderBy( $arr )
 * @method static QueryBuilder limit( $limit )
 * @method static QueryBuilder offset( $offset )
 * @method static QueryBuilder select( $arr )
 * @method static QueryBuilder noTenant()
 * @method static Collection fetch()
 * @method static Collection[] fetchAll()
 * @method static string toSql()
 * @method static QueryBuilder leftJoin( $joinTo )
 * @method static QueryBuilder rightJoin( $joinTo )
 * @method static QueryBuilder innerJoin( $joinTo )
 */
class Model
{

	/**
	 * Table ID field name
	 *
	 * @var string
	 */
	protected static $idField = 'id';

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected static $tableName;

	/**
	 * Models' relationsips...
	 * @var array
	 */
	public static $relations = [];

	/**
	 * Create QueryBuilder isntance...
	 *
	 * @param $name
	 * @param $arguments
	 * @return QueryBuilder|mixed
	 */
	public static function __callStatic($name, $arguments)
	{
		$qb = new QueryBuilder( static::class );

		if( method_exists( $qb, $name ) )
		{
			return call_user_func_array( [$qb, $name], $arguments );
		}

		return $qb;
	}

	/**
	 * Create QueryBuilder isntance...
	 *
	 * @param $name
	 * @param $arguments
	 * @return QueryBuilder|mixed
	 */
	public function __call($name, $arguments)
	{
		$qb = new QueryBuilder( static::class );

		if( method_exists( $qb, $name ) )
		{
			return call_user_func_array( [$qb, $name], $arguments );
		}

		return $qb;
	}

	/**
	 * Get table name from Model name
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		if( !is_null( static::$tableName ) )
			return static::$tableName;

		$modelName = basename( str_replace('\\', '/', get_called_class()) );

		$tableName = strtolower( preg_replace('/([A-Z])/', '_$1', $modelName) ) . 's';
		return ltrim($tableName, '_');
	}

	public static function lastId()
	{
		return DB::lastInsertedId();
	}

	/**
	 * Get ID field name
	 *
	 * @return string
	 */
	public static function getIdField()
	{
		return static::$idField;
	}

}
