<?php

namespace modules\order\src\payment\services;

use common\components\BackOffice;
use yii\helpers\VarDumper;
use Yii;

class SimplePaymentService implements PaymentService
{
    public function capture(array $data): array
    {
        $host = Yii::$app->params['backOffice']['urlV2'];
        $responseBO = BackOffice::sendRequest2('payment/capture-amount', $data, 'POST', 120, $host);

        if (!$responseBO->isOk) {
            Yii::error([
                'message' => 'BO response error.',
                'response' => VarDumper::dumpAsString($responseBO->content),
                'data' => $data,
            ], 'SimplePaymentService:capture');
            throw new \RuntimeException('Payment capture BO request error. ' . VarDumper::dumpAsString($responseBO->content));
        }

        $responseData = $responseBO->data;

        if (empty($responseData['status'])) {
            Yii::error([
                'message' => 'BO response error. Not found Status',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'SimplePaymentService:capture');
            throw new \DomainException('Undefined BO response. Not found Status');
        }

        if (!in_array($responseData['status'], ['success', 'failed'], false)) {
            Yii::error([
                'message' => 'BO response undefined status.',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'SimplePaymentService:capture');
            throw new \DomainException('Undefined BO response Status');
        }

        if ($responseData['status'] === 'success') {
            return [
                'transaction_id' => $responseData['transaction_id'] ?? null
            ];
        }

        if (!empty($responseData['errors'])) {
            $errors = '';
            foreach ($responseData['errors'] as $error) {
                if (is_array($error)) {
                    $errors .= implode('; ', $error);
                } else {
                    $errors .= $error . '; ';
                }
            }
            Yii::error([
                'message' => 'BO response error.',
                'error' => $errors,
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'SimplePaymentService:capture');
            throw new \RuntimeException('Payment Capture BO errors: ' . $errors);
        }

        \Yii::error([
            'data' => $data,
            'response' => VarDumper::dumpAsString($responseData),
        ], 'SimplePaymentService:capture');
        throw new \RuntimeException('Payment Capture BO errors. Undefined error.');
    }

    public function refund(array $data): array
    {
        $host = Yii::$app->params['backOffice']['urlV2'];
        $responseBO = BackOffice::sendRequest2('payment/refund-amount', $data, 'POST', 120, $host);

        if (!$responseBO->isOk) {
            Yii::error([
                'message' => 'BO response error.',
                'response' => VarDumper::dumpAsString($responseBO->content),
                'data' => $data,
            ], 'SimplePaymentService:refund');
            throw new \RuntimeException('Payment refund BO request error. ' . VarDumper::dumpAsString($responseBO->content));
        }

        $responseData = $responseBO->data;

        if (empty($responseData['status'])) {
            Yii::error([
                'message' => 'BO response error. Not found Status',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'SimplePaymentService:refund');
            throw new \DomainException('Undefined BO response. Not found Status');
        }

        if (!in_array($responseData['status'], ['success', 'failed'], false)) {
            Yii::error([
                'message' => 'BO response undefined status.',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'SimplePaymentService:refund');
            throw new \DomainException('Undefined BO response Status');
        }

        if ($responseData['status'] === 'success') {
            return [
                'transaction_id' => $responseData['transaction_id'] ?? null
            ];
        }

        if (!empty($responseData['errors'])) {
            $errors = '';
            foreach ($responseData['errors'] as $error) {
                if (is_array($error)) {
                    $errors .= implode('; ', $error);
                } else {
                    $errors .= $error . '; ';
                }
            }
            Yii::error([
                'message' => 'BO response error.',
                'error' => $errors,
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'SimplePaymentService:refund');
            throw new \RuntimeException('Payment Refund BO errors: ' . $errors);
        }

        \Yii::error([
            'data' => $data,
            'response' => VarDumper::dumpAsString($responseData),
        ], 'SimplePaymentService:refund');
        throw new \RuntimeException('Payment Refund BO errors. Undefined error.');
    }
}
