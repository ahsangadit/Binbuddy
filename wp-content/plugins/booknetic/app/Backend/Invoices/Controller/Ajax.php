<?php

namespace BookneticApp\Backend\Invoices\Controller;

use BookneticApp\Backend\Invoices\Model\Invoice;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function save()
	{
		$id			=	Helper::_post('id', '0', 'integer');
		$name		=	Helper::_post('name', '', 'string');
		$content	=	Helper::_post('content', '', 'string');

		if( $id < 0 || empty( $name ) || empty( $content ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$sqlData = [
			'name'		=>	$name,
			'content'	=>	$content
		];

		if( $id > 0 )
		{
			Invoice::where('id', $id)->update( $sqlData );
		}
		else
		{
			Invoice::insert( $sqlData );
			$id = DB::lastInsertedId();
		}

		Helper::response(true, [
			'id'	=>	$id
		]);
	}

}
