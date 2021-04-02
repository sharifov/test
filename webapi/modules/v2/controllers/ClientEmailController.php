<?php

namespace webapi\modules\v2\controllers;

use common\models\EmailUnsubscribe;
use sales\forms\api\clientEmail\SubscribeForm;
use sales\forms\api\clientEmail\UnSubscribeForm;
use sales\helpers\app\AppHelper;
use sales\repositories\emailUnsubscribe\EmailUnsubscribeRepository;
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
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use Yii;

/**
 * Class ClientEmailController
 */
class ClientEmailController extends BaseController
{

    /**
     * @api {post} /v2/client-email/subscribe Client Email Subscribe
     * @apiVersion 0.2.0
     * @apiName Client Email Subscribe
     * @apiGroup ClientEmail
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeader {string} If-Modified-Since  Format <code> day-name, day month year hour:minute:second GMT</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     * @apiHeaderExample {json} Header-Example (If-Modified-Since):
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate",
     *      "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT"
     *  }
     *
     * @apiParam {string{160}}           email                    Email
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "email" : "example@email.com"
     *      },
     *      "technical": {
     *          "action": "v2/client-email/subscribe",
     *          "response_id": 11926631,
     *          "request_dt": "2020-03-16 11:26:34",
     *          "response_dt": "2020-03-16 11:26:34",
     *          "execution_time": 0.076,
     *          "memory_usage": 506728
     *      },
     *      "request": []
     *  }
     *
     *
     * @apiSuccessExample {json} Not Modified-Response (304):
     *
     * HTTP/1.1 304 Not Modified
     * Cache-Control: public, max-age=3600
     * Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT
     *
     * @apiErrorExample {json} Error-Response (405):
     *
     * HTTP/1.1 405 Method Not Allowed
     *   {
     *       "name": "Method Not Allowed",
     *       "message": "Method Not Allowed. This URL can only handle the following request methods: GET.",
     *       "code": 0,
     *       "status": 405,
     *       "type": "yii\\web\\MethodNotAllowedHttpException"
     *   }
     *
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     *   {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "email": [
     *               "Contact Email cannot be blank."
     *           ]
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

    /**
     * @return Response
     */
    public function actionSubscribe(): Response
    {
        if (!$this->auth->au_project_id) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new ErrorsMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $form = new SubscribeForm($this->auth->au_project_id);

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('POST data not loaded'),
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

        if (!(new EmailUnsubscribeRepository())->find($form->email, $form->project_id)) {
            return new SuccessResponse(
                new DataMessage(
                    new Message('email', 'Database entry (email : ' . $form->email . ', project : ' . $form->project_id . ') not exists'),
                )
            );
        }

        try {
            $repository = new EmailUnsubscribeRepository();
            if ($model = $repository->find($form->email, $form->project_id)) {
                $repository->remove($model);
            }
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        return new SuccessResponse(
            new DataMessage(
                new Message('email', $form->email),
                new Message('project_id', $form->project_id),
            )
        );
    }

    /**
     * @api {post} /v2/client-email/unsubscribe Client Email Unsubscribe
     * @apiVersion 0.2.0
     * @apiName Client Email Unsubscribe
     * @apiGroup ClientEmail
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeader {string} If-Modified-Since  Format <code> day-name, day month year hour:minute:second GMT</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     * @apiHeaderExample {json} Header-Example (If-Modified-Since):
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate",
     *      "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT"
     *  }
     *
     * @apiParam {string{160}}           email                    Email
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "email" : "example@email.com"
     *      },
     *      "technical": {
     *          "action": "v2/client-email/unsubscribe",
     *          "response_id": 11926631,
     *          "request_dt": "2020-03-16 11:26:34",
     *          "response_dt": "2020-03-16 11:26:34",
     *          "execution_time": 0.076,
     *          "memory_usage": 506728
     *      },
     *      "request": []
     *  }
     *
     *
     * @apiSuccessExample {json} Not Modified-Response (304):
     *
     * HTTP/1.1 304 Not Modified
     * Cache-Control: public, max-age=3600
     * Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT
     *
     * @apiErrorExample {json} Error-Response (405):
     *
     * HTTP/1.1 405 Method Not Allowed
     *   {
     *       "name": "Method Not Allowed",
     *       "message": "Method Not Allowed. This URL can only handle the following request methods: GET.",
     *       "code": 0,
     *       "status": 405,
     *       "type": "yii\\web\\MethodNotAllowedHttpException"
     *   }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     *   {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "email": [
     *               "Contact Email cannot be blank."
     *           ]
     *       },
     *       "code": "21301",
     *       "technical": {
     *          ...
     *       },
     *       "request": {
     *          ...
     *       }
     *   }
     *
     */

    /**
     * @return Response
     */
    public function actionUnsubscribe(): Response
    {
        if (!$this->auth->au_project_id) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new ErrorsMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $form = new UnSubscribeForm($this->auth->au_project_id);

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('POST data not loaded'),
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

        if ((new EmailUnsubscribeRepository())->find($form->email, $form->project_id)) {
            return new SuccessResponse(
                new DataMessage(
                    new Message('email', 'Database entry (email : ' . $form->email . ', project : ' . $form->project_id . ') already exists'),
                )
            );
        }

        try {
            $emailUnsubscribe = EmailUnsubscribe::create($form->email, $form->project_id);
            (new EmailUnsubscribeRepository())->save($emailUnsubscribe);
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        return new SuccessResponse(
            new DataMessage(
                new Message('email', $form->email),
                new Message('project_id', $form->project_id),
            )
        );
    }
}
