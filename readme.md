# Tanda

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

```bash
composer require edlugz/tanda
```

## Publish Configuration File

```bash
php artisan vendor:publish --provider="EdLugz\Tanda\TandaServiceProvider" --tag="migrations"
```

Fill in all the details you will be requiring for your application. Here are the env variables for quick copy paste.

```bash
TANDA_RESULT_URL
TANDA_CLIENT_ID
TANDA_CLIENT_SECRET
TANDA_ORG_ID
TANDA_BASE_URL=
```

## Usage

Using the facade

Sub Wallet
```bash
Tanda::subwallet()->create($name, $ipnUrl, $username, $password, $customFieldsKeyValue = []);
Tanda::subwallet()->get();
Tanda::subwallet()->update($walletId, $name, $username, $password, $ipnUrl);
```

C2B - Fund Wallet (send stk push to mobile number)
```bash
Tanda::C2B()->request(string $serviceProviderId, $merchantWallet, $mobileNumber, $amount, $customFieldsKeyValue = []);
```

P2P -  send to internal wallets
```bash
Tanda::P2P()->send($senderWallet, $receiverWallet, $amount, $customFieldsKeyValue = []);
```

B2C -  send to bank accounts
```bash
Tanda::B2C()->bank($merchantWallet, $bankCode, $amount, $accountNumber, $accountName, $narration, $customFieldsKeyValue = []);
Tanda::B2C()->mobile($merchantWallet, $serviceProviderId, $amount, $mobileNumber, $customFieldsKeyValue = []);
```

B2B - to paybills and till numbers
```bash
Tanda::B2B()->buygoods($merchantWallet, $amount, $till, $contact, $customFieldsKeyValue = []);
Tanda::B2B()->paybill($merchantWallet, $amount, $paybill, $accountNumber, $contact, $customFieldsKeyValue = []);
```

Airtime - prepaid airtime (pinless topup)
```bash
Tanda::airtime()->prepaid($serviceProviderId, $amount, $mobileNumber, $customFieldsKeyValue = []);
```

Utility - kplc, nairobi water and paid tv
```bash
Tanda::utility()->postpaid($serviceProviderId, $amount, $accountNumber, $customFieldsKeyValue = []);
Tanda::utility()->prepaid($amount, $accountNumber, $contact, $customFieldsKeyValue = []);
Tanda::utility()->tv($serviceProviderId, $amount, $accountNumber, $customFieldsKeyValue = []);
```

Transaction - check status
```bash
Tanda::transaction()->status($transactionId);
```

Helper functions - get mno network based on mobile number
```bash
Tanda::helper()->serviceProvider($mobileNumber);
```

Helper functions - receive results
```bash
Tanda::helper()->result($data);
```


## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email eddy.lugaye@gmail.com instead of using the issue tracker.

## Credits

- [Eddy Lugaye][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/edlugz/tanda.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/edlugz/tanda.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/edlugz/tanda/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/edlugz/tanda
[link-downloads]: https://packagist.org/packages/edlugz/tanda
[link-travis]: https://travis-ci.org/edlugz/tanda
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/edlugz
[link-contributors]: ../../contributors
