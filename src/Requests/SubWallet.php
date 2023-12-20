<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\TandaClient;
use EdLugz\Tanda\Models\TandaWallet;
use EdLugz\Tanda\Exceptions\TandaRequestException;

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
	@param array customFieldsKeyValue	
		
  	@return TandaWallet
     */
    public function create(
	    string $name, 
	    string $ipnUrl, 
	    string $username, 
	    string $password, 
	    array $customFieldsKeyValue = []
    ) : TandaWallet
    {
	$wallet = TandaWallet::create(array_merge([
		'name' => $name,
		'ipnUrl' => $ipnUrl,
		'username' => $username,
		'password' => $password
	], $customFieldsKeyValue));
	    
        $parameters = [
            "name" => $name,
			"ipnUrl" => $ipnUrl,
			"username" => $username,
			"password" => $password
        ];

	try {
		$response = $this->call($this->endPoint, ['json' => $parameters], 'POST');
	} catch(TandaRequestException $e){
		$response = [
			'status'         => $e->getCode(),
			'responseCode'   => $e->getCode(),
			'message'        => $e->getMessage(),
		];

            $response = (object) $response;
	}

	if ($response) {
            $data = [
                'wallet_account_number'  => $response->account
            ];
        }

        $wallet->update($data);

        return $wallet;

    }
	
    /**
     * Get Sub wallets
	 
		@return mixed
     */
    public function get()
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
    public function update($walletId, $name, $ipnUrl = 'https://webhook.site/cbfb845b-f92e-40be-bf56-30cf5da0a70b', $username, $password)
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
