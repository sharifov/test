<?php

namespace webapi\modules\v1\controllers;

use sales\model\client\ClientCodeException;
use webapi\src\Messages;
use webapi\src\response\messages\ErrorsMessage;
use Yii;
use common\models\Client;
use common\models\Project;
use sales\model\client\useCase\info\ClientForm;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;

/**
 * Class ClientController
 */
class ClientController extends ApiBaseController
{
    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['info'] = ['GET'];

        return $verbs;
    }

    /**
     * @api {get} /v1/client/info Client Info
     * @apiVersion 0.1.0
     * @apiName Client
     * @apiGroup Client
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}           client_uuid                            Client UUID
     * @apiParam {string}           project_key                            Project key
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *      "client_uuid": "af5241f1-094f-4fde-ada3-bd72986216f0",
     *      "project_key": "ovago"
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "first_name": "Client first name",
     *          "last_name": "Client last name",
     *          "created": "2020-09-24 11:29:15"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 200 OK
     *
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "client_uuid": [
     *             "Client Uuid cannot be blank."
     *        ],
     *        "project_key": [
     *             "Project Key is invalid."
     *         ]
     *     },
     *     "code": "11602"
     * }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 200 OK
     *
     * {
     *     "status": 400,
     *     "message": "Load data error",
     *     "errors": {
     *          "Not found Client data on request"
     *     },
     *     "code": "11601"
     * }
     *
     * @apiErrorExample {json} Error-Response (404):
     *
     * HTTP/1.1 200 OK
     *
     * {
     *     "status": 404,
     *     "message": "Client not found",
     *     "code": "11100",
     *     "errors": []
     * }
     */
    public function actionInfo(): Response
    {
        $form = new ClientForm();

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Client data on request'),
                new CodeMessage(ClientCodeException::API_CLIENT_INFO_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(ClientCodeException::API_CLIENT_INFO_VALIDATE)
            );
        }

        $client = Client::find()
            ->select(['first_name', 'last_name', 'created'])
            ->andWhere(['uuid' => $form->client_uuid])
            ->innerJoin(Project::tableName(), Project::tableName() . '.id = cl_project_id and project_key = :key', [':key' => $form->project_key])
            ->asArray()
            ->one();

        if (!$client) {
            return new ErrorResponse(
                new StatusCodeMessage(404),
                new MessageMessage('Client not found'),
                new CodeMessage(ClientCodeException::CLIENT_NOT_FOUND)
            );
        }

        return new SuccessResponse(
            new StatusCodeMessage(200),
            new DataMessage([
                'first_name' => $client['first_name'],
                'last_name' => $client['last_name'],
                'created' => $client['created'],
            ])
        );
    }
}
