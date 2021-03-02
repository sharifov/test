<?php

namespace webapi\modules\v1\controllers;

use modules\flight\models\FlightQuote;
use modules\flight\src\exceptions\FlightCodeException;
use modules\flight\src\forms\TicketFlightsForm;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\services\flightQuote\FlightQuotePdfService;
use sales\helpers\app\AppHelper;
use sales\repositories\product\ProductQuoteRepository;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;

/**
 * Class FlightController
 */
class FlightController extends ApiBaseController
{
    public function actionTicket()
    {
        $form = new TicketFlightsForm();
        $post = Yii::$app->request->post();

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on request'),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE)
            );
        }

        if (!$flightQuote = FlightQuote::findOne(['fq_flight_request_uid' => $form->uniqueId])) {
            return new ErrorResponse(
                new StatusCodeMessage(404),
                new MessageMessage('FlightQuote not found'),
                new CodeMessage(FlightCodeException::FLIGHT_QUOTE_NOT_FOUND)
            );
        }

        $flightQuoteRepository = Yii::createObject(FlightQuoteRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);
        $productQuote = $flightQuote->fqProductQuote;

        if ($form->status === $form::SUCCESS_STATUS) {
            $productQuote->booked();
            try {
                FlightQuotePdfService::processingFile($flightQuote);
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'FlightController:processingFile:failed');
            }
        } else {
            $productQuote->error();
        }
        $productQuoteRepository->save($productQuote);

        $flightQuote->fq_ticket_json = $post;
        $flightQuoteRepository->save($flightQuote);

        return new SuccessResponse(
            new StatusCodeMessage(200),
            new DataMessage([
                'flightQuoteUid' => $flightQuote->fq_uid,
            ])
        );
    }
}
