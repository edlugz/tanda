<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Exceptions\TandaRequestException;
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
     *
     * @throws \EdLugz\Tanda\Exceptions\TandaRequestException
     */
    public function __construct()
    {
        parent::__construct();

        $this->endPoint = 'registry/v1/countries/KE/mmos/';
    }

    /**
     * Till lookup.
     *
     * @param string $mmoId - mobile money operator id (Mpesa,AirtelMoney,TKash)
     * @param string $till
     *
     * @throws \EdLugz\Tanda\Exceptions\TandaRequestException
     *
     * @return mixed
     */
    public function till(string $mmoId, string $till): mixed
    {
        return $this->call($this->endPoint.$mmoId.'/merchants/'.$till, [], 'GET');
    }

    /**
     * Business number (paybill) lookup.
     *
     * @param string $mmoId
     * @param string $businessShortCode
     *
     * @throws TandaRequestException
     *
     * @return mixed
     */
    public function paybill(string $mmoId, string $businessShortCode): mixed
    {
        return $this->call($this->endPoint.$mmoId.'/businesses/'.$businessShortCode, [], 'GET');
    }
}
