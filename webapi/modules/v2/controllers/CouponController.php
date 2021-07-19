<?php

namespace webapi\modules\v2\controllers;

use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\model\coupon\entity\coupon\Coupon;
use sales\model\coupon\entity\coupon\repository\CouponRepository;
use sales\model\coupon\entity\coupon\serializer\CouponSerializer;
use sales\model\coupon\entity\coupon\service\CouponService;
use sales\model\coupon\entity\couponUse\CouponUse;
use sales\model\coupon\entity\couponUse\repository\CouponUseRepository;
use sales\model\coupon\entity\couponUserAction\CouponUserAction;
use sales\model\coupon\entity\couponUserAction\repository\CouponUserActionRepository;
use sales\model\coupon\useCase\apiCreate\CouponApiCreateService;
use sales\model\coupon\useCase\apiCreate\CouponCreateForm;
use sales\model\coupon\useCase\apiEdit\CouponApiEditService;
use sales\model\coupon\useCase\apiEdit\CouponEditForm;
use sales\model\coupon\useCase\apiInfo\CouponInfoForm;
use sales\model\coupon\useCase\apiUse\CouponUseForm;
use sales\model\coupon\useCase\apiValidate\CouponValidateForm;
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
     * @apiParam {int}                          [percent]                   Percent
     * @apiParam {bool}                         [reusable]                  Reusable
     * @apiParam {int}                          [reusableCount]             Reusable Count
     * @apiParam {string{format yyyy-mm-dd}}    [startDate]                 Start Date
     * @apiParam {string{format yyyy-mm-dd}}    [expirationDate]            Expiration Date
     * @apiParam {bool}                         [public]                    Public
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
     *            "coupon": {
                    "c_status_id": 1,
                    "c_type_id": 1,
                    "c_code": "KLCVZWDZGCCNFJE",
                    "c_amount": 25,
                    "c_currency_code": "USD",
                    "c_public": false,
                    "c_reusable": false,
                    "c_reusable_count": 3,
                    "c_percent": 0,
                    "c_created_dt": "2021-07-16 08:37:02",
                    "startDate": "2021-06-20",
                    "expDate": "2022-07-16",
                    "statusName": "Send",
                    "typeName": "Voucher"
                },
                "serviceResponse": {
                    "dec_coupon": "",
                    "enc_coupon": "KLCVZWDZGCCNFJE",
                    "exp_date": "2022-07-16",
                    "amount": 25,
                    "currency": "USD",
                    "public": false,
                    "reusable": false,
                    "valid": true
                },
                "warning": [
                    "Input param \"reusable\" (1) rewritten by result service (0)",
                    "Input param \"expirationDate\" (2021-12-25) rewritten by result service (2022-07-16)"
                ]
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
            $couponAirSearch = CouponApiCreateService::requestCouponFromAirSearch($couponCreateForm->getAmountCurrencyCode());
            $couponForm = new CouponForm();
            if (!$couponForm->load($couponAirSearch)) {
                throw new \DomainException('CouponForm not loaded');
            }
            if (!$couponForm->validate()) {
                throw new \DomainException(ErrorsToStringHelper::extractFromModel($couponForm));
            }

            $apiUserId = $this->auth->getId();
            $couponCreated = $this->transactionManager->wrap(static function () use ($couponCreateForm, $couponForm, $apiUserId) {
                $coupon = CouponApiCreateService::createFromAirSearch($couponCreateForm, $couponForm);
                $coupon = (new CouponRepository($coupon))->save()->getModel();

                $couponUserAction = CouponUserAction::create(
                    $coupon->c_id,
                    CouponUserAction::ACTION_CREATE,
                    $apiUserId,
                    null
                );
                (new CouponUserActionRepository($couponUserAction))->save();
                return $coupon;
            });

            $dataMessage['coupon'] = $couponCreated->serialize();
            $dataMessage['serviceResponse'] = $couponAirSearch;
            if (!empty($couponCreateForm->public) && ((bool) $couponCreateForm->public !== (bool) $couponForm->public)) {
                $dataMessage['warning'][] =
                    'Input param "public" (' . (int) $couponCreateForm->public . ') rewritten by result service (' . (int) $couponForm->public . ')';
            }
            if (!empty($couponCreateForm->reusable) && ((bool) $couponCreateForm->reusable !== (bool) $couponForm->reusable)) {
                $dataMessage['warning'][] =
                    'Input param "reusable" (' . (int) $couponCreateForm->reusable . ') rewritten by result service (' . (int) $couponForm->reusable . ')';
            }
            if (!empty($couponCreateForm->expirationDate)) {
                $inputExpirationDate = date('Y-m-d', strtotime($couponCreateForm->expirationDate));
                $serviceExpirationDate = date('Y-m-d', strtotime($couponForm->exp_date));
                if ($inputExpirationDate !== $serviceExpirationDate) {
                    $dataMessage['warning'][] =
                        'Input param "expirationDate" (' . $inputExpirationDate . ') rewritten by result service (' . $serviceExpirationDate . ')';
                }
            }
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
            new DataMessage($dataMessage)
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
     *          "result": true,
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

            $apiUserId = $this->auth->getId();
            $couponUsed = $this->transactionManager->wrap(static function () use ($coupon, $couponUseForm, $apiUserId) {
                $couponUse = CouponUse::create($coupon->c_id, $couponUseForm->clientIp, $couponUseForm->clientUserAgent);
                (new CouponUseRepository($couponUse))->save();

                $coupon->usedCountIncrement();

                if (CouponService::checkChangeStatusToProgress($coupon)) {
                    $coupon->statusInProgress();
                }
                if (CouponService::checkChangeStatusToUse($coupon)) {
                    $coupon->statusUsed();
                }
                $coupon = (new CouponRepository($coupon))->save()->getModel();

                $couponUserAction = CouponUserAction::create(
                    $coupon->c_id,
                    CouponUserAction::ACTION_USE,
                    $apiUserId,
                    null
                );
                (new CouponUserActionRepository($couponUserAction))->save();
                return $coupon;
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

    /**
     * @api {post} /v2/coupon/edit Coupon edit
     * @apiVersion 0.1.0
     * @apiName Coupon edit
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
     * @apiParam {string{format yyyy-mm-dd}}    [c_start_date]       Start Date
     * @apiParam {string{format yyyy-mm-dd}}    [c_exp_date]         Expiration Date
     * @apiParam {bool}                         [c_disabled]         Disabled
     * @apiParam {bool}                         [c_public]           Public
     * @apiValidate Note: at least one parameter is required (c_start_date,c_exp_date,c_disabled)
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "code": "D2EYEWH64BDGD3Y",
     *      "c_disabled": false,
     *      "c_public": false,
     *      "c_start_date": "2021-07-15",
     *      "c_exp_date": "2021-07-20"
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
     *              "c_code": "HPCCZH68PNQB5FY",
     *              "c_amount": "25.00",
     *              "c_currency_code": "USD",
     *              "c_percent": null,
     *              "c_reusable": 1,
     *              "c_reusable_count": 1,
     *              "c_public": 0,
     *              "c_status_id": 2,
     *              "c_disabled": null,
     *              "c_type_id": 1,
     *              "c_created_dt": "2021-07-12 07:16:25",
     *              "c_used_count": 0,
     *              "startDate": null,
     *              "expDate": "2022-08-12",
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
     *
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
    public function actionEdit()
    {
        $post = Yii::$app->request->post();
        $couponEditForm = new CouponEditForm();

        if (!$couponEditForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$couponEditForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($couponEditForm->getErrors()),
            );
        }

        try {
            if (!$coupon = Coupon::findOne(['c_code' => $couponEditForm->code])) {
                throw new \DomainException('Coupon not found');
            }

            $apiUserId = $this->auth->getId();
            $couponUpdated = $this->transactionManager->wrap(static function () use ($coupon, $couponEditForm, $apiUserId) {
                $coupon = CouponApiEditService::editFromApiForm($coupon, $couponEditForm);
                $coupon = (new CouponRepository($coupon))->save()->getModel();

                $couponUserAction = CouponUserAction::create(
                    $coupon->c_id,
                    CouponUserAction::ACTION_UPDATE,
                    $apiUserId,
                    null
                );
                (new CouponUserActionRepository($couponUserAction))->save();
                return $coupon;
            });
        } catch (\Throwable $throwable) {
            \Yii::error(
                ['throwable' => AppHelper::throwableLog($throwable), 'post' => $post],
                'CouponController:actionEdit:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage([
                'coupon' => $couponUpdated->serialize(),
            ])
        );
    }
}
