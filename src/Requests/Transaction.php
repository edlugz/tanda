<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\TandaClient;

class Transaction extends TandaClient
{
	/**
     * send p2p request end point on Tanda API.
     *
     * @var string
     */
    protected $endPoint;
	
	/**
     * The organisation ID assigned for the application on Tanda API.
     *
     * @var string
     */
    protected $orgId;
	
	/**
     * P2P constructor.
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->orgId = config('tanda.organisation_id'); 
		
    }
	
    /**
     * Transaction status query
     
      	@param string reference
		
		@return mixed
     */
    public function status($reference)
    {
        return $this->call('https://tandaio-api-uats.tanda.co.ke/io/v2/organizations/'.$this->orgId.'/requests/'.$reference.'', [], 'GET');
    }
	
}