<?php

namespace webapi\modules\v2\controllers;

use sales\helpers\app\AppHelper;
use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\repository\CouponRepository;
use sales\model\coupon\entity\coupon\serializer\CouponSerializer;
use sales\model\coupon\entity\coupon\service\CouponService;
use sales\model\coupon\entity\couponUse\CouponUse;
use sales\model\coupon\entity\couponUse\repository\CouponUseRepository;
use sales\model\coupon\useCase\apiCreate\CouponApiCreateService;
use sales\model\coupon\useCase\apiCreate\CouponCreateForm;
use sales\model\coupon\useCase\apiInfo\CouponInfoForm;
use sales\model\coupon\useCase\apiUse\CouponUseForm;
use sales\model\coupon\useCase\apiValidate\CouponValidateForm;
use sales\services\TransactionManager;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
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
                $coupon = (new CouponRepository($coupon))->save();
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

    /**
     * @api {post} /v2/coupon/info Coupon info
     * @apiVersion 0.1.0
     * @apiName Coupon info
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
     * @apiParam {string{15}}                    code                Coupon Code
     *
     * @apiParamExample {json} Request-Example:
     *   {
            "code": "D2EYEWH64BDGD3Y"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *           "coupon": {
     *              "c_id": 9,
     *              "c_code": "HPCCZH68PNQB5FY",
     *              "c_amount": "25.00",
     *              "c_currency_code": "USD",
     *              "c_percent": null,
     *              "c_exp_date": "2022-07-12 00:00:00",
     *              "c_start_date": null,
     *              "c_reusable": 0,
     *              "c_reusable_count": null,
     *              "c_public": 0,
     *              "c_status_id": 2,
     *              "c_disabled": null,
     *              "c_type_id": 1,
     *              "c_created_dt": "2021-07-12 07:16:25",
     *              "statusName": "Send",
     *              "typeName": "Voucher"
     *          }
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
     *        "message": "Coupon not found",
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
    public function actionInfo()
    {
        $post = Yii::$app->request->post();
        $couponInfoForm = new CouponInfoForm();

        if (!$couponInfoForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$couponInfoForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($couponInfoForm->getErrors()),
            );
        }

        try {
            if (!$coupon = Coupon::findOne(['c_code' => $couponInfoForm->code])) {
                throw new \DomainException('Coupon not found');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                ['throwable' => AppHelper::throwableLog($throwable), 'post' => $post],
                'CouponController:actionInfo:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage([
                'coupon' => $coupon->serialize(),
            ])
        );
    }

    /**
     * @api {post} /v2/coupon/validate Coupon validate
     * @apiVersion 0.1.0
     * @apiName Coupon validate
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
     * @apiParam {string{15}}                    code                Coupon Code
     *
     * @apiParamExample {json} Request-Example:
     *   {
            "code": "D2EYEWH64BDGD3Y"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *           "isValid": true,
     *           "couponInfo": {
     *               "c_reusable": 1,
     *               "c_reusable_count": 5,
     *               "c_disabled": 0,
     *               "c_used_count": 0,
     *               "startDate": "2021-07-14",
     *               "expDate": "2021-12-25",
     *               "statusName": "New"
     *           }
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
     *        "message": "Coupon not found",
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
    public function actionValidate()
    {
        $post = Yii::$app->request->post();
        $couponValidateForm = new CouponValidateForm();

        if (!$couponValidateForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$couponValidateForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($couponValidateForm->getErrors()),
            );
        }

        $exceptFields = [
            'c_code', 'c_amount', 'c_currency_code', 'c_percent', 'c_public',
            'c_status_id', 'c_type_id', 'c_created_dt', 'typeName',
        ];

        try {
            $couponInfo = [];
            $isValid = CouponService::checkIsValid($couponValidateForm->code);
            if ($coupon = Coupon::findOne(['c_code' => $couponValidateForm->code])) {
                $couponInfo = (new CouponSerializer($coupon))->getDataExcept($exceptFields);
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                ['throwable' => AppHelper::throwableLog($throwable), 'post' => $post],
                'CouponController:actionValidate:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage([
                'isValid' => $isValid,
                'couponInfo' => $couponInfo,
            ])
        );
    }

    /**
     * @api {post} /v2/coupon/use Coupon use
     * @apiVersion 0.1.0
     * @apiName Coupon use
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
     * @apiParam {string{15}}                    code                Coupon Code
     * @apiParam {string{40}}                    [clientIp]          Client Ip
     * @apiParam {string{500}}                   [clientUserAgent]   Client UserAgent
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "code": "D2EYEWH64BDGD3Y",
     *      "clientIp": "127.0.0.1",
     *      "clientUserAgent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *          "status": "Used",
     *          "couponInfo": {
     *               "c_reusable": 1,
     *               "c_reusable_count": 5,
     *               "c_disabled": 0,
     *               "c_used_count": 0,
     *               "startDate": "2021-07-14",
     *               "expDate": "2021-12-25",
     *               "statusName": "Used"
     *           }
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
     *        "message": "Coupon not found",
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
    public function actionUse()
    {
        $post = Yii::$app->request->post();
        $couponUseForm = new CouponUseForm();

        if (!$couponUseForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$couponUseForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($couponUseForm->getErrors()),
            );
        }

        $exceptFields = [
            'c_code', 'c_amount', 'c_currency_code', 'c_percent', 'c_public',
            'c_status_id', 'c_type_id', 'c_created_dt', 'typeName',
        ];

        try {
            if (!$isValid = CouponService::checkIsValid($couponUseForm->code)) {
                throw new \DomainException('Coupon is not valid');
            }
            if (!$coupon = Coupon::findOne(['c_code' => $couponUseForm->code])) {
                throw new \DomainException('Coupon not found by code (' . $couponUseForm->code . ')');
            }

            $couponUsed = $this->transactionManager->wrap(static function () use ($coupon, $couponUseForm) {
                $couponUse = CouponUse::create($coupon->c_id, $couponUseForm->clientIp, $couponUseForm->clientUserAgent);
                (new CouponUseRepository($couponUse))->save();

                $coupon->usedCountIncrement();

                if (CouponService::checkChangeStatusToProgress($coupon)) {
                    $coupon->statusInProgress();
                }
                if (CouponService::checkChangeStatusToUse($coupon)) {
                    $coupon->statusUsed();
                }
                return (new CouponRepository($coupon))->save();
            });

            $couponInfo = (new CouponSerializer($couponUsed))->getDataExcept($exceptFields);
        } catch (\Throwable $throwable) {
            \Yii::error(
                ['throwable' => AppHelper::throwableLog($throwable), 'post' => $post],
                'CouponController:actionUse:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage([
                'result' => true,
                'couponInfo' => $couponInfo,
            ])
        );
    }
}
