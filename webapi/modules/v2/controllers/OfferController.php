<?php

namespace webapi\modules\v2\controllers;

use modules\offer\src\entities\offer\OfferRepository;
use modules\offer\src\entities\offerViewLog\CreateDto;
use modules\offer\src\exceptions\OfferCodeException;
use modules\offer\src\services\OfferViewLogService;
use modules\offer\src\useCases\offer\api\view\OfferViewForm;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;

/**
 * Class OfferController
 *
 * @property OfferRepository $offerRepository
 * @property OfferViewLogService $offerViewLogService
 */
class OfferController extends BaseController
{
    private $offerRepository;
    private $offerViewLogService;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        OfferRepository $offerRepository,
        OfferViewLogService $offerViewLogService,
        $config = []
    )
    {
        parent::__construct($id, $module, $logger, $config);
        $this->offerRepository = $offerRepository;
        $this->offerViewLogService = $offerViewLogService;
    }

    public function actionView()
    {
        $form = new OfferViewForm();

        $form->load(\Yii::$app->request->post());

        if (!$form->load(\Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Offer data on POST request'),
                new CodeMessage(OfferCodeException::API_OFFER_VIEW_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(OfferCodeException::API_OFFER_VIEW_VALIDATE)
            );
        }

        if (!$offer = $this->offerRepository->getByGid($form->offerGid)) {
            return new ErrorResponse(
                new ErrorsMessage('Not found Offer'),
                new CodeMessage(OfferCodeException::API_OFFER_VIEW_NOT_FOUND)
            );
        }

        $this->offerViewLogService->log(
            new CreateDto(
                $offer->of_id,
                $form->visitor->id,
                $form->visitor->ipAddress,
                $form->visitor->userAgent
            )
        );

        return new SuccessResponse(
            new Message('offer', $offer->serialize())
        );
    }
}
