<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use modules\flight\models\FlightQuote;
use modules\flight\src\exceptions\FlightCodeException;
use modules\flight\src\forms\TicketFlightsForm;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\services\flightQuote\FlightQuotePdfService;
use sales\helpers\app\AppHelper;
use sales\repositories\product\ProductQuoteRepository;
use webapi\src\ApiCodeException;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FlightController
 */
class FlightController extends ApiBaseController
{
    /* TODO:: add api docs */
    public function actionTicket()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        if (!Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found POST request'),
                new CodeMessage(ApiCodeException::REQUEST_IS_NOT_POST)
            );
        }

        if (!Yii::$app->request->post()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('POST data request is empty'),
                new CodeMessage(ApiCodeException::POST_DATA_IS_EMPTY)
            );
        }

        $form = new TicketFlightsForm();
        $post = Yii::$app->request->post();
        $flights = \Yii::$app->request->post('flights');

        if (!$flights) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Flights is not provided'),
                new CodeMessage(ApiCodeException::EVENT_OR_DATA_IS_NOT_PROVIDED)
            ));
        }

        if (!$form->load($flights[0])) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on request'),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_NOT_FOUND_DATA_ON_REQUEST)
            ));
        }

        if (!$form->validate()) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE)
            ));
        }

        if (!$flightQuote = FlightQuote::findOne(['fq_flight_request_uid' => $form->uniqueId])) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(404),
                new MessageMessage('FlightQuote not found'),
                new CodeMessage(FlightCodeException::FLIGHT_QUOTE_NOT_FOUND)
            ));
        }

        $flightQuoteRepository = Yii::createObject(FlightQuoteRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);
        $productQuote = $flightQuote->fqProductQuote;

        if ($form->status === $form::SUCCESS_STATUS) {
            $productQuote->booked();
            /* TODO:: to job */
            /*try {
                FlightQuotePdfService::processingFile($flightQuote);
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'FlightController:processingFile:failed');
            }*/
        } else {
            $productQuote->error();
        }
        $productQuoteRepository->save($productQuote);

        $flightQuote->fq_ticket_json = $post;
        $flightQuoteRepository->save($flightQuote);

        return $this->endApiLog($apiLog, new SuccessResponse(
            new StatusCodeMessage(200),
            new DataMessage([
                'flightQuoteUid' => $flightQuote->fq_uid,
            ])
        ));
    }

    private function endApiLog(ApiLog $apiLog, Response $response): Response
    {
        $apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }
}
