<?php

namespace modules\flight\src\services\flightQuote;

use common\components\BackOffice;
use modules\flight\models\FlightQuote;
use modules\order\src\events\OrderFileGeneratedEvent;
use sales\services\pdf\processingPdf\PdfBaseService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

/**
 * Class FlightQuotePdfService
 */
class FlightQuotePdfService extends PdfBaseService
{
    public $templateKey = 'flight_ticket_pdf';
    public $eventType = OrderFileGeneratedEvent::TYPE_FLIGHT_CONFIRMATION;

    public function generateContent(): string
    {
        /** @var FlightQuote $flightQuote */
        $flightQuote = $this->object;
        if (!$flightQuote->fq_flight_request_uid) {
            throw new \RuntimeException('FlightRequestUid is empty');
        }
        $requestData = [
            'uid' => [$flightQuote->fq_flight_request_uid],
            'type' => 'e-ticket',
        ];
        $host = Yii::$app->params['backOffice']['serverUrlV2'];
        $responseBO = BackOffice::sendRequest2('download/files', $requestData, 'POST', 120, $host);

        if ($responseBO->isOk) {
            $status = $responseBO->getStatusCode();
            $headers = $responseBO->getHeaders();

            if (
                (int) $status === 200 &&
                ($contentType = ArrayHelper::getValue($headers, 'content-type')) &&
                $contentType === 'application/pdf'
            ) {
                return $responseBO->getContent();
            }

            $responseData = $responseBO->getData();

            if ($status = (ArrayHelper::getValue($responseData, 'status') !== null)) {
                if ($status === false && $message = ArrayHelper::getValue($responseData, 'message')) {
                    throw new \RuntimeException('FlightQuotePdfService BO response error: ' . $message);
                }
            }
            \Yii::error(
                VarDumper::dumpAsString([
                'data' => $requestData,
                'responseData' => $responseData,
                ]),
                'FlightQuotePdfService:book:failResponse'
            );
            throw new \RuntimeException('FlightQuotePdfService BO response fail');
        }
        \Yii::error(
            VarDumper::dumpAsString([
            'data' => $requestData,
            'responseContent' => $responseBO->content,
            ]),
            'FlightQuotePdfService:request'
        );
        throw new \RuntimeException('FlightQuotePdfService BO request error. ' . VarDumper::dumpAsString($responseBO->content));
    }

    public function generateAsFile(): string
    {
        $patchToDir =  \Yii::getAlias('@frontend/runtime/pdf/');
        $patchToFile = $patchToDir . $this->generateName();

        if (!file_exists($patchToDir)) {
            FileHelper::createDirectory($patchToDir);
        }
        if (file_exists($patchToFile)) {
            FileHelper::unlink($patchToFile);
        }
        file_put_contents($patchToFile, $this->generateContent());
        return $patchToFile;
    }

    public function generateForBrowserOutput()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/pdf');
        return $this->generateContent();
    }
}
