<?php

namespace BookneticApp\Providers;

class QueryBuilder
{

	/**
	 * @var Model
	 */
	private $model;

	private $whereArr = [];
	private $orderByArr = [];
	private $groupByArr = [];
	private $columnsArr = [];
	private $joins = [];
	private $noTenant = false;

	private $offset;
	private $limit;

	public function __construct( $model )
	{
		$this->model = $model;
	}

	public function get( $id = null )
	{
		$model = $this->model;

		return $this->where([ $model::getIdField() => $id ])->fetch();
	}

	public function where( $arr, $value = false, $valueSt2 = false )
	{
		$uniqueKey = md5( json_encode( [ $arr, $value, $valueSt2 ] ) );

		if( is_array( $arr ) )
		{
			$this->whereArr = array_merge( $this->whereArr, $arr);
		}
		else if( is_string( $arr ) && !empty( $arr ) && $value !== false && $valueSt2 !== false )
		{
			$this->whereArr[ $arr . '|' . $uniqueKey ] = [$value, $valueSt2];
		}
		else if( is_string( $arr ) && !empty( $arr ) && $value !== false )
		{
			$this->whereArr[ $arr . '|' . $uniqueKey ] = $value;
		}

		return $this;
	}

	public function orderBy( $arr )
	{
		$this->orderByArr = array_merge($this->orderByArr, (array)$arr);

		return $this;
	}

	public function groupBy( $arr )
	{
		$this->groupByArr = array_merge($this->groupByArr, (array)$arr);

		return $this;
	}

	public function select( $arr )
	{
		$this->columnsArr = array_merge($this->columnsArr, (array)$arr);

		return $this;
	}

	public function noTenant()
	{
		$this->noTenant = true;

		return $this;
	}

	public function limit( $limit )
	{
		$this->limit = $limit;

		return $this;
	}

	public function offset( $offset )
	{
		$this->offset = $offset;

		return $this;
	}

	private function join( $joinTo, $joinType, $select_fields )
	{
		$model = $this->model;

		$relations = $model::$relations;

		if( isset( $relations[ $joinTo ] ) )
		{
			$model = $relations[ $joinTo ][0];

			$field1 = DB::table( $model::getTableName() ) . '.' . $relations[ $joinTo ][1];
			$field2 = DB::table( $this->getTableName() ) . '.' . $relations[ $joinTo ][2];

			$this->joins[] = [ $model::getTableName(), [ [ $field1, '=', $field2 ] ], $joinType ];

			if( !empty( $select_fields ) )
			{
				$select_fields = is_array( $select_fields ) ? $select_fields : (array)$select_fields;

				if( empty( $this->columnsArr ) )
					$this->columnsArr[] = DB::table( $this->getTableName() ) . '.*';

				foreach ( $select_fields AS &$select_field )
				{
					$select_field = DB::table( $model::getTableName() ) . '.' . $select_field . ' AS `' . $joinTo . '_' . $select_field . '`';
				}

				$this->columnsArr = array_merge( $this->columnsArr, $select_fields );
			}
		}

		return $this;
	}

	public function leftJoin( $joinTo, $select_fields )
	{
		return $this->join( $joinTo, 'LEFT', $select_fields );
	}

	public function rightJoin( $joinTo, $select_fields )
	{
		return $this->join( $joinTo, 'RIGHT', $select_fields );
	}

	public function innerJoin( $joinTo, $select_fields )
	{
		return $this->join( $joinTo, 'INNER', $select_fields );
	}

	public function fetch()
	{
		$data = DB::fetch( $this->getTableName(), $this->whereArr, $this->orderByArr, $this->columnsArr, $this->offset, $this->limit, $this->groupByArr, $this->noTenant, $this->joins );

		if( !$data )
		{
			return $data;
		}

		return new Collection( $data, $this->model );
	}

	public function fetchAll()
	{
		$data = DB::fetchAll( $this->getTableName(), $this->whereArr, $this->orderByArr, $this->columnsArr, $this->offset, $this->limit, $this->groupByArr, $this->noTenant, $this->joins );
		$returnData = [];

		foreach ( $data AS $row )
		{
			$returnData[] = new Collection( $row, $this->model );
		}

		return $returnData;
	}

	public function toSql()
	{
		return DB::selectQuery( $this->getTableName(), $this->whereArr, $this->orderByArr, $this->columnsArr, $this->offset, $this->limit, $this->groupByArr, $this->noTenant, $this->joins );
	}

	public function count()
	{
		return $this->select(['count(0) as `row_count`'])->fetch()->row_count;
	}

	public function sum( $column )
	{
		return $this->select(['SUM('.$column.') as `sum_column`'])->fetch()->sum_column;
	}

	public function update( $data )
	{
		$whereArr = [];
		foreach ( $this->whereArr AS $field => $value )
		{
			$field = explode('|', $field);
			$field = $field[0];
			$whereArr[$field] = $value;
		}

		if( method_exists( $this->model, 'beforeUpdate' ) && call_user_func( [ $this->model, 'beforeUpdate' ], $data, $whereArr ) === false )
		{
			return false;
		}

		$result = DB::DB()->update( DB::table( $this->getTableName() ), $data, $whereArr );

		if( method_exists( $this->model, 'afterUpdate' ) )
		{
			call_user_func( [ $this->model, 'afterUpdate' ], $data, $whereArr );
		}

		return $result;
	}

	public function delete()
	{
		$whereArr = [];
		foreach ( $this->whereArr AS $field => $value )
		{
			$field = explode('|', $field);
			$field = $field[0];
			$whereArr[$field] = $value;
		}

		if( method_exists( $this->model, 'beforeDelete' ) && call_user_func( [ $this->model, 'beforeDelete' ], $whereArr ) === false )
		{
			return false;
		}

		$result = DB::delete( $this->getTableName(), $this->whereArr, $this->noTenant );

		if( method_exists( $this->model, 'afterDelete' ) )
		{
			call_user_func( [ $this->model, 'afterDelete' ], $whereArr );
		}

		return $result;
	}

	public function insert( $data )
	{
		if( Helper::isSaaSVersion() && in_array( $this->getTableName(), \BookneticSaaS\Providers\Helper::$tenantTables ) && !key_exists( 'tenant_id', $data ) )
		{
			$data['tenant_id'] = Permission::tenantId();
		}

		if( method_exists( $this->model, 'beforeInsert' ) && call_user_func( [ $this->model, 'beforeInsert' ], $data ) === false )
		{
			return false;
		}

		$result = DB::DB()->insert( DB::table( $this->getTableName() ), $data );

		if( method_exists( $this->model, 'afterInsert' ) )
		{
			call_user_func( [ $this->model, 'afterInsert' ], $data, DB::lastInsertedId() );
		}

		return $result;
	}

	public function getTableName()
	{
		$model = $this->model;

		return $model::getTableName();
	}

}