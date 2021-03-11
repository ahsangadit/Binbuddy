<?php

namespace BookneticApp\Integrations\PaymentGateways;

use BookneticApp\Providers\Curl;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\Helper;
use PayPal\Api\Amount;
use PayPal\Api\ChargeModel;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Agreement;
use PayPal\Api\AgreementDetails;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Common\PayPalModel;

class Paypal
{

	private $_paymentId;
	private $_price;
	private $_currency;
	private $_itemId;
	private $_itemName;
	private $_plan;
	private $_itemDescription;
	private $_apiContext;
	private $_successURL;
	private $_cancelURL;


	public function __construct(  )
	{
		$clientId		= Helper::getOption('paypal_client_id', null );
		$clientSecret	= Helper::getOption('paypal_client_secret', null );
		$mode			= Helper::getOption('paypal_mode', null ) == 'live' ? 'live' : 'sandbox';

		$this->_apiContext = new \PayPal\Rest\ApiContext(
			new \PayPal\Auth\OAuthTokenCredential( $clientId , $clientSecret )
		);

		$this->_apiContext->setConfig([ 'mode' => $mode ]);
	}

	public function setId( $paymentId )
	{
		$this->_paymentId = $paymentId;

		return $this;
	}

	public function setAmount( $price , $currency = 'USD' )
	{
		$this->_price = $price;
		$this->_currency = $currency;

		return $this;
	}

	public function setItem( $itemId , $itemName , $itemDescription )
	{
		$this->_itemId = $itemId;
		$this->_itemName = $itemName;
		$this->_itemDescription = $itemDescription;

		return $this;
	}

	public function setSuccessURL( $url )
	{
		$this->_successURL = $url;

		return $this;
	}

	public function setCancelURL( $url )
	{
		$this->_cancelURL = $url;

		return $this;
	}

	public function setPlan( $plan )
	{
		$this->_plan = ($plan == 'monthly' ? 'MONTH' : 'YEAR');

		return $this;
	}

	public function create()
	{
		$payer = new Payer();
		$payer->setPaymentMethod("paypal");

		$item1 = new Item();
		$item1->setName($this->_itemName)
			->setCurrency($this->_currency)
			->setQuantity(1)
			->setSku($this->_itemId)
			->setPrice($this->_price);


		$itemList = new ItemList();
		$itemList->setItems(array($item1));


		$details = new Details();
		$details->setShipping(0)
			->setTax(0)
			->setSubtotal($this->_price);


		$amount = new Amount();
		$amount->setCurrency($this->_currency)
			->setTotal($this->_price)
			->setDetails($details);


		$transaction = new Transaction();
		$transaction->setAmount( $amount )
			->setItemList( $itemList )
			->setDescription("Payment")
			->setInvoiceNumber('PP-' . $this->_paymentId);


		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($this->_successURL)->setCancelUrl($this->_cancelURL);

		$payment = new Payment();
		$payment->setIntent("sale")
			->setPayer($payer)
			->setRedirectUrls($redirectUrls)
			->setTransactions(array($transaction));

		try
		{
			$payment->create($this->_apiContext);

			$approvalUrl = $payment->getApprovalLink();

			return [
				'status'	=> true,
				'url'		=> $approvalUrl
			];
		}
		catch (\Exception $ex)
		{
			return [
				'status'	=> false,
				'error'		=> bkntc__('Colud\'t create a payment!')
			];
		}
	}

	public function check( $payerId , $paymentId )
	{
		$payment = Payment::get( $paymentId, $this->_apiContext );

		$execution = new PaymentExecution();
		$execution->setPayerId( $payerId );

		try
		{
			$result = $payment->execute( $execution, $this->_apiContext );

			if( $result->state == 'approved' && ( $result->transactions[0]->invoice_number == 'PP-' . $this->_paymentId ) )
			{
				return ['status' => true];
			}
			else
			{
				return ['status' => false , 'message' => 'not_approved'];
			}
		}
		catch (\PayPal\Exception\PayPalConnectionException $ex)
		{
			return [ 'status' => false ];
		}
		catch (\Exception $ex)
		{
			return [ 'status' => false ];
		}
	}


}