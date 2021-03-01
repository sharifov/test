<?php

namespace modules\flight\components\api;

use common\components\BackOffice;
use modules\flight\models\FlightQuote;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use sales\services\TransactionManager;
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
        $host = Yii::$app->params['backOffice']['serverUrlV2'];
        $responseBO = BackOffice::sendRequest2('products/book-flight', $requestData, 'POST', 120, $host);

        if ($responseBO->isOk) {
            $responseData = $responseBO->data;

            if (!empty($responseData['success'])) {
                return $responseData['success'];
            }

            if ($error = self::detectResponseDataErrors($responseData)) {
                \Yii::error(
                    VarDumper::dumpAsString(['requestData' => $requestData, 'error' => $error]),
                    'FlightQuoteBookService:book:errors'
                );
                throw new \RuntimeException('FlightQuoteBookService BO errors: ' . $error);
            }
            \Yii::error(
                VarDumper::dumpAsString([
                'data' => $requestData,
                'responseData' => $responseData,
                ]),
                'FlightQuoteBookService:book:failResponse'
            );
            throw new \RuntimeException('FlightQuoteBookService BO in response not found success||errors||warning.');
        }
        \Yii::error(
            VarDumper::dumpAsString([
            'data' => $requestData,
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
     * @param array $data
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

            $flightQuote->fq_json_booking = $responseData;
            $flightQuote->fq_record_locator = $recordLocator;
            $flightQuoteRepository->save($flightQuote);

            $productQuote = $flightQuote->fqProductQuote;
            $productQuote->inProgress();
            $productQuoteRepository->save($productQuote);
        });
        return true;
    }
}
