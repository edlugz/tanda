<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\TandaClient;

class Validation extends TandaClient
{
    /**
     * Till lookup
     
      	@param string countryId
      	@param string mmoId - mobile money operator id (Mpesa,AirtelMoney,TKash)
      	@param string till
		
		@return mixed
     */
    protected function till($countryId = 'KE', $mmoId, $till)
    {

        return $this->call('https://tandaio-api-uats.tanda.co.ke/registry/v1/countries/'.$countryId.'/mmos/'.$mmoId.'/merchants/'.$till.'', [], 'GET');
    }
	
    /**
     * Business number (paybill) lookup
     
      	@param string countryId
      	@param string mmoId - mobile money operator id (Mpesa,AirtelMoney,TKash)
      	@param string till
		
		@return mixed
     */
    protected function paybill($countryId = 'KE', $mmoId, $businessShortCode)
    {

        return $this->call('https://tandaio-api-uats.tanda.co.ke/registry/v1/countries/'.$countryId.'/mmos/'.$mmoId.'/businesses/'.$businessShortcode.'', [], 'GET');
    }
	
}