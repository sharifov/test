<?php

namespace modules\order\src\payment\jobs;

use common\models\Payment;
use common\models\Transaction;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\payment\services\PaymentService;
use yii\queue\RetryableJobInterface;

/**
 * Class ChargePaymentJob
 *
 * @property int $paymentId
 */
class ChargePaymentJob implements RetryableJobInterface
{
    public $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function execute($queue)
    {
        $payment = Payment::findOne($this->paymentId);
        if (!$payment) {
            \Yii::error([
                'message' => 'Charge payment Job error',
                'error' => 'Not found payment',
                'paymentId' => $this->paymentId,
            ], 'ChargePaymentJob');
            return;
        }
        if ($payment->isCompleted()) {
            \Yii::error([
                'message' => 'Payment already completed',
                'paymentId' => $this->paymentId,
            ], 'ChargePaymentJob');
            return;
        }

        try {
            $paymentService = \Yii::createObject(PaymentService::class);
            $result = $paymentService->capture([
                'amount' => $payment->pay_amount,
                'transaction_id' => $payment->pay_code,
            ]);
            $transaction = new Transaction([
                'tr_amount' => $payment->pay_amount,
                'tr_code' => $result['transaction_id'] ?? null,
                'tr_date' => date('Y-m-d'),
                'tr_type_id' => Transaction::TYPE_CAPTURE,
                'tr_payment_id' => $payment->pay_id,
                'tr_created_dt' => date('Y-m-d H:i:s'),
            ]);
            if (!$transaction->save()) {
                \Yii::error([
                    'message' => 'Transaction save error',
                    'model' => $transaction->getAttributes(),
                    'errors' => $transaction->getErrors()
                ], 'ChargePaymentJob:Transaction:Save');
            }
            $repo = \Yii::createObject(PaymentRepository::class);
            $payment->completed();
            $repo->save($payment);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Payment Capture Error',
                'error' => $e->getMessage(),
                'paymentId' => $payment->pay_id,
            ], 'ChargePaymentJob');
        }
    }

    public function getTtr(): int
    {
        return 5;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Charge Payment error',
            'error' => $error->getMessage(),
        ], 'ChargePaymentJob');
        return false;
        return !($attempt > 5);
    }
}
