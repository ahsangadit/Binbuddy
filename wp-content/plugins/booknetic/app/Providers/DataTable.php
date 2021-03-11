<?php

namespace BookneticApp\Providers;

class DataTable
{

	private $query				=	'';
	private $tableName			=	'';
	private $title				=	'';
	private $addNewButton		=	'';
	private $columns			=	[];
	private $rows				=	[];
	private $actions			=	[];
	private $rowCount			=	0;
	private $currentPage		=	1;
	private $rowsPerPage		=	8;
	private $orderBy			=	'id';
	private $orderByType		=	'DESC';
	private $isAjaxRequest		=	false;
	private $exportCSV			=	false;
	private $deleteAction		=	false;
	private $getChoicesAction	=	false;
	private $deleteFn			=	false;
	private $generalSearch		=	'';
	private $searchByColumns	=	[];
	private $hideGeneralSearch	=	false;
	private $attributes			=	[];
	private $exportBtn			=	false;
	private $importBtn			=	false;
	private $pagination			=	true;
	private $isRemovable		=	true;
	private $bulkAction 		=	true;
	private $filters			=	[];

	const ROW_INDEX 		= '__ROWINDEX__';
	const DEFAULT_VIEW 		= Backend::MODULES_DIR . 'Base' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'data_table.php';


	public function __construct( $query = '' )
	{
		if( !$this->checkPermission() )
		{
			Helper::response(false, bkntc__('Permission denied!') );
		}

		if( !is_string( $query ) )
		{
			$this->setTableName( $query->getTableName() );
		}

		$this->query = is_string( $query ) ? $query : $query->toSql();

		if( Helper::_post('fs-data-table', false, 'bool') )
		{
			$this->isAjaxRequest = true;

			$this->currentPage = Helper::_post('page', '1', 'int');
			if( $this->currentPage < 1 )
				$this->currentPage = 1;

			$this->generalSearch = Helper::_post('search', '', 'string');
		}
		else if( Helper::_get('export_csv', 'false', 'string') == 'true' )
		{
			$this->exportCSV = true;
		}
		else if( Helper::_post('fs-data-table-delete', false, 'bool') )
		{
			$this->deleteAction = true;
		}
		else if( Helper::_post('action', false, 'string') === 'get_select_options' )
		{
			$this->getChoicesAction = true;
		}
	}

	private function checkPermission()
	{
		$trace = debug_backtrace();
		if ( !isset($trace[2]) )
		{
			return false;
		}

		$class = $trace[2]['class'];
		preg_match( '/BookneticApp\\\\Backend\\\(.+)\\\\/iU', $class, $module );
		$module = $module[1];

		return Permission::canAccessTo( $module );
	}

	public function isNotRemovable( )
	{
		$this->isRemovable = false;

		return $this;
	}

	public function noBulkAction( )
	{
		$this->bulkAction = false;

		return $this;
	}

	public function addAction( $id, $title )
	{
		$this->actions[] = [
			'id'    =>  $id,
			'title' =>  $title
		];

		return $this;
	}

	public function setTableName( $tableName )
	{
		$this->tableName = $tableName;

		return $this;
	}

	public function deleteFn( $fn )
	{
		$this->deleteFn = $fn;

		return $this;
	}

	public function addFilter( $columnName, $inputType = 'input', $placeholder = 'Filter', $searchType = '=', $choices = [], $colMd = 2 )
	{
		$this->filters[] = [
			'column_name'	=>	$columnName,
			'input_type'	=>	$inputType,
			'placeholder'	=>	$placeholder,
			'search_type'	=>	is_string( $searchType ) ? strtolower( $searchType ) : $searchType,
			'choices'		=>	$choices,
			'col_md'		=>	$colMd
		];

		return $this;
	}

	public function addNewBtn( $btnTitle )
	{
		$this->addNewButton = $btnTitle;

		return $this;
	}

	public function activateExportBtn( )
	{
		$this->exportBtn = true;

		return $this;
	}

	public function activateImportBtn( )
	{
		$this->importBtn = true;

		return $this;
	}

	public function disablePagination()
	{
		$this->pagination = false;

		return $this;
	}

	public function noSearch()
	{
		$this->hideGeneralSearch = true;

		return $this;
	}

	public function setQuery( $query )
	{
		$this->query = $query;

		return $this;
	}

	public function setTitle( $title )
	{
		$this->title = $title;

		return $this;
	}

	public function searchBy( $columns )
	{
		$this->searchByColumns = $columns;

		return $this;
	}

	public function addColumns( $name, $sqlColumn, $options = [], $hideInExport = false )
	{
		$options['name'] = $name;
		$options['sql_column'] = $sqlColumn;

		$standartOptions = [
			'is_sortable'	=>	true,
			'is_shown'		=>	true,
			'type'			=>	'text',
			'is_html'		=>	false
		];

		$column = array_merge($standartOptions, $options);

		if( $column['is_sortable'] && !isset($column['order_by_field']) )
		{
			if( $column['sql_column'] === static::ROW_INDEX || !is_string( $column['sql_column'] ) )
			{
				$column['is_sortable'] = false;
			}
			else
			{
				$column['order_by_field'] = $column['sql_column'];
			}
		}

		if( $hideInExport )
		{
			$column['is_shown'] = !$this->exportCSV;
		}

		$this->columns[] = $column;

		return $this;
	}

	public function addColumnsForExport( $name, $sqlColumn, $options = [] )
	{
		$options['is_shown'] = $this->exportCSV;

		return $this->addColumns( $name, $sqlColumn, $options );
	}

	public function addAttr( $attribute, $value )
	{
		if( !is_string( $attribute ) )
			return false;

		$this->attributes[ $attribute ] = $value;

		return $this;
	}

	private function queryWhere()
	{
		$where = $this->prepareFilters( );

		if( !empty( $this->generalSearch ) )
		{
			$searchColumns = [];

			$searchWord = esc_sql( $this->generalSearch );

			foreach ($this->searchByColumns AS $column)
			{
				$searchColumns[] = $column . " LIKE '%" . $searchWord . "%'";
			}

			if( !empty($searchColumns) )
				$where[] = '(' . implode(' OR ', $searchColumns) . ')';
		}



		return empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);
	}

	private function prepareFilters( )
	{
		$filters = Helper::_post('filters', [], 'arr');
		$filtersSanitized = [];

		foreach( $filters AS $filter )
		{
			if(
				isset( $filter[0] ) && is_numeric( $filter[0] ) && isset( $this->filters[ $filter[0] ] )
				&& isset( $filter[1] ) && is_string( $filter[1] )
			)
			{
				$filtersSanitized[] = [ (int)$filter[0] , (string)$filter[1] ];
			}
		}


		if( empty( $filtersSanitized ) )
		{
			return [];
		}

		$whereFilters = [];

		foreach ( $filtersSanitized AS $filter )
		{
			$filterInf	= $this->filters[ $filter[0] ];
			$filterVal	= esc_sql( $filter['1'] );

			if( !is_string( $filterInf['search_type'] ) && is_callable( $filterInf['search_type'] ) )
			{
				$whereFilters[] = $filterInf['search_type']( $filterVal );
			}
			else if( in_array( $filterInf['search_type'] , [ '=', '!=', '<>', '>', '<', '>=', '<=', 'like' ] ) )
			{
				if( $filterInf['search_type'] == 'like' )
					$filterVal = '%'.$filterVal.'%';

				$whereFilters[] = $filterInf['column_name'] . ' ' . $filterInf['search_type'] . ' "' . $filterVal . '" ';
			}
		}

		return $whereFilters;
	}

	private function queryOrderBy()
	{
		$orderBy = Helper::_post('order_by', null, 'int');
		$orderByType = Helper::_post('order_by_type', 'ASC', 'string', ['DESC']);

		if( !is_null( $orderBy ) && isset($this->columns[ $orderBy ]) && $this->columns[ $orderBy ]['is_sortable'] )
		{
			$this->orderBy		= $this->columns[ $orderBy ]['order_by_field'];
			$this->orderByType	= $orderByType;
		}

		$orderBy = '';
		foreach ( explode(',', $this->orderBy) AS $orderByCol )
		{
			if( !empty( $orderByCol ) )
			{
				$orderBy .= ( empty( $orderBy ) ? '' : ',' ) . $orderByCol . ' ' . $this->orderByType;
			}
		}

		return $orderBy;
	}

	public function render()
	{
		if( $this->deleteAction )
		{
			$this->deleteRows();
		}

		if( $this->getChoicesAction )
		{
			$this->showSelectChoices();
		}

		$where = $this->queryWhere();
		$orderBy = $this->queryOrderBy();

		$getCount = DB::DB()->get_row("SELECT COUNT(0) AS `rowcount` FROM (".$this->query.") AS `FStbl` {$where}", ARRAY_A);
		$this->rowCount = (int)$getCount['rowcount'];

		$maxPage = ceil( $this->rowCount / $this->rowsPerPage );

		if( $maxPage < $this->currentPage )
		{
			$this->currentPage = 1;
		}

		$limit		= (int)$this->rowsPerPage;
		$offset		= (int)( ($this->currentPage - 1) * $limit );

		if( $this->exportCSV )
		{
			$query = $this->query;
		}
		else
		{
			$query = "SELECT * FROM (".$this->query.") AS `FStbl` {$where} ORDER BY {$orderBy}" . ($this->pagination === false ? '' : " LIMIT {$offset}, {$limit}");
		}

		$this->rows = DB::DB()->get_results($query, ARRAY_A);

		$thead = $this->getThead();
		$tbody = $this->getTbody();

		return [
			'title'				=>	$this->title,
			'hide_search'		=>	$this->hideGeneralSearch,
			'search'			=>	$this->generalSearch,
			'is_ajax'			=>	$this->isAjaxRequest,
			'row_count'			=>	$this->rowCount,
			'current_page'		=>	$this->currentPage,
			'max_page'			=>	ceil( $this->rowCount / $this->rowsPerPage ),
			'rows_per_page'		=>	$this->rowsPerPage,
			'order_by'			=>	$this->orderBy,
			'order_by_type'		=>	$this->orderByType,
			'thead'				=>	$thead,
			'tbody'				=>	$tbody,
			'actions'			=>	$this->actions,
			'attributes'		=>	$this->attributes,
			'add_new_btn'		=>	$this->addNewButton,
			'export_btn'		=>	$this->exportBtn,
			'import_btn'		=>	$this->importBtn,
			'pagination'		=>	$this->pagination,
			'is_removable'		=>	$this->isRemovable,
			'bulk_action'		=>	$this->bulkAction,
			'filters'			=>	$this->filters
		];
	}

	private function renderCSV( $dataTable )
	{
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 01 Jul 2001 04:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download");
		header('Content-type: text/csv; charset=UTF-8');
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header('Content-Disposition: attachment;filename="' . Backend::$currentModule . '_' . Date::format('YMd') . '.csv"');
		header("Content-Transfer-Encoding: binary");

		$df = fopen("php://output", 'w');

		fputs( $df, "\xEF\xBB\xBF" );

		$head = [];

		foreach ( $dataTable['thead'] AS $column )
		{
			$head[] = htmlspecialchars( $column['name'] );
		}

		fputcsv( $df, $head );

		foreach ( $dataTable['tbody'] AS $data )
		{
			$csvTr = [];

			foreach ( $data['data'] AS $getTd )
			{
				$csvTrTd = trim( strip_tags( $getTd['content'] ) );
				$csvTrTd = preg_replace("/[\r\n]+/", ' | ', $csvTrTd);
				$csvTrTd = preg_replace('/[ \t]+/', ' ', $csvTrTd);
				$csvTr[] = $csvTrTd;
			}

			fputcsv( $df, $csvTr );
		}

		fclose($df);

		exit;
	}

	public function renderHTML( $view = null )
	{
		$dataTable = $this->render();

		if( $this->exportCSV )
		{
			$this->renderCSV( $dataTable );
		}

		$view = empty($view) ? static::DEFAULT_VIEW : $view;

		if( file_exists($view) )
		{
			ob_start();
			require $view;
			$viewOutput = ob_get_clean();
		}
		else
		{
			$viewOutput = '('.htmlspecialchars( $view ).') View not found!';
		}

		if( $this->isAjaxRequest )
		{
			Helper::response(true, [
				'html'			=> htmlspecialchars($viewOutput),
				'rows_count'	=> $this->rowCount
			]);
		}

		return $viewOutput;
	}

	private function getThead()
	{
		$thead = [];

		foreach ($this->columns as $column)
		{
			if( !$column['is_shown'] )
				continue;

			$thead[] = [
				'name'				=>	$column['name'],
				'is_sortable'		=>	$column['is_sortable'],
				'order_by_field'	=>	$column['is_sortable'] ? $column['order_by_field'] : false
			];
		}

		return $thead;
	}

	private function getTbody()
	{
		$data = [];
		$index = ($this->currentPage - 1) * $this->rowsPerPage;

		foreach ( $this->rows AS $row )
		{
			$index++;
			$newRow = [];
			foreach ( $this->columns AS $column )
			{
				if( !$column['is_shown'] )
					continue;

				if( is_callable( $column['sql_column'] ) && is_object( $column['sql_column'] ) )
				{
					$columnData = $column['sql_column']( $row );
				}
				else if( is_string( $column['sql_column'] ) && key_exists( $column['sql_column'], $row ) )
				{
					$columnData = $row[ $column['sql_column'] ];

					if( isset( $column['type'] ) )
					{
						$columnData = $this->columnTypeFilter( $columnData, $column['type'] );
					}
				}
				else if( $column['sql_column'] === static::ROW_INDEX )
				{
					$columnData = $index;
				}
				else
				{
					$columnData = '-';
				}

				if( $column['is_html'] == false )
				{
					$columnData = htmlspecialchars($columnData, ENT_QUOTES);
				}

				$attributes = [];

				if( isset( $column['attr'] ) )
				{
					$attributes = $column['attr'];
				}

				$newRow[] = [
					'content'		=>	$columnData,
					'attributes'	=>	$attributes
				];
			}

			$attributes = '';
			foreach ( $this->attributes AS $dataName => $dataValue )
			{
				if( is_callable( $dataValue ) && is_object( $dataValue ) )
				{
					$attrData = $dataValue( $row );
				}
				else
				{
					$attrData = isset( $row[ $dataValue ] ) ? $row[ $dataValue ] : '-';
				}

				$attributes .= ' data-' . htmlspecialchars( $dataName ) . '="' . htmlspecialchars( $attrData ) . '"';
			}

			$data[$row['id']] = [
				'data'			=>	$newRow,
				'attributes'	=>	$attributes,
				'is_active' 	=>	key_exists('is_active', $row) && (int)$row['is_active'] === 0 ? 0 : 1
			];
		}

		return $data;
	}

	private function columnTypeFilter( $data, $type )
	{
		switch ($type)
		{
			case 'datetime':
				return empty( $data ) ? '' : Date::dateTime( $data );
				break;
			case 'date':
				return empty( $data ) ? '' : Date::datee( $data );
				break;
			case 'time':
				return empty( $data ) ? '' : Date::time( $data );
				break;
			case 'price':
				return Helper::price( $data );
				break;
			default:
				return $data;
				break;
		}
	}

	private function deleteRows( )
	{
		if( !$this->isRemovable )
		{
			Helper::response( false, 'You can not delete this data!' );
		}

		$ids = Helper::_post('ids', [], 'array');

		$idsArr = [];

		foreach ( $ids AS $nodeId )
		{
			if( is_numeric($nodeId) && $nodeId > 0 )
			{
				$idsArr[] = (int)$nodeId;
			}
		}

		if( empty( $idsArr ) )
			Helper::response(false);

		$idsStr = '"' . implode('","', $idsArr) . '"';

		// check ids for security reasons...
		$searchIds = DB::DB()->get_results("SELECT id FROM (".$this->query.") AS `tb` WHERE id IN ({$idsStr})", ARRAY_A);
		$allowedIdsArr = [];

		foreach($searchIds AS $searchId)
		{
			$allowedIdsArr[] = (int)$searchId['id'];
		}

		if( !empty( $this->deleteFn ) && is_callable( $this->deleteFn ) )
		{
			$result = call_user_func( $this->deleteFn, $allowedIdsArr );

			if( $result === false )
			{
				Helper::response(true);
			}
		}

		$idsStrSec = '"' . implode('","', $allowedIdsArr) . '"';

		if( empty( $this->tableName ) )
		{
			Helper::response(false, bkntc__('Please set table name for deleting rows!'));
		}
		else if( !Permission::canDeleteRow( $this->tableName ) )
		{
			Helper::response(false, bkntc__('Permission denied!'));
		}

		DB::DB()->query("DELETE FROM `".DB::table($this->tableName)."` WHERE id IN ({$idsStrSec})");

		Helper::response(true);
	}

	private function showSelectChoices()
	{
		$filterId	= Helper::_post('filter_id', false, 'int');
		$filter		= Helper::_post('q', '', 'string');

		if( $filterId === false || !isset( $this->filters[ $filterId ] ) || $this->filters[ $filterId ]['input_type'] != 'select' )
		{
			Helper::response(false);
		}

		$filterData	= $this->filters[ $filterId ];

		$choices	= $filterData['choices'];

		$data = [];

		if( isset( $choices['list'] ) && is_array( $choices['list'] ) )
		{
			foreach ( $choices['list'] AS $choiceKey => $choiceVal )
			{
				if( !(empty( $filter ) || strpos( $choiceKey, $filter ) !== false || strpos( $choiceVal, $filter ) !== false) )
				{
					continue;
				}

				$data[] = [
					'id'	=>	htmlspecialchars( $choiceKey ),
					'text'	=>	htmlspecialchars( $choiceVal )
				];
			}
		}
		else
		{
			$tableName	        = $choices['table'];
			$idField	        = isset( $choices['id_field'] ) ? $choices['id_field'] : 'id';
			$nameField	        = isset( $choices['name_field'] ) ? $choices['name_field'] : 'name';
			$additionalFilter   = isset( $choices['additional_filter'] ) ? $choices['additional_filter'] : '';

			if( is_object( $additionalFilter ) && is_callable( $additionalFilter ) )
			{
				$additionalFilter = $additionalFilter();
			}

			$queryWherePart = '';
			$queryWhereParams = [];

			if( !empty( $filter ) )
			{
				$queryWherePart .= " WHERE " . $nameField . " LIKE %s";
				$queryWhereParams[] = '%' . $filter . '%';
			}

			$queryWherePart .= Permission::queryFilter( $tableName, 'id', ( empty($queryWherePart) ? 'WHERE' : 'AND' ) );

			if( !empty( $additionalFilter ) )
			{
				$queryWherePart .= ( $queryWherePart == '' ? ' WHERE ' : ' AND ' ) . $additionalFilter;
			}

			$searchResult = DB::DB()->get_results(
				DB::DB()->prepare( "SELECT " . $idField . " AS `id_as`, " . $nameField . " AS `name_as` FROM `" . DB::table( $tableName ) . "`" . $queryWherePart . ' LIMIT 0, 50', $queryWhereParams ), ARRAY_A
			);

			foreach ( $searchResult AS $result )
			{
				$data[] = [
					'id'	=>	htmlspecialchars( $result['id_as'] ),
					'text'	=>	htmlspecialchars( $result['name_as'] )
				];
			}

		}

		Helper::response(true, [ 'results' => $data ]);
	}

}