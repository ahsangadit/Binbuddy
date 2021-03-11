<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

if( !$dataTable['is_ajax'] )
{
?>

<div class="m_header clearfix">
	<div class="m_head_title float-left"><?php print  $dataTable['title'] ?> <span class="badge badge-warning row_count"><?php print  $dataTable['row_count'] ?></span></div>
	<div class="m_head_actions float-right">

		<?php
		if( $dataTable['export_btn'] )
		{
			print '<button type="button" class="btn btn-outline-secondary btn-lg export_csv"><i class="fa fa-upload"></i> ' . bkntc__('EXPORT TO CSV') . '</button>';
		}

		if( $dataTable['import_btn'] )
		{
			print '<button type="button" class="btn btn-outline-secondary btn-lg" id="importBtn"><i class="fa fa-download"></i> ' . bkntc__('IMPORT') . '</button>';
		}

		if( !empty( $dataTable['add_new_btn'] ) )
		{
			print '<button type="button" class="btn btn-primary btn-lg" id="addBtn"><i class="fa fa-plus"></i> ' . $dataTable['add_new_btn'] . '</button>';
		}

		?>

	</div>
</div>

<?php
if( !$dataTable['hide_search'] )
{
	$getColMdForSearchPanel = 12;
	foreach ( $dataTable['filters'] AS $filterId => $filter )
	{
		$getColMdForSearchPanel -= (int)$filter['col_md'];
	}
?>
<div class="data_table_search_panel">
	<div class="row m-0 p-0">
		<div class="col-md-<?php print $getColMdForSearchPanel?> m-0 p-0">
			<div class="input-icon">
				<i><img src="<?php print Helper::icon('search.svg')?>"></i>
				<input type="text" class="form-control form-control-lg search_input" placeholder="<?php print bkntc__('Quick search')?>" value="<?php print  htmlspecialchars($dataTable['search']) ?>">
			</div>
		</div>
		<?php

		foreach ( $dataTable['filters'] AS $filterId => $filter )
		{
			?>
			<div class="col-md-<?php print $filter['col_md']?> m-0 p-0">
				<?php
				if( $filter['input_type'] == 'select' )
				{
					?>
					<select class="form-control" data-filter-id="<?php print (int)$filterId?>" data-placeholder="<?php print esc_html( $filter['placeholder'] )?>">

					</select>
					<?php
				}
				else if( $filter['input_type'] == 'date' )
				{
					?>
					<div class="position-relative">
						<input type="text" data-type="date" class="form-control" data-filter-id="<?php print (int)$filterId?>" placeholder="<?php print esc_html( $filter['placeholder'] )?>">
						<span class="datepicker_clear_btn">×</span>
					</div>
					<?php
				}
				else if( $filter['input_type'] == 'input' )
				{
					?>
					<input type="text" class="form-control" data-filter-id="<?php print (int)$filterId?>" placeholder="<?php print esc_html( $filter['placeholder'] )?>">
					<?php
				}
				?>

			</div>
			<?php
		}

		?>


	</div>

	<hr>
</div>
<?php
}
?>
<div class="m_content pt-0" id="fs_data_table_div">
	<?php
	}
	?>
	<div class="fs_data_table_wrapper">
		<table class="fs_data_table elegant_table">
			<thead>
			<tr>
				<th>
					<input type="checkbox" class="select_data_all_checkbox<?php print $dataTable['bulk_action'] ? '' : ' hidden'?>" id="checkbox_select_all">
				</th>

				<?php

				foreach( $dataTable['thead'] AS $columnKey => $column )
				{
					$addClass = [];
					$addAttr = '';

					if( $column['is_sortable'] )
					{
						$addClass[] = 'is_sortable';

						if( $column['order_by_field'] == $dataTable['order_by'] )
						{
							$addClass[] = 'active_order_field';
							$addAttr .= ' data-order-type="' . ($dataTable['order_by_type'] == 'ASC' ? 'ASC' : 'DESC') . '"';
						}
					}

					$addClass = empty( $addClass ) ? '' : ' class="' . implode(' ', $addClass) . '"';

					print '<th data-column="' . (int)$columnKey . '"' . $addClass . $addAttr . '>' . htmlspecialchars( $column['name'] ) . '</th>';
				}

				?>
				<th class="text-right">
					<button type="button" class="btn btn-xs btn-light prev_page"><i class="fa fa-caret-left"></i></button>
					<button type="button" class="btn btn-xs btn-light next_page"><i class="fa fa-caret-right"></i></button>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $dataTable['tbody'] AS $rid => $rows )
			{
				$attributes = '';
				foreach ( $dataTable['attributes'] AS $dataName => $dataValues )
				{
					$attributes .= ' data-' . $dataName . '="' . $dataValues . '"';
				}

				?>
				<tr data-id="<?php print $rid?>"<?php print $rows['attributes'] . ( !$rows['is_active'] ? ' data-disabled="true"' : '' )?>>
					<td><input type="checkbox" id="checkbox_select_all<?php print $rid?>" class="select_data_checkbox <?php print $dataTable['bulk_action'] ? '' : ' hidden'?>"></td>

					<?php
					foreach ( $rows['data'] AS $data )
					{
						$attributes = '';
						foreach ( $data['attributes'] AS $dataName => $dataValues )
						{
							$attributes .= ' data-' . $dataName . '="' . $dataValues . '"';
						}

						print '<td'.$attributes.'>' . $data['content'] . '</td>';
					}
					?>

					<td class="text-right">
						<i class="fa fa-eye-slash row_is_disabled"></i>
						<span class="actions_btn" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false"><i class="fa fa-ellipsis-h"></i></span>
						<div class="dropdown-menu dropdown-menu-right row-actions-area">
							<?php foreach ( $dataTable['actions'] AS $action ):?>
								<button class="dropdown-item <?php print esc_html($action['id'])?>_action_btn" type="button"><?php print $action['title']?></button>
							<?php endforeach;?>
							<button class="dropdown-item info_action_btn hidden" type="button"><?php print bkntc__('Info')?></button>
							<button class="dropdown-item edit_action_btn" type="button"><?php print bkntc__('Edit')?></button>
							<button class="dropdown-item delete_action_btn" type="button"><?php print bkntc__('Delete')?></button>
						</div>
					</td>
				</tr>
				<?php
			}
			if( empty( $dataTable['tbody'] ) )
			{
				print '<tr><td colspan="100%" class="pl-4 text-secondary">' . bkntc__('No any data!') . '</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>

	<div class="row mt-4">
		<div class="col-md-12">
			<span class="text-secondary mr-2 font-size-14"><?php print bkntc__('Showing %d of %d total', [ count( $dataTable['tbody'] ) , $dataTable['row_count'] ])?></span>
			<?php

			if( $dataTable['pagination'] !== false )
			{
				if( $dataTable['max_page'] <= 7 )
				{
					$startPage = 2;
					$endPage = $dataTable['max_page'] - 1;
				}
				else
				{
					$startPage = $dataTable['current_page'] - 2;
					$endPage = $startPage + 4;

					if( $startPage < 2 )
					{
						$endPage += 2 - $startPage;
						$startPage = 2;
					}

					if( $endPage > $dataTable['max_page'] - 1 )
					{
						$startPage -= 1 - ( $dataTable['max_page'] - $endPage );
						$endPage = $dataTable['max_page'] - 1;
					}
				}

				print '<span class="page_class badge' . (1==$dataTable['current_page'] ? ' active_page badge-default' : '') . '">1</span>' . ($startPage > 2 ? ' ... ' : '');

				for($page = $startPage; $page <= $endPage; $page++)
				{
					print '<span class="page_class badge' . ($page==$dataTable['current_page'] ? ' active_page badge-default' : '') . '">' . $page . '</span>';
				}

				if( $dataTable['max_page'] >= 2 )
				{
					print ($dataTable['max_page']-1 > $endPage ? ' ... ' : '') . '<span class="page_class badge' . ($dataTable['max_page']==$dataTable['current_page'] ? ' active_page badge-default' : '') . '">' . $dataTable['max_page'] . '</span>';
				}
			}

			?>

			<a href="<?php print esc_html( Helper::getOption('documentation_url', 'https://www.booknetic.com/documentation/', false) )?>" class="need_help_btn" target="_blank"><i class="far fa-question-circle"></i> <?php print bkntc__('Need Help?')?></a>
		</div>
	</div>
	<?php
	if( !$dataTable['is_ajax'] )
	{
	?>
</div>

<div class="m_bottom_fixed hidden">
	<div class="position-absolute"><?php print bkntc__('Selected')?> <span class="badge badge-success ml-2 selected_count">2</span></div>

	<?php
	if( $dataTable['is_removable'] )
	{
		print '<button type="button" data-toggle="tooltip" data-placement="top" title="Delete" class="btn btn-danger m-2 delete_btn"><i class="far fa-trash-alt"></i></button>';
	}
	?>

</div>
<?php
}
?>