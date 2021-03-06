# Stack Pay™ - Payments SDK for PHP

[![Total Downloads](https://poser.pugx.org/stack-pay/payments-sdk-php/downloads.svg)](https://packagist.org/packages/stack-pay/payments-sdk-php)
[![Latest Stable Version](https://poser.pugx.org/stack-pay/payments-sdk-php/v/stable.svg)](https://packagist.org/packages/stack-pay/payments-sdk-php)
[![License](https://poser.pugx.org/stack-pay/payments-sdk-php/license.svg)](https://packagist.org/packages/stack-pay/payments-sdk-php)

The Stack Pay Payments SDK for PHP is an open source library through which your
PHP application can easily interact with the
[Stack Pay API](https://developer.mystackpay.com/docs).

**Note:** This release utilizes Stack Pay API v1. There are substantial
differences between this version of the client library and subsequent versions.
Please be mindful of this when upgrading.

## Requirements

PHP 5.4.0 (or higher)

## Dependencies

### PHP Curl Class 7.2.0 (or higher)

This library also requires `'ext-curl': '*'`.

## Installation

### Composer (recommended)

It is strongly recommended that you use [Composer](http://getcomposer.org) to
install this package and its dependencies. Some methods utilize `GuzzleHttp`. If you do not install via Composer, these methods will be difficult to use.

To install via Composer, run the following command:

```bash
composer require stack-pay/payments-sdk-php
```

You can also manually add this dependency to your `composer.json` file:

```json
{
    "require": {
        "stack-pay/payments-sdk-php": "~1.0.0"
    }
}
```

To use the bindings, use Composer's
[autoload](https://getcomposer.org/doc/00-intro.md#autoloading):

```php
require_once('vendor/autoload.php');
```

### Manual Installation (not recommended)

If you do not wish to use Composer, you can download the
[latest release](https://github.com/stack-pay/payments-sdk-php/releases). Then,
to use the bindings, include the `payments-sdk.php` file.

```php
require_once('/path/to/stack-pay/payments-sdk-php/lib/payments-sdk.php');
```

You will also need to download the dependencies and manually include them, which can be extremely cumbersome. It is **strongly** recommended that you use Composer.

## Instantiating the SDK

```php
$stackpay = new StackPay\Payments\StackPay($yourPublicKey, $yourPrivateKey);
```

This will create a StackPay class instance in *PRODUCTION* mode with *USD* as the default currency.

To enable development/testing mode, you should then use:

```php
$stackpay->enableTestMode();
```

To change currency:

```php
$stackpay->setCurrency('CAD');
```

## Basic Structures

The basic structures used in the SDK.

#### Merchant

```php
$merchant = new StackPay\Payments\Structures\Merchant(
    $merchantId,
    $merchantHashKey
);

// or

$merchant = (new StackPay\Payments\Structures\Merchant())
    ->setID($merchantId)
    ->setHashKey($merchantHashKey);
```

#### Account

##### Card Account

```php
$cardAccount = new StackPay\Payments\Structures\CardAccount(
    $type, // StackPay\Payments\AccountTypes::AMEX, DISCOVER, MASTERCARD, VISA
    $accountNumber,
    $mmddExpirationDate,
    $cvv2,
    $savePaymentMethodBoolean
);

// or

$cardAccount = (new StackPay\Payments\Structures\Account())
    ->setSavePaymentMethod($trueOrFalse)
    ->setType(StackPay\Payments\AccountTypes::VISA) // MASTERCARD, DISCOVER, AMEX
    ->setNumber($accountNumber)
    ->setExpireDate($mmddExpirationDate)
    ->setCvv2($cvv2);
```

##### Bank Account

```php
$bankAccount = new StackPay\Payments\Structures\BankAccount(
    $type, // StackPay\Payments\AccountTypes::CHECKING, SAVINGS
    $accountNumber,
    $routingNumber,
    $savePaymentMethodBoolean
);

// or

$bankAccount = (new StackPay\Payments\Structures\Account())
    ->setSavePaymentMethod($trueOrFalse)
    ->setType(StackPay\Payments\AccountTypes::CHECKING) // SAVINGS
    ->setNumber($accountNumber)
    ->setRoutingNumber($routingNumber);
```

#### Account Holder

```php
$accountHolder = new StackPay\Payments\Structures\AccountHolder(
    $accountHolderName,
    $billingAddress
);

// or

$accountHolder = (new StackPay\Payments\Structures\AccountHolder())
    ->setName($accountHolderName)
    ->setBillingAddress($billingAddress);
```

#### Address

```php
$address = new StackPay\Payments\Structures\Address(
    $addressLine1,
    $addressLine2,
    $city,
    $state,
    $postalCode,
    StackPay\Payments\Structures\Country::usa() // canada()
);

// or

$address = (new StackPay\Payments\Structures\Address())
    ->setAddress1($addressLine1)
    ->setAddress2($addressLine2)
    ->setCity($city)
    ->setState($stateAbbreviation)
    ->setPostalCode($postalCode)
    ->setCountry(StackPay\Payments\Structures\Country::usa());

// or

$address = (new StackPay\Payments\Structures\Address())
    ->setAddressLines("$addressLine1.$lineSeparator.$addressLine2", $lineSeparator)
    ->setCity($city)
    ->setState($stateAbbreviation)
    ->setPostalCode($zipCode)
    ->setCountry(StackPay\Payments\Structures\Country::usa());
```

#### Existing Customer

```php
$customer = new StackPay\Payments\Structures\Customer($customerId);

// or

$customer = (new StackPay\Payments\Structures\Customer())
    ->setId($customerId);
```

#### Existing Transaction (for use with Refunds and Voids)

```php
$transaction = new StackPay\Payments\Structures\Transaction($transactionId);

// or

$transaction = (new StackPay\Payments\Structures\Transaction())
    ->setId($transactionId);
```

#### Transaction Split

```php
$split = new StackPay\Payments\Structures\Split(
    $merchant,
    $amountInCents
);

// or

$split = (new StackPay\Payments\Structures\Split())
    ->setMerchant($merchant)
    ->setAmount($amountInCents);
```

## Main SDK Transaction Methods (scheduled for deprecation)

The following is a sampling of direct SDK transaction method signatures. It is not exhaustive.

These methods build the request, send it, and process the response.

This method of SDK usage is scheduled for deprecation. It will eventually be replaced with a process similar to what is shown in the Scheduled Transactions section below.

### Sale

#### via stored Payment Method

```php
$processedTransaction = $StackPay->saleWithPaymentMethod(
    Structures\PaymentMethod $paymentMethod,
    Structures\Merchant $merchant,
    $amount,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

#### via Token

```php
$processedTransaction = $StackPay->saleWithToken(
    Structures\Token $token,
    Structures\Merchant $merchant,
    $amount,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

#### via Account Details

```php
$processedTransaction = $StackPay->saleWithAccountDetails(
    Structures\Account $account,
    Structures\AccountHolder $accountHolder,
    Structures\Merchant $merchant,
    $amount,
    Structures\Customer $customer = null,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

#### via MasterPass

```php
$processedTransaction = $StackPay->saleWithMasterPass(
    $masterPassTransactionId,
    Structures\Merchant $merchant,
    $amount,
    Structures\Customer $customer = null,
    Structures\Split $split = null,
    $idempotencyKey = null,
    $currency = null
);
```

## Transaction Factories (scheduled for deprecation)

Generate request objects using factory classes and process them.

This method of SDK usage is scheduled for deprecation. It will eventually be replaced with a process similar to what is shown in the Scheduled Transactions section below.

Prior to calling `processTransaction()`, you are able to add optional fields to your request objects:

```php
$saleRequest
    ->setInvoiceNumber('invoice_874196')
    ->setExternalId('transaction_174968')
    ->setComment1('Final payment on Invoice #874196')
    ->setComment2('Collected by Joe Salesman')
;
```

### Sale

#### with Account Details

```php
$saleRequest = StackPay\Payments\Factories\Sale::withAccountDetails(
    StackPay\Payments\Structures\Account $account,
    StackPay\Payments\Structures\AccountHolder $accountHolder,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Customer $optionalCustomer = null,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$sale = $stackpay->processTransaction(
    $saleRequest,
    $optionalIdempotencyKey
);
```

#### with Payment Method

```php
$saleRequest = StackPay\Payments\Factories\Sale::withPaymentMethod(
    StackPay\Payments\Structures\PaymentMethod $paymentMethod,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$sale = $stackpay->processTransaction(
    $saleRequest,
    $optionalIdempotencyKey
);
```

### Auth

#### with Account Details

```php
$authRequest = StackPay\Payments\Factories\Auth::withAccountDetails(
    StackPay\Payments\Structures\Account $account,
    StackPay\Payments\Structures\AccountHolder $accountHolder,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Customer $optionalCustomer = null,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$auth = $stackpay->processTransaction(
    $authRequest,
    $optionalIdempotencyKey
);
```

#### with Payment Method

```php
$authRequest = StackPay\Payments\Factories\Auth::withPaymentMethod(
    StackPay\Payments\Structures\PaymentMethod $paymentMethod,
    StackPay\Payments\Structures\Merchant $merchant,
    $amountInCents,
    StackPay\Payments\Structures\Split $optionalSplit = null,
    StackPay\Payments\Structures\Currency $optionalCurrencyOverride
);

$auth = $stackpay->processTransaction(
    $authRequest,
    $optionalIdempotencyKey
);
```

### Capture

```php
$captureRequest = StackPay\Payments\Factories\Capture::previousTransaction(
    StackPay\Payments\Structures\Transaction $previousTransaction,
    $amountInCents,
    StackPay\Payments\Structures\Split $optionalSplit = null, // used for setting splitAmount, splitMerchant can not be changed
);

$capture = $stackpay->processTransaction(
    $captureRequest,
    $optionalIdempotencyKey
);
```

### Void

```php
$voidRequest = StackPay\Payments\Factories\VoidTransaction::previousTransaction(
    StackPay\Payments\Structures\Transaction $previousTransaction
);

$void = $stackpay->processTransaction(
    $voidRequest,
    $optionalIdempotencyKey
);
```

### Refund

```php
$refundRequest = StackPay\Payments\Factories\Refund::previousTransaction(
    StackPay\Payments\Structures\Transaction $previousSaleOrCaptureTransaction,
    $amountInCents
);

$refund = $stackpay->processTransaction(
    $refundRequest,
    $optionalIdempotencyKey
);
```

## Request-Focused Implementation

The following examples are recommended when the SDK is installed via Composer. These methods use `GuzzleHttp` which is very difficult to use without a good autoloader.

You can directly interact with the response returned by these methods using the `->body()` method, which is the JSON-decoded `Body` element of the response payload as a `stdClass` PHP object.

```php
$response = $request->send();

echo $response->body()->ID;
```

You can check the response for success using method `success()`. If `success()` returns `false`, then you can use `error()` to access `code`, `messages`, and `errors` attributes.

```php
$response = $request->send();

if (! $response->success()) {
    echo $response->error()->code."\n"; // the API response error code
    echo $response->error()->message."\n"; // the API response error message
    print_r($response->error()->errors); // populated when the request body does not pass validation
}
```

### Payment Methods

#### Create a stored Payment Method

```php
$account                        = new \StackPay\Payments\Structures\Account;
$account->type                  = \StackPay\Payments\AccountTypes::VISA;
$account->accountNumber         = '4111111111111111';
$account->expirationMonth       = '12';
$account->expirationYear        = '25';
$account->cvv2                  = '999';

$billingAddress                 = new \StackPay\Payments\Structures\Address;
$billingAddress->address1       = '5360 Legacy Drive #150';
$billingAddress->city           = 'Plano';
$billingAddress->state          = 'TX';
$billingAddress->postalCode     = '75024';
$billingAddress->country        = Structures\Country::usa();

$accountHolder                  = new \StackPay\Payments\Structures\AccountHolder;
$accountHolder->name            = 'Stack Testerman';
$accountHolder->billingAddress  = $billingAddress;

$paymentMethod                  = new Structures\PaymentMethod;
$paymentMethod->account         = $account;
$paymentMethod->accountHolder   = $accountHolder;

$request = (new \StackPay\Payments\Requests\v1\PaymentMethodRequest($paymentMethod))
    ->create();

$response = $request->send();
```

#### Store a tokenized Payment Method

```php
$paymentMethod        = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->token = new \StackPay\Payments\Structures\Token('this-is-a-payment-token');

$request = (new \StackPay\Payments\Requests\v1\PaymentMethodRequest($this->paymentMethod))
    ->token();

$response = $request->send();
```

#### Delete a stored Payment Method

```php
$paymentMethod     = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\PaymentMethodRequest($paymentMethod))
            ->delete();

$response = $request->send();
```

### Scheduled Transactions

Scheduled transactions run in a batch once per day on the given date at `12:00:00 UTC`.

Use a DateTime object to set the value for the `scheduledAt` field. Use of a timezone is optional. It is recommended that you experiment with this value knowing that the API will process the date as described above.

```php
$scheduledAt = new DateTime('2018-03-20');
$scheduledAt->setTimezone(new DateTimeZone('America/New_York'));

// or simply

$scheduledAt = new DateTime('2018-03-20', new DateTimeZone('America/New_York'));
```

#### Create a Scheduled Transaction

##### via stored Payment Method
```php
$merchant          = new \StackPay\Payments\Structures\Merchant;
$merchant->id      = 12345;
$merchant->hashKey = 'merchant-hash-key-value';

$paymentMethod     = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->id = 12345;

$scheduledTransaction = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->merchant      = $merchant;
$scheduledTransaction->paymentMethod = $paymentMethod;
$scheduledTransaction->amount        = 5000;
$scheduledTransaction->currencyCode  = 'USD';
$scheduledTransaction->scheduledAt   = new DateTime('second friday');
$scheduledTransaction->externalId    = 'id-in-remote-system';

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

$response = $request->send();
```

##### via Payment Method Token
```php
$merchant          = new \StackPay\Payments\Structures\Merchant;
$merchant->id      = 12345;
$merchant->hashKey = 'merchant-hash-key-value';

$paymentMethod        = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->token = 'payment-method-token';

$scheduledTransaction = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->merchant      = $merchant;
$scheduledTransaction->paymentMethod = $paymentMethod;
$scheduledTransaction->amount        = 5000;
$scheduledTransaction->currencyCode  = 'USD';
$scheduledTransaction->scheduledAt   = new DateTime('second friday');
$scheduledTransaction->externalId    = 'id-in-remote-system';

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

$response = $request->send();
```

##### via Account Details
```php
$merchant          = new \StackPay\Payments\Structures\Merchant;
$merchant->id      = 12345;
$merchant->hashKey = 'merchant-hash-key-value';

$paymentMethod                  = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->accountNumber   = '4111111111111111';
$paymentMethod->expirationMonth = '12';
$paymentMethod->expirationYear  = '25';
$paymentMethod->cvv2            = '999';
$paymentMethod->billingName     = 'Stack Payman';
$paymentMethod->billingAddress1 = '5360 Legacy Drive #150';
$paymentMethod->billingCity     = 'Plano';
$paymentMethod->billingState    = 'TX';
$paymentMethod->billingZip      = '75024';
$paymentMethod->billingCountry  = \StackPay\Payments\Structures\Country::usa();

$scheduledTransaction                = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->merchant      = $merchant;
$scheduledTransaction->paymentMethod = $paymentMethod;
$scheduledTransaction->amount        = 5000;
$scheduledTransaction->currencyCode  = 'USD';
$scheduledTransaction->scheduledAt   = new DateTime('second friday');
$scheduledTransaction->externalId    = 'id-in-remote-system';

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->create();

$response = $request->send();
```

#### Get Scheduled Transaction

```php
$scheduledTransaction     = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->get();

$response = $request->send();
```

#### Retry Scheduled Transaction

This method is only available when a scheduled transaction is in a `failed` state.

##### with currently attached Payment Method

```php
$scheduledTransaction     = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

$response = $request->send();
```

##### with a different Payment Method

Example below shows the simplest example: via stored Payment Method (from above). A different can be attached using a token or account details as well by building the `$paymentMethod` object as shown in the Create examples above.

```php
$paymentMethod     = new \StackPay\Payments\Structures\PaymentMethod;
$paymentMethod->id = 12345;

$scheduledTransaction                = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id            = 12345;
$scheduledTransaction->paymentMethod = $paymentMethod;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->retry();

$response = $request->send();
```

#### Delete Scheduled Transaction

This method is only available when a scheduled transaction is in a `scheduled` state.

```php
$scheduledTransaction     = new \StackPay\Payments\Structures\ScheduledTransaction;
$scheduledTransaction->id = 12345;

$request = (new \StackPay\Payments\Requests\v1\ScheduledTransactionRequest($scheduledTransaction))
            ->delete();

$response = $request->send();
```

## Documentation

Please see https://developer.mystackpay.com/docs for up-to-date documentation.

## Development

Install dependencies:

```bash
composer install
```

## Tests

Install dependencies as mentioned above (which will resolve
[PHPUnit](http://packagist.org/packages/phpunit/phpunit)), then you can run the
test suite:

```bash
composer test
```

If you plan to use these tests, it is highly recommended that you familiarize yourself with PHPUnit as well as the `phpunit.xml` configuration file included with this package.

## Support

- [https://developer.mystackpay.com/docs](https://developer.mystackpay.com/docs)
- [stackoverflow](http://stackoverflow.com/questions/tagged/stackpay)

## Contributing Guidelines

Please refer to [CONTRIBUTING.md](CONTRIBUTING.md) (coming soon)
