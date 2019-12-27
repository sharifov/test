<?php

namespace webapi\modules\v2\controllers;

use sales\model\lead\LeadCodeException;
use webapi\src\response\lead\LeadCreateResponse;
use Yii;
use sales\model\lead\useCase\lead\api\create\LeadCreateHandler;
use sales\model\lead\useCase\lead\api\create\LeadForm;
use webapi\src\logger\ApiLogger;
use webapi\src\response\ErrorResponse;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;

/**
 * Class LeadController
 *
 * @property LeadCreateHandler $leadCreateHandler
 */
class LeadController extends BaseController
{
    private $leadCreateHandler;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        LeadCreateHandler $leadCreateHandler,
        $config = []
    )
    {
        parent::__construct($id, $module, $logger, $config);
        $this->leadCreateHandler = $leadCreateHandler;
    }

    public function actionCreate(): Response
    {
        $form = new LeadForm();

        if (!$form->load(Yii::$app->request->post())) {
            $message = 'Not found Lead data on POST request';
            return new ErrorResponse([
                'statusCode' => 400,
                'message' => $message,
                'errors' => [$message],
                'code' => LeadCodeException::API_LEAD_NOT_FOUND_DATA_ON_REQUEST,
            ]);
        }

        if (!$form->validate()) {
            return new ErrorResponse([
                'errors' => $form->errors,
                'code' => LeadCodeException::API_LEAD_VALIDATE,
            ]);
        }

        try {
            $lead = $this->leadCreateHandler->handle($form);
        } catch (\Throwable $e) {
            return new ErrorResponse([
                'message' => $e->getMessage(),
                'errors' => [$e->getMessage()],
                'code' => $e->getCode(),
            ]);
        }

        $response = new SuccessResponse();

        $response->addDataResponse(
            new LeadCreateResponse([
                'uid' => $lead->uid,
                'gid' => $lead->gid,
            ])
        );

        return $response;
    }
}
