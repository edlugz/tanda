<?php

namespace EdLugz\Tanda;

use EdLugz\Tanda\Requests\SubWallet;
use EdLugz\Tanda\Requests\Validation;
use EdLugz\Tanda\Requests\P2P;
use EdLugz\Tanda\Requests\C2B;
use EdLugz\Tanda\Requests\B2C;
use EdLugz\Tanda\Requests\B2B;
use EdLugz\Tanda\Requests\Airtime;
use EdLugz\Tanda\Requests\Utility;
use EdLugz\Tanda\Requests\Transaction;
use EdLugz\Tanda\Helpers\TandaHelper;

class Tanda
{
    /**
     * Sub Wallet functions
     *
     * @return SubWallet
     */
    public function subwallet() : SubWallet
    {
        return new SubWallet();
    }
	
    /**
     * Validation functions
     *
     * @return Validation
     */
    public function validation() : Validation
    {
        return new Validation();
    }
	
    /**
     * P2P functions
     *
     * @return P2P
     */
    public function p2p() : P2P
    {
        return new P2P();
    }
	
    /**
     * 	C2B functions
     *
     * @return C2B
     */
    public function c2b() : C2B
    {
        return new C2B();
    }
	
    /**
     * 	B2C functions
     *
     * @return B2C
     */
    public function b2c() : B2C
    {
        return new B2C();
    }
	
    /**
     * 	B2B functions
     *
     * @return B2B
     */
    public function b2b() : B2B
    {
        return new B2B();
    }
	
    /**
     * 	Airtime functions
     *
     * @return Airtime
     */
    public function airtime() : Airtime
    {
        return new Airtime();
    }
	
    /**
     * 	Utility functions
     *
     * @return Utility
     */
    public function utility() : Utility
    {
        return new Utility();
    }
	
    /**
     * 	Transaction functions
     *
     * @return Transaction
     */
    public function transaction() : Transaction
    {
        return new Transaction();
    }
	
    /**
     * 	Helper functions
     *
     * @return TandaHelper
     */
    public function helper() : TandaHelper
    {
        return new TandaHelper();
    }
	
}