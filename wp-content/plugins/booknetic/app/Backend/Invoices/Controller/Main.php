<?php

namespace BookneticApp\Backend\Invoices\Controller;

use BookneticApp\Backend\Invoices\Model\Invoice;
use BookneticApp\Providers\Controller;
use BookneticApp\Providers\DataTable;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class Main extends Controller
{

	public function index()
	{
		$dataTable = new DataTable( "SELECT * FROM `" . DB::table('invoices') . "` tb1" . DB::tenantFilter('WHERE') );

		$dataTable->setTableName('invoices');
		$dataTable->setTitle(bkntc__('Invoices'));
		$dataTable->addNewBtn(bkntc__('ADD INVOICE'));

		$dataTable->searchBy(['name']);

		$dataTable->addColumns(bkntc__('№'), DataTable::ROW_INDEX);
		$dataTable->addColumns(bkntc__('NAME'), 'name');

		$table = $dataTable->renderHTML();

		$this->view( 'index', ['table' => $table] );
	}

	public function edit()
	{
		$invoiceId = Helper::_get('invoice_id', null, 'int');

		if( $invoiceId > 0 )
		{
			$invoiceInf = Invoice::get( $invoiceId );
		}
		else
		{
			$invoiceInf	= [
				'id'            =>  0,
				'name'          =>  '',
				'content'   	=>  ''
			];
		}

		$this->view( 'edit_invoice', [
			'id'		=>	$invoiceId,
			'info'		=>	$invoiceInf
		] );
	}

	public function download()
	{
		$invoiceId = Helper::_get('invoice_id', null, 'int');

		$invoiceInf = Invoice::get( $invoiceId );
		if( !$invoiceInf )
		{
			Helper::response( false );
		}

		$mpdf = new Mpdf();
		$mpdf->WriteHTML( $invoiceInf->content );
		$mpdf->Output( $invoiceInf->name . '.pdf', Destination::DOWNLOAD );
		exit();
	}

}
