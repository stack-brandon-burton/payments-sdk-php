<?php

namespace StackPay\Payments\Transforms\Responses;

use StackPay\Payments\Exceptions;

trait CopyPaymentPlanTransform
{
    public function responseCopyPaymentPlan($transaction)
    {
        $body = $transaction->response()->body();

        $transaction->object()->paymentPlan()->setID($body['id']);
        $transaction->object()->paymentPlan()->setName($body['name']);
        $transaction->object()->paymentPlan()->setRequestIncomingId($body['request_incoming_id']);
        $transaction->object()->paymentPlan()->setDownPaymentAmount($body['down_payment_amount']);
        $transaction->object()->paymentPlan()->merchant()->setID($body['merchant_id']);
        $transaction->object()->paymentPlan()->configuration()->setMonths($body['configuration']['months']);
        if (!empty($body['split_merchant_id'])) {
            $transaction->object()->paymentPlan()->splitMerchant()->setID($body['split_merchant_id']);
        }
        if (!empty($body['configuration']['day'])) {
            $transaction->object()->paymentPlan()->configuration()->setDay($body['configuration']['day']);
        }
        if (!empty($body['payment_priority'])) {
            $transaction->object()->paymentPlan()->setPaymentPriority($body['payment_priority']);
        }
    }
}