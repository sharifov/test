<?php

namespace webapi\modules\v2\controllers;

use sales\helpers\app\AppHelper;
use sales\model\clientAccount\ClientAccountRepository;
use sales\model\clientAccount\entity\ClientAccount;
use sales\model\clientAccount\form\ClientAccountCreateApiForm;
use sales\model\clientAccount\form\ClientAccountGetApiForm;
use sales\model\clientAccount\form\ClientAccountUpdateApiForm;
use sales\services\client\ClientManageService;
use webapi\src\ApiCodeException;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use webapi\src\response\Response;
use yii\web\NotFoundHttpException;

/**
 * Class ClientAccount
 * @property ClientAccountRepository $clientAccountRepository
 * @property ClientManageService $clientManageService
 */
class ClientAccountController extends BaseController
{
    private ClientAccountRepository $clientAccountRepository;
    private ClientManageService $clientManageService;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param ClientAccountRepository $clientAccountRepository
     * @param ClientManageService $clientManageService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        ClientAccountRepository $clientAccountRepository,
        ClientManageService $clientManageService,
        $config = []
    ) {
        parent::__construct($id, $module, $logger, $config);
        $this->clientAccountRepository = $clientAccountRepository;
        $this->clientManageService = $clientManageService;
    }

    /**
     * @api {post} /v2/client-account/get Get Client Account
     * @apiVersion 0.2.0
     * @apiName GetClientAccount
     * @apiGroup ClientAccount
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{36}}       uuid        Client Uuid
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *   {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *          "ca_uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef",
     *          "ca_hid": 2,
     *          "ca_username": "example",
     *          "ca_first_name": "",
     *          "ca_middle_name": "",
     *          "ca_last_name": "",
     *          "ca_nationality_country_code": "",
     *          "ca_dob": "2001-09-09",
     *          "ca_gender": 1,
     *          "ca_phone": "",
     *          "ca_subscription": 1,
     *          "ca_language_id": "en-PI",
     *          "ca_currency_code": "EUR",
     *          "ca_timezone": "",
     *          "ca_created_ip": "",
     *          "ca_enabled": 1,
     *          "ca_origin_created_dt": "2020-11-19 10:45:17",
     *          "ca_origin_updated_dt": "2020-11-04 05:25:18"
     *       },
     *       "technical": {
     *           "action": "/v2/client-account/get",
     *           "response_id": 11934216,
     *           "request_dt": "2020-03-17 08:31:30",
     *           "response_dt": "2020-03-17 08:31:30",
     *           "execution_time": 0.156,
     *           "memory_usage": 979248
     *       },
     *       "request": {
     *           "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
     *       }
     *   }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     *   {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           ...
     *       },
     *       "code": "21301",
     *       "technical": {
     *          ...
     *       },
     *       "request": {
     *          ...
     *       }
     *   }
     */
    public function actionGet(): Response
    {
        if (!$projectId = $this->auth->au_project_id) {
            $message = 'Not found Project with current user: ' . $this->auth->au_api_username;
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($message),
                new ErrorsMessage($message),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $form = new ClientAccountGetApiForm();
        $form->projectId = $projectId;

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            if (!$clientAccount = ClientAccount::findOne(['ca_project_id' => $form->projectId, 'ca_uuid' => $form->uuid])) {
                throw new NotFoundHttpException('Not found ClientAccount');
            }
            $result = self::prepareGetResult($clientAccount->toArray());

            return new SuccessResponse(
                new DataMessage($result)
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'ClientAccountController:actionGet:Throwable');
            return new ErrorResponse(
                new MessageMessage($throwable->getMessage()),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/client-account/create Create Client Account
     * @apiVersion 0.2.0
     * @apiName CreateClientAccount
     * @apiGroup ClientAccount
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{36}}               [uuid]                      Client Uuid
     * @apiParam {int}                      [hid]                       Origin Id
     * @apiParam {string{100}}              username                    Username
     * @apiParam {string{100}}              [first_name]                First name
     * @apiParam {string{100}}              [middle_name]               Middle name
     * @apiParam {string{2}}                [nationality_country_code]  Nationality country code
     * @apiParam {datetime{YYYY-MM-DD}}     [dob]                       Dob
     * @apiParam {int{1..2}}                [gender]                    Gender
     * @apiParam {string{100}}              [phone]                     Phone
     * @apiParam {int{0..1}}                [subscription]              Subscription
     * @apiParam {string{5}}                [language_id]               Language
     * @apiParam {string{3}}                [currency_code]             Currency code
     * @apiParam {string{50}}               [timezone]                  Timezone
     * @apiParam {string{40}}               [created_ip]                Created ip
     * @apiParam {int{0..1}}                [enabled]                   Enabled
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}    [origin_created_dt] Origin Created dt
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}    [origin_updated_dt] Origin Updated dt
     *
     * @apiParamExample {json} Request-Example:
     * {
     *          "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef",
     *          "hid": 2,
     *          "username": "example",
     *          "first_name": "",
     *          "middle_name": "",
     *          "last_name": "",
     *          "nationality_country_code": "",
     *          "dob": "2001-09-09",
     *          "gender": 1,
     *          "phone": "",
     *          "subscription": 1,
     *          "language_id": "en-PI",
     *          "currency_code": "EUR",
     *          "timezone": "",
     *          "created_ip": "127.0.0.1",
     *          "enabled": 1,
     *          "origin_created_dt": "2020-11-19 10:45:17",
     *          "origin_updated_dt": "2020-11-04 05:25:18"
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *   {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *          "ClientAccount created successfully": 123
     *       },
     *       "technical": {
     *           "action": "/v2/client-account/create",
     *           "response_id": 11934216,
     *           "request_dt": "2020-03-17 08:31:30",
     *           "response_dt": "2020-03-17 08:31:30",
     *           "execution_time": 0.156,
     *           "memory_usage": 979248
     *       },
     *       "request": {
     *           "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
     *       }
     *   }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     *   {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           ...
     *       },
     *       "code": "21301",
     *       "technical": {
     *          ...
     *       },
     *       "request": {
     *          ...
     *       }
     *   }
     */
    public function actionCreate(): Response
    {
        if (!$projectId = $this->auth->au_project_id) {
            $message = 'Not found Project with current user: ' . $this->auth->au_api_username;
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($message),
                new ErrorsMessage($message),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $form = new ClientAccountCreateApiForm();
        $form->project_id = $projectId;

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            $clientAccount = ClientAccount::createFromApi($form);
            $clientAccount = $this->clientAccountRepository->save($clientAccount);
            $this->clientManageService->createOrLinkByClientAccount($clientAccount);

            return new SuccessResponse(
                new DataMessage(
                    new Message('ClientAccount created successfully', $clientAccount->ca_id)
                )
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'ClientAccountController:actionCreate:Throwable');
            return new ErrorResponse(
                new MessageMessage($throwable->getMessage()),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/client-account/update Update Client Account
     * @apiVersion 0.2.0
     * @apiName UpdateClientAccount
     * @apiGroup ClientAccount
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{36}}               uuid                        Client Uuid
     * @apiParam {int}                      [hid]                       Origin Id
     * @apiParam {string{100}}              [username]                  Username
     * @apiParam {string{100}}              [first_name]                First name
     * @apiParam {string{100}}              [middle_name]               Middle name
     * @apiParam {string{2}}                [nationality_country_code]  Nationality country code
     * @apiParam {datetime{YYYY-MM-DD}}     [dob]                       Dob
     * @apiParam {int{1..2}}                [gender]                    Gender
     * @apiParam {string{100}}              [phone]                     Phone
     * @apiParam {int{0..1}}                [subscription]              Subscription
     * @apiParam {string{5}}                [language_id]               Language
     * @apiParam {string{3}}                [currency_code]             Currency code
     * @apiParam {string{50}}               [timezone]                  Timezone
     * @apiParam {string{40}}               [created_ip]                Created ip
     * @apiParam {int{0..1}}                [enabled]                   Enabled
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}    [origin_created_dt] Origin Created dt
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}    [origin_updated_dt] Origin Updated dt
     *
     * @apiParamExample {json} Request-Example:
     * {
     *          "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef",
     *          "hid": 2,
     *          "username": "example",
     *          "first_name": "",
     *          "middle_name": "",
     *          "last_name": "",
     *          "nationality_country_code": "",
     *          "dob": "2001-09-09",
     *          "gender": 1,
     *          "phone": "",
     *          "subscription": 1,
     *          "language_id": "en-PI",
     *          "currency_code": "EUR",
     *          "timezone": "",
     *          "created_ip": "127.0.0.1",
     *          "enabled": 1,
     *          "origin_created_dt": "2020-11-19 10:45:17",
     *          "origin_updated_dt": "2020-11-04 05:25:18"
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *   {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *          "ClientAccount updated successfully": 123
     *       },
     *       "technical": {
     *           "action": "/v2/client-account/update",
     *           "response_id": 11934216,
     *           "request_dt": "2020-03-17 08:31:30",
     *           "response_dt": "2020-03-17 08:31:30",
     *           "execution_time": 0.156,
     *           "memory_usage": 979248
     *       },
     *       "request": {
     *           "uuid": "f04f9609-31e1-4dba-bffd-a689d4391fef"
     *       }
     *   }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     *   {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           ...
     *       },
     *       "code": "21301",
     *       "technical": {
     *          ...
     *       },
     *       "request": {
     *          ...
     *       }
     *   }
     */
    public function actionUpdate(): Response
    {
        if (!$projectId = $this->auth->au_project_id) {
            $message = 'Not found Project with current user: ' . $this->auth->au_api_username;
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($message),
                new ErrorsMessage($message),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $form = new ClientAccountUpdateApiForm();
        $form->project_id = $projectId;

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            if (!$clientAccount = ClientAccount::findOne(['ca_uuid' => $form->uuid])) {
                throw new NotFoundHttpException('Not found ClientAccount from uuid (' . $form->uuid . ')');
            }
            $clientAccount = $this->clientAccountRepository->fillForUpdate($clientAccount, $form);
            $clientAccount = $this->clientAccountRepository->save($clientAccount);

            $this->clientManageService->createOrLinkByClientAccount($clientAccount);

            return new SuccessResponse(
                new DataMessage(
                    new Message('ClientAccount updated successfully', $clientAccount->ca_id)
                )
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'ClientAccountController:actionUpdate:Throwable');
            return new ErrorResponse(
                new MessageMessage($throwable->getMessage()),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    private static function prepareGetResult(array $result): array
    {
        unset(
            $result['ca_id'],
            $result['ca_project_id'],
            $result['ca_created_dt'],
            $result['ca_updated_dt']
        );
        return $result;
    }
}
