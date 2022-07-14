<?php

namespace modules\flight\components\api;

use common\components\BackOffice;
use common\models\Project;
use modules\flight\models\FlightQuote;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\services\flightQuote\FlightQuoteBookGuardService;
use modules\order\src\entities\order\Order;
use src\services\TransactionManager;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use modules\product\src\entities\productQuote\ProductQuoteRepository;

/**
 * Class FlightQuoteBookService
 */
class FlightQuoteBookService
{
    /**
     * @param array $data
     * @return mixed
     */
    public static function prepareRequestData(array $data)
    {
        return $data['Request'] ?? [];
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function requestBook(array $data)
    {
        $requestData = self::prepareRequestData($data);
        $host = Yii::$app->params['backOffice']['urlV2'];
        $responseBO = BackOffice::sendRequest2('products/book-flight', $requestData, 'POST', 120, $host);

        if ($responseBO->isOk) {
            $responseData = $responseBO->data;

            if (!empty($responseData['success'])) {
                return $responseData['success'];
            }

            if ($error = self::detectResponseDataErrors($responseData)) {
                \Yii::error(
                    VarDumper::dumpAsString([
                        'error' => $error
                    ]),
                    'FlightQuoteBookService:book:errors'
                );
                throw new \RuntimeException('FlightQuoteBookService BO errors: ' . $error);
            }
            \Yii::error(
                VarDumper::dumpAsString([
                    'responseData' => $responseData,
                ]),
                'FlightQuoteBookService:book:failResponse'
            );
            throw new \RuntimeException('FlightQuoteBookService BO in response not found success||errors||warning.');
        }
        \Yii::error(
            VarDumper::dumpAsString([
                'responseContent' => $responseBO->content,
            ]),
            'FlightQuoteBookService:book:request'
        );
        throw new \RuntimeException('FlightQuoteBookService BO request error. ' . VarDumper::dumpAsString($responseBO->content));
    }

    public static function detectResponseDataErrors(array $responseData): string
    {
        $result = '';
        if (!empty($responseData['errors'])) {
            $result .= VarDumper::dumpAsString($responseData['errors']);
        }
        if (!empty($responseData['warning'])) {
            $result .= VarDumper::dumpAsString($responseData['warning']);
        }
        return $result;
    }

    /**
     * @param FlightQuote $flightQuote
     * @param array $responseData
     * @return bool
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public static function createBook(FlightQuote $flightQuote, array $responseData): bool
    {
        $transactionManager = Yii::createObject(TransactionManager::class);
        $flightQuoteRepository = Yii::createObject(FlightQuoteRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);

        $transactionManager->wrap(function () use ($flightQuote, $responseData, $flightQuoteRepository, $productQuoteRepository) {
            if (!$recordLocator = ArrayHelper::getValue($responseData, 'recordLocator')) {
                throw new \RuntimeException('RecordLocator not found');
            }
            if (!$order = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqOrder')) {
                throw new \RuntimeException('Order not found');
            }
            /** @var Order $order */
            if (!$hybridUid = ArrayHelper::getValue($order->or_request_data, 'Request.FlightRequest.uid')) {
                throw new \RuntimeException('HybridUid not found in order');
            }

            $flightQuote->fq_flight_request_uid = $hybridUid;
            $flightQuote->fq_json_booking = $responseData;
            $flightQuote->fq_record_locator = $recordLocator;
            $flightQuoteRepository->save($flightQuote);

            $productQuote = $flightQuote->fqProductQuote;
            $productQuote->inProgress();
            $productQuoteRepository->save($productQuote);
        });
        return true;
    }

    public static function cancel(string $uid, int $projectId): void
    {
        $project = Project::findOne($projectId);
        if (!$project) {
            throw new \DomainException('Nor found Project. Id ' . $projectId);
        }
        if (!$project->api_key) {
            throw new \DomainException('Nor found API KEY. Project. Id ' . $projectId);
        }
        $data = [
            'apiKey' => $project->api_key,
            'FlightRequest' => [
                'uid' => $uid,
            ]
        ];
        $host = Yii::$app->params['backOffice']['urlV2'];
        $responseBO = BackOffice::sendRequest2('flight-request/cancel', $data, 'POST', 120, $host);

        if (!$responseBO->isOk) {
            Yii::error([
                'message' => 'BO response error.',
                'response' => VarDumper::dumpAsString($responseBO->content),
                'data' => $data,
            ], 'FlightQuoteBookService:cancel');
            throw new \RuntimeException('Flight Cancel BO request error. ' . VarDumper::dumpAsString($responseBO->content));
        }

        $responseData = $responseBO->data;

        if (empty($responseData['status'])) {
            Yii::error([
                'message' => 'BO response error. Not found Status',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'FlightQuoteBookService:cancel');
            throw new \DomainException('Undefined BO response. Not found Status');
        }

        if (!in_array($responseData['status'], ['Success', 'Failed'], false)) {
            Yii::error([
                'message' => 'BO response undefined status.',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'FlightQuoteBookService:cancel');
            throw new \DomainException('Undefined BO response Status');
        }

        if ($responseData['status'] === 'Success') {
            return;
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
            ], 'FlightQuoteBookService:cancel');
            throw new \RuntimeException('Flight Cancel BO errors: ' . $errors);
        }

        \Yii::error([
            'data' => $data,
            'response' => VarDumper::dumpAsString($responseData),
        ], 'FlightQuoteBookService:cancel');
        throw new \RuntimeException('Flight Cancel BO errors. Undefined error.');
    }

    public static function void(string $uid, int $projectId): void
    {
        $project = Project::findOne($projectId);
        if (!$project) {
            throw new \DomainException('Nor found Project. Id ' . $projectId);
        }
        if (!$project->api_key) {
            throw new \DomainException('Nor found API KEY. Project. Id ' . $projectId);
        }
        $data = [
            'apiKey' => $project->api_key,
            'FlightRequest' => [
                'uid' => $uid,
            ]
        ];
        $host = Yii::$app->params['backOffice']['urlV2'];
        $responseBO = BackOffice::sendRequest2('flight-request/void', $data, 'POST', 120, $host);

        if (!$responseBO->isOk) {
            Yii::error([
                'message' => 'BO response error.',
                'response' => VarDumper::dumpAsString($responseBO->content),
                'data' => $data,
            ], 'FlightQuoteBookService:void');
            throw new \RuntimeException('Flight Void BO request error. ' . VarDumper::dumpAsString($responseBO->content));
        }

        $responseData = $responseBO->data;

        if (empty($responseData['status'])) {
            Yii::error([
                'message' => 'BO response error. Not found Status',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'FlightQuoteBookService:void');
            throw new \DomainException('Undefined BO response. Not found Status');
        }

        if (!in_array($responseData['status'], ['Success', 'Failed'], false)) {
            Yii::error([
                'message' => 'BO response undefined status.',
                'response' => VarDumper::dumpAsString($responseData),
                'data' => $data,
            ], 'FlightQuoteBookService:void');
            throw new \DomainException('Undefined BO response Status');
        }

        if ($responseData['status'] === 'Success') {
            return;
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
            ], 'FlightQuoteBookService:void');
            throw new \RuntimeException('Flight Void BO errors: ' . $errors);
        }

        \Yii::error([
            'data' => $data,
            'response' => VarDumper::dumpAsString($responseData),
        ], 'FlightQuoteBookService:void');
        throw new \RuntimeException('Flight Void BO errors. Undefined error.');
    }
}
