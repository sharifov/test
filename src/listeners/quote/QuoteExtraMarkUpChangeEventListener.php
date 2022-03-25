<?php

namespace src\listeners\quote;

use common\components\BackOffice;
use common\models\GlobalLog;
use src\events\quote\QuoteExtraMarkUpChangeEvent;
use src\logger\db\GlobalLogInterface;
use src\logger\db\LogDTO;
use src\services\quote\quotePriceService\ClientQuotePriceService;
use yii\helpers\Json;

class QuoteExtraMarkUpChangeEventListener
{
    public function handle(QuoteExtraMarkUpChangeEvent $event): void
    {
        $userId = $event->userId;
        $quote = $event->quote;
        $sellingOld = $event->sellingOld;
        $quote->refresh();
        $lead =  $quote->lead;
        $clientQuotePriceService = new ClientQuotePriceService($quote);
        $priceData = $clientQuotePriceService->getClientPricesData();
        (\Yii::createObject(GlobalLogInterface::class))->log(
            new LogDTO(
                get_class($quote),
                $quote->id,
                \Yii::$app->id,
                $userId,
                Json::encode(['selling' => $sellingOld]),
                Json::encode(['selling' => $priceData['total']['selling']]),
                null,
                GlobalLog::ACTION_TYPE_UPDATE
            )
        );
        if ($lead->called_expert) {
            $data = $quote->getQuoteInformationForExpert(true);
            $response = BackOffice::sendRequest('lead/update-quote', 'POST', json_encode($data));
            if ($response['status'] != 'Success' || !empty($response['errors'])) {
                \Yii::$app->getSession()->setFlash('warning', sprintf(
                    'Update info quote [%s] for expert failed! %s',
                    $quote->uid,
                    print_r($response['errors'], true)
                ));
            }
        }
    }
}
