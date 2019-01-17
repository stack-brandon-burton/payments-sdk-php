<?php

use StackPay\Payments\Requests\v1\PaymentPlanRequest;
use StackPay\Payments\Structures;
use StackPay\Payments\Translators;

class PaymentPlanRequestUnitTest extends UnitTestCase
{
    public function setUp()
    {
        $this->StackPay                = new StackPay\Payments\StackPay('public-12345', 'private-12345');
        $this->merchant                = new Structures\Merchant(13, 'merchant_hash_key_123');
        $this->paymentPlan             = new Structures\PaymentPlan;
        $this->paymentPlan->id         = 12345;
        $this->paymentPlan->merchant   = $this->merchant;

        $this->request = new PaymentPlanRequest($this->paymentPlan);
    }

    public function testConstructor()
    {
        $this->assertEquals($this->request->paymentPlan, $this->paymentPlan);
    }

    public function testCopy()
    {
        $mockedCopyPaymentPlanElement = [
            'fakeKey1' => 'fakeValue1',
            'fakeKey2' => 'fakeValue2',
            'fakeKey3' => 'fakeValue3',
        ];

        $mockedRestTranslator = Mockery::mock(Translators\V1RESTTranslator::class);
        $mockedRestTranslator->shouldReceive('buildPaymentPlanCopyElement')->once()
            ->with($this->paymentPlan)
            ->andReturn($mockedCopyPaymentPlanElement);

        $this->request->restTranslator = $mockedRestTranslator;

        $createRequest = $this->request->copy();

        $this->assertEquals($createRequest->method, 'POST');
        $this->assertEquals($createRequest->endpoint, '/api/merchants/' . $this->paymentPlan->merchant->id . '/payment-plan');
        $this->assertEquals($createRequest->hashKey, $this->merchant->hashKey);
        $this->assertEquals($createRequest->body, $mockedCopyPaymentPlanElement);
    }
}