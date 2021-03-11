<?php

namespace BookneticApp\Integrations\PaymentGateways;

use BookneticApp\Providers\Helper;
use Stripe\Exception\ApiErrorException;

class Stripe
{

	private $_paymentId;
	private $_price;
	private $_currency;
	private $_itemName;
	private $_itemImage;
	private $_successURL;
	private $_cancelURL;


	public function __construct()
	{
		\Stripe\Stripe::setApiKey( Helper::getOption('stripe_client_secret') );
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

	public function setItem( $itemName , $itemImage )
	{
		$this->_itemName = $itemName;
		$this->_itemImage = $itemImage;

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

	public function create()
	{
		try
		{
			$checkout_session = \Stripe\Checkout\Session::create([
				'payment_method_types' => ['card'],
				'line_items' => [
					[
						'price_data' => [
							'currency' => $this->_currency,
							'unit_amount' => $this->normalizePrice($this->_price, $this->_currency),
							'product_data' => [
								'name' => $this->_itemName,
								'images' => [$this->_itemImage],
							],
						],
						'quantity' => 1
					]
				],
				'mode' => 'payment',
				'success_url' => $this->_successURL,
				'cancel_url' => $this->_cancelURL,
				"metadata" => ['appointment_id' => $this->_paymentId]
			]);
		}
		catch (ApiErrorException $e)
		{
			return 0;
		}

		return $checkout_session->id;
	}

	public function check( $sessionId )
	{
		try
		{
			$sessionInf = \Stripe\Checkout\Session::retrieve($sessionId);
		}
		catch (ApiErrorException $e)
		{
			return false;
		}

		return (isset($sessionInf->payment_status) && $sessionInf->payment_status == 'paid' && isset($sessionInf->metadata) && isset($sessionInf->metadata->appointment_id) && $this->_paymentId == $sessionInf->metadata->appointment_id);
	}

	private function normalizePrice ( $price, $currency )
	{
		$zeroDecimalCurrencies = [ 'BIF', 'DJF', 'JPY', 'KRW', 'PYG', 'VND', 'XAF', 'XPF', 'CLP', 'GNF', 'KMF', 'MGA', 'RWF', 'VUV', 'XOF' ];

		if ( in_array( $currency, $zeroDecimalCurrencies ) )
		{
			return $price;
		}
		else
		{
			return round( $price * 100 );
		}
	}


}