<?php

namespace webapi\modules\v2\controllers;

use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\model\coupon\entity\coupon\repository\CouponRepository;
use sales\model\coupon\useCase\apiCreate\CouponApiCreateService;
use sales\model\coupon\useCase\apiCreate\CouponCreateForm;
use sales\model\coupon\useCase\request\CouponForm;
use sales\services\TransactionManager;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use webapi\src\services\payment\PaymentManageApiService;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CouponController
 *
 * @property TransactionManager $transactionManager
 */
class CouponController extends BaseController
{
    private TransactionManager $transactionManager;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param TransactionManager $transactionManager
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        TransactionManager $transactionManager,
        $config = []
    ) {
        $this->transactionManager = $transactionManager;
        parent::__construct($id, $module, $logger, $config);
    }

    /**
     * @api {post} /v2/coupon/create Create coupon
     * @apiVersion 0.1.0
     * @apiName Create coupon
     * @apiGroup Coupon
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {int}                          amount                      Amount
     * @apiParam {string{3}}                    currencyCode                Currency Code (USD)
     * @apiParam {int}                          [percent]                   Percent (required if amount is empty)
     * @apiParam {bool}                         [reusable]                  Reusable (default false)
     * @apiParam {int}                          [reusableCount]             Reusable Count
     * @apiParam {string{format yyyy-mm-dd}}    [startDate]                 Start Date
     * @apiParam {string{format yyyy-mm-dd}}    [expirationDate]            Expiration Date
     * @apiParam {bool}                         [public]                    Public (default false)
     *
     * @apiParamExample {json} Request-Example:
     *   {
            "amount": 25,
            "currencyCode": "USD",
            "percent": "",
            "reusableCount": 3,
            "startDate": "2021-12-20",
            "expirationDate": "2021-12-25"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *            "code": "D2EYEWH64BDGD3Y"
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *        "status": 400,
     *        "message": "Coupon create is failed",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *.
     * @apiErrorExample {json} Error-Response (500):
     * HTTP/1.1 500 Internal Server Error
     * {
     *        "status": "Failed",
     *        "source": {
     *            "type": 1,
     *            "status": 500
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 422 Unprocessable entity
     * {
     *        "status": "Failed",
     *        "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
     *        "errors": [
     *              "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
     *        ],
     *        "code": 0,
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $couponCreateForm = new CouponCreateForm();

        if (!$couponCreateForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$couponCreateForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($couponCreateForm->getErrors()),
            );
        }

        try {
            $code = CouponApiCreateService::getCodeFromAirSearch($couponCreateForm);
            $couponCode = $this->transactionManager->wrap(static function () use ($couponCreateForm, $code) {
                $coupon = CouponApiCreateService::createFromApiForm($couponCreateForm, $code);
                $coupon = (new CouponRepository())->save($coupon);
                return $coupon->c_code;
            });
        } catch (\Throwable $throwable) {
            \Yii::error(
                ['throwable' => AppHelper::throwableLog($throwable), 'post' => $post],
                'CouponController:actionCreate:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Coupon create is failed. ' . $throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage([
                'code' => $couponCode,
            ])
        );
    }
}
