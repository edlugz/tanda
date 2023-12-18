<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\TandaClient;

class SubWallet extends TandaClient
{
    /**
     * Create sub wallet end point on Tanda API.
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
     * SubWallet constructor.
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->orgId = config('tanda.organisation_id'); 
		
		$this->endPoint = 'wallets/v1/resellers/'.$this->orgId.'/wallets';

		
    }

    /**
     * Create a new Sub wallet
     
      	@param string name
      	@param string ipnUrl
      	@param string username
      	@param string password
		
		@return mixed
     */
    protected function create($name, $ipnUrl, $username, $password)
    {
        $parameters = [
            "name" => $name,
			"ipnUrl" => $ipnUrl,
			"username" => $username,
			"password" => $password
        ];

        return $this->call($this->endPoint, ['json' => $parameters], 'POST');
    }
	
    /**
     * Get Sub wallets
	 
		@return mixed
     */
    protected function get()
    {
        return $this->call($this->endPoint, [], 'GET');
    }
	
    /**
     * Update a Sub wallet
		
		@param string walletId
		@param string name
      	@param string ipnUrl
      	@param string username
      	@param string password
		
		@return mixed
     */
    protected function update($walletId, $name, $ipnUrl = 'https://webhook.site/cbfb845b-f92e-40be-bf56-30cf5da0a70b', $username, $password)
    {
		$parameters = [
            "name" => $name,
			"ipnUrl" => $ipnUrl,
			"username" => $username,
			"password" => $password
        ];
		
        return $this->call($this->endPoint.'/'.$walletId, ['json' => $parameters], 'PATCH');
    }
	
}