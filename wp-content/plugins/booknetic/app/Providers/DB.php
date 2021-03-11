<?php

namespace BookneticApp\Providers;

class DB
{

	const PLUGIN_DB_PREFIX = 'bkntc_';

	public static function DB()
	{
		global $wpdb;

		return $wpdb;
	}

	public static function table( $table )
	{
		return self::DB()->base_prefix . self::PLUGIN_DB_PREFIX . $table;
	}

	public static function selectQuery( $table , $where = null, $orderBy = null, $columns = [], $offset = null, $limit = null, $groupBy = null, $noTenant = false, $joins = [] )
	{
		$whereStatement = '';
		$joinStatement = '';
		$where = is_numeric($where) && $where >= 0 ? [$where] : $where;

		Permission::modelFilter( $table, $where, $noTenant );

		if( !empty($where) && is_array($where) )
		{
			$whereStatement = ' WHERE ' . DB::where( $where, ( empty( $joins ) ? '' : self::table( $table ) ) );
		}

		if( !empty( $joins ) && is_array( $joins ) )
		{
			foreach ( $joins AS $joinElement )
			{
				$joinTable = $joinElement[0];
				$joinConditions = $joinElement[1];
				$joinType = strtoupper( $joinElement[2] );

				$joinStatement .= " {$joinType} JOIN `".DB::table( $joinTable )."` ON " . DB::on( $joinConditions );
			}
		}

		$orderByQuery = '';
		if( !empty( $orderBy ) && is_array( $orderBy ) )
		{
			$orderByQuery = ' ORDER BY ' . implode( ', ', $orderBy );
		}

		$groupByQuery = '';
		if( !empty( $groupBy ) && is_array( $groupBy ) )
		{
			$groupByQuery = ' GROUP BY ' . implode( ', ', $groupBy );
		}

		$columns = empty($columns) ? '*' : implode(',', $columns);

		$limitOffset = '';
		if( !is_null( $limit ) && $limit > 0 )
		{
			$offset = is_null($offset) ? 0 : (int)$offset;
			$limit = (int)$limit;

			$limitOffset = " LIMIT {$offset}, {$limit}";
		}

		$queryString = "SELECT {$columns} FROM " . self::table($table) . $joinStatement . $whereStatement . $groupByQuery . $orderByQuery . $limitOffset;

		return self::raw( $queryString );
	}

	public static function delete( $table , $where = null, $noTenant = false )
	{
		$whereStatement = '';
		$where = is_numeric($where) && $where >= 0 ? [ $where ] : $where;

		Permission::modelFilter( $table, $where, $noTenant );

		if( !empty($where) && is_array($where) )
		{
			$whereStatement = ' WHERE ' . DB::where( $where, ( empty( $joins ) ? '' : self::table( $table ) ) );
		}

		$queryString = "DELETE FROM " . self::table($table) . $whereStatement;

		return self::DB()->query( self::raw( $queryString ) );
	}

	public static function fetch()
	{
		return self::DB()->get_row( call_user_func_array( [static::class, 'selectQuery'], func_get_args() ),ARRAY_A );
	}

	public static function fetchAll()
	{
		return self::DB()->get_results( call_user_func_array( [static::class, 'selectQuery'], func_get_args() ),ARRAY_A );
	}

	public static function raw( $raw_query, $args = [] )
	{
		if( empty( $args ) )
			return $raw_query;

		return DB::DB()->prepare( $raw_query, $args );
	}

	public static function tenantFilter( $and_or = 'AND', $field_name = '`tenant_id`' )
	{
		$tenantId = (int)Permission::tenantId();

		if( $tenantId > 0 )
		{
			return " {$and_or} {$field_name}='{$tenantId}' ";
		}

		return '';
	}

	public static function lastInsertedId()
	{
		return DB::DB()->insert_id;
	}

	private static function where( $where, $table = '' )
	{
		if( empty( $where ) || !is_array( $where ) )
			return '';

		$whereQuery =  '';
		$argss = [];

		foreach($where AS $field => $value)
		{
			if( $field === 0 )
			{
				$field = 'id';
			}
			else if( strpos( $field, '|' ) !== false )
			{
				$field = explode('|', $field);
				$field = $field[0];
			}

			if( !empty( $table ) && strpos( $field, '.' ) === false )
			{
				$field = $table . '.' . $field;
			}

			$symbol	= '=';

			if( is_array( $value ) && count($value) === 2 && in_array( strtoupper($value[0]), ['=', '<>', '!=', '>', '<' , '>=', '<=', 'LIKE', 'NOT LIKE'] ) )
			{
				$symbol	= $value[0];
				$value	= $value[1];

				$whereQuery .= ($whereQuery == '' ? '' : ' AND ') . $field . ' ' . $symbol . ' ' . '%s';
				$argss[] = (string)$value;
			}
			else if( is_array( $value ) && count( $value ) === 2 && in_array( strtoupper( $value[0] ), ['IS', 'IS NOT'] ) && is_null( $value[1] ) )
			{
				$symbol	= $value[0];

				$whereQuery .= ($whereQuery == '' ? '' : ' AND ') . $field . ' ' . strtoupper( $value[0] ) . ' NULL';
			}
			else if( is_array( $value ) )
			{
				$percentS = '';
				foreach( $value AS $value1 )
				{
					$percentS .= (empty($percentS) ? '' : ',') . '%s';
					$argss[] = $value1;
				}
				$whereQuery .= ($whereQuery == '' ? '' : ' AND ') . $field . ' IN (' . $percentS . ')';
			}
			else
			{
				$whereQuery .= ($whereQuery == '' ? '' : ' AND ') . $field . $symbol . '%s';
				$argss[] = (string)$value;
			}
		}

		return self::raw( $whereQuery, $argss );
	}

	private static function on( $where )
	{
		if( empty( $where ) || !is_array( $where ) )
			return '';

		$whereQuery =  '';

		foreach($where AS $value)
		{
			$field  = $value[0];
			$symbol = $value[1];
			$value  = $value[2];

			$whereQuery .= ($whereQuery == '' ? '' : ' AND ') . $field . ' ' . $symbol . ' ' . $value;
		}

		return self::raw( $whereQuery );
	}

}
