<?php

namespace webapi\modules\v1\controllers;

use common\components\jobs\LeadRequestJob;
use common\models\ApiLog;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use src\model\leadRequest\entity\LeadRequest;
use src\model\leadRequest\repository\LeadRequestRepository;
use webapi\src\ApiCodeException;
use webapi\src\forms\leadRequest\AdwordsRequestForm;
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
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

/**
 * Class LeadRequestController
 *
 * @property LeadRequestRepository $leadRequestRepository
 */
class LeadRequestController extends Controller
{
    private LeadRequestRepository $leadRequestRepository;

    /**
     * LeadRequestController constructor.
     * @param $id
     * @param $module
     * @param LeadRequestRepository $leadRequestRepository
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        LeadRequestRepository $leadRequestRepository,
        $config = []
    ) {
        $this->leadRequestRepository = $leadRequestRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * @api {post} /v1/lead-request/adwords Lead create from request
     * @apiVersion 0.1.0
     * @apiName Lead create adwords
     * @apiGroup Leads
     *
     * @apiParam {string{50}}   google_key                  Google key
     * @apiParam {bool}         [is_test]                   Is test
     * @apiParam {object}       user_column_data            A repeated key-value tuple transmitting user submitted data
     *
     * @apiParamExample {json} Request-Example:
     *
     *   {
     *     "google_key":"examplekey",
     *     "is_test":true,
     *     "user_column_data": [
     *          {
     *            "string_value":"john@doe.com",
     *            "column_id": "EMAIL"
     *          },
     *          {
     *            "string_value":"+11234567890",
     *            "column_id":"PHONE_NUMBER"
     *          }
     *    ]
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "resultMessage": "LeadRequest created. ID(123)"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 200 OK
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "email": [
     *             "Email cannot be blank"
     *        ]
     *     }
     * }
     */
    public function actionAdwords()
    {
        if (!SettingHelper::getLeadApiGoogleAllowCreate()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Service disabled')
            );
        }

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

        $adwordsRequestForm = new AdwordsRequestForm();
        $post = Yii::$app->request->post();

        if (!$adwordsRequestForm->load($post)) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on request'),
            ));
        }
        if (!$adwordsRequestForm->validate()) {
            \Yii::warning(
                ErrorsToStringHelper::extractFromModel($adwordsRequestForm),
                'LeadRequestController:actionAdwords:AdwordsRequestForm'
            );
            return $this->endApiLog($apiLog, new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($adwordsRequestForm->getErrors()),
            ));
        }

        try {
            $leadRequest = LeadRequest::create(
                LeadRequest::TYPE_GOOGLE,
                $adwordsRequestForm->getAppProjectKey()->apk_project_id,
                $adwordsRequestForm->getAppProjectKey()->apk_project_source_id,
                $post
            );
            $this->leadRequestRepository->save($leadRequest);

            $leadRequestJob = new LeadRequestJob();
            $leadRequestJob->leadRequestId = $leadRequest->lr_id;

            if ($jobId = Yii::$app->queue_client_chat_job->priority(50)->push($leadRequestJob)) {
                $leadRequest->setJobId($jobId);
                $this->leadRequestRepository->save($leadRequest);
            } else {
                throw new \RuntimeException('LeadRequestJob not saved');
            }

            return $this->endApiLog($apiLog, new SuccessResponse(
                new StatusCodeMessage(200),
                new MessageMessage('OK'),
                new DataMessage([
                    'resultMessage' => 'LeadRequest created. ID(' . $leadRequest->lr_id . ')',
                ])
            ));
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'LeadRequestController:actionAdwords:Throwable');
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
            ));
        }
    }

    private function startApiLog(string $action = ''): ApiLog
    {
        $data = Yii::$app->request->post();
        $data['received_microtime'] = microtime(true);

        $apiLog = new ApiLog();
        $apiLog->al_request_data = @json_encode($data);
        $apiLog->al_request_dt = date('Y-m-d H:i:s');
        $apiLog->al_ip_address = Yii::$app->request->getRemoteIP();
        $apiLog->al_action = $action;

        $apiLog->start_microtime = microtime(true);
        $apiLog->start_memory_usage = memory_get_usage();

        if (!$apiLog->save()) {
            Yii::error(print_r($apiLog->errors, true), 'ApiBaseControl:startApiLog:ApiLog:save');
        }
        return $apiLog;
    }

    private function endApiLog(ApiLog $apiLog, Response $response): Response
    {
        $apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }

    private static function validateErrorResponse(Model $form): ErrorResponse
    {
        return  new ErrorResponse(
            new MessageMessage(Messages::VALIDATION_ERROR),
            new ErrorsMessage($form->getErrors()),
        );
    }

    private static function notLoadErrorResponse(string $formName = ''): ErrorResponse
    {
        return new ErrorResponse(
            new StatusCodeMessage(400),
            new MessageMessage(Messages::LOAD_DATA_ERROR),
            new ErrorsMessage($formName . '. Not found data on request')
        );
    }

    private static function errorMessage(\Throwable $throwable, ?array $additionalData = null): array
    {
        $throwableLog = AppHelper::throwableLog($throwable, false);
        if (!$additionalData) {
            return $throwableLog;
        }
        $throwableLog['additionalData'] = $additionalData;
        return $throwableLog;
    }
}
