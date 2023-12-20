<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\TandaClient;

class Validation extends TandaClient
{
	/**
     * till check end point on Tanda API.
     *
     * @var string
     */
    protected string $endPoint;
	
	/**
     * Utility constructor.
     */
	public function __construct()
    {		
		$this->endPoint = 'registry/v1/countries/KE/mmos/';		
    }
	
    /**
     * Till lookup
   
      	@param string mmoId - mobile money operator id (Mpesa,AirtelMoney,TKash)
      	@param string till
		
		@return mixed
     */
    public function till($mmoId, $till)
    {
        return $this->call($this->endPoint.''.$mmoId.'/merchants/'.$till.'', [], 'GET');
    }
	
    /**
     * Business number (paybill) lookup
     
      	@param string mmoId - mobile money operator id (Mpesa,AirtelMoney,TKash)
      	@param string till
		
		@return mixed
     */
    public function paybill($mmoId, $businessShortCode)
    {
        return $this->call($this->endPoint.''.$mmoId.'/businesses/'.$businessShortcode.'', [], 'GET');
    }
	
}