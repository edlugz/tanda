<?php

namespace EdLugz\Tanda;

use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\Helpers\TandaHelper;
use EdLugz\Tanda\Requests\Airtime;
use EdLugz\Tanda\Requests\B2B;
use EdLugz\Tanda\Requests\B2C;
use EdLugz\Tanda\Requests\C2B;
use EdLugz\Tanda\Requests\P2P;
use EdLugz\Tanda\Requests\SubWallet;
use EdLugz\Tanda\Requests\Transaction;
use EdLugz\Tanda\Requests\Utility;
use EdLugz\Tanda\Requests\Validation;

class Tanda
{
    /**
     * Sub Wallet functions.
     *
     * @return SubWallet
     */
    public function subwallet(): SubWallet
    {
        return new SubWallet();
    }

    /**
     * Validation functions.
     *
     * @return Validation
     */
    public function validation(): Validation
    {
        return new Validation();
    }

    /**
     * P2P functions.
     *
     * @return P2P
     */
    public function p2p(): P2P
    {
        return new P2P();
    }

    /**
     *    C2B functions.
     *
     * @return C2B
     */
    public function c2b(): C2B
    {
        return new C2B();
    }

    /**
     *    B2C functions.
     *
     * @param string $resultUrl
     * @return B2C
     * @throws TandaRequestException
     */
    public function b2c(string $resultUrl): B2C
    {
        return new B2C($resultUrl);
    }

    /**
     *    B2B functions.
     *
     * @param string $resultURl
     * @return B2B
     * @throws TandaRequestException
     */
    public function b2b(string $resultURl): B2B
    {
        return new B2B($resultURl);
    }

    /**
     *    Airtime functions.
     *
     * @param string $resultUrl
     * @return Airtime
     * @throws TandaRequestException
     */
    public function airtime(string $resultUrl): Airtime
    {
        return new Airtime($resultUrl);
    }

    /**
     *    Utility functions.
     *
     * @param string $resultUrl
     * @return Utility
     * @throws TandaRequestException
     */
    public function utility(string $resultUrl): Utility
    {
        return new Utility($resultUrl);
    }

    /**
     * 	Transaction functions.
     *
     * @return Transaction
     */
    public function transaction(): Transaction
    {
        return new Transaction();
    }

    /**
     * 	Helper functions.
     *
     * @return TandaHelper
     */
    public function helper(): TandaHelper
    {
        return new TandaHelper();
    }
}
