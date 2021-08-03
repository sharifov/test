<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\ReprotectionCreateJob;
use modules\flight\models\FlightRequest;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\useCases\reprotectionCreate\form\ReprotectionCreateForm;
use modules\flight\src\useCases\reprotectionCreate\form\ReprotectionGetForm;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\logger\behaviors\TechnicalInfoBehavior;
use webapi\src\Messages;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\behaviors\ResponseStatusCodeBehavior;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\messages\StatusFailedMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FlightController
 *
 * @property TransactionManager $transactionManager
 * @property-read ProductQuoteRepository $productQuoteRepository
 */
class FlightController extends BaseController
{
    private TransactionManager $transactionManager;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param TransactionManager $transactionManager
     * @param ProductQuoteRepository $productQuoteRepository
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        TransactionManager $transactionManager,
        ProductQuoteRepository $productQuoteRepository,
        $config = []
    ) {
        $this->transactionManager = $transactionManager;
        $this->productQuoteRepository = $productQuoteRepository;
        parent::__construct($id, $module, $logger, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'except' => ['reprotection-get'],
        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'except' => ['reprotection-get'],
        ];
        $behaviors['responseStatusCode'] = [
            'class' => ResponseStatusCodeBehavior::class,
            'except' => ['reprotection-get'],
        ];
        $behaviors['technical'] = [
            'class' => TechnicalInfoBehavior::class,
            'except' => ['reprotection-get'],
        ];
        return $behaviors;
    }

    /**
     * @api {post} /v2/flight/reprotection-create Create flight reprotection from BO
     * @apiVersion 0.1.0
     * @apiName ReProtection Create
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{10}}           booking_id                  Booking Id
     * @apiParam {bool}                 [is_automate]               Is automate (default false)
     * @apiParam {object}               [flight_quote]              Flight quote
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "booking_id": "XXXYYYZ",
     *      "is_automate": false,
     *      "flight_quote": {} // TODO::
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *            "resultMessage": "FlightRequest created",
     *            "id" => 12345
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
     *        "message": "FlightRequest save is failed.",
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
    public function actionReprotectionCreate()
    {
        $post = Yii::$app->request->post();
        $reprotectionCreateForm = new ReprotectionCreateForm();

        if (!$reprotectionCreateForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$reprotectionCreateForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($reprotectionCreateForm->getErrors()),
            );
        }

        try {
            $apiUserId = $this->auth->getId();
            $resultId = $this->transactionManager->wrap(function () use ($reprotectionCreateForm, $apiUserId) {
                $flightRequest = FlightRequest::create(
                    $reprotectionCreateForm->booking_id,
                    FlightRequest::TYPE_REPRODUCTION_CREATE,
                    FlightRequest::STATUS_PENDING,
                    $reprotectionCreateForm->getAttributes(),
                    $apiUserId
                );
                $flightRequest = (new FlightRequestRepository())->save($flightRequest);

                $job = new ReprotectionCreateJob();
                $job->flight_request_id = $flightRequest->fr_id;
                $jobId = Yii::$app->queue_job->priority(100)->push($job);

                $flightRequest->fr_job_id = $jobId;
                (new FlightRequestRepository())->save($flightRequest);

                return $flightRequest->fr_id;
            });
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightController:actionReprotectionCreate:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('FlightRequest save is failed. ' . $throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage([
                'resultMessage' => 'FlightRequest created',
                'id' => $resultId,
            ])
        );
    }

    public function actionReprotectionGet()
    {
        $form = new ReprotectionGetForm();

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
            );
        }

        try {
            $productQuote = $this->productQuoteRepository->findByGidFlightProductQuote($form->flight_product_quote_gid);

            $originProductQuote = $productQuote->relateParent ? $productQuote->relateParent->serialize() : [];

            $order = $productQuote->pqOrder;

            $orderSerialized = $order ? $order->serialize() : [];
            if ($orderSerialized && array_key_exists('quotes', $orderSerialized)) {
                unset($orderSerialized['quotes']);
            }

            $orderContacts = [];
            foreach ($order->orderContacts ?? [] as $orderContact) {
                $orderContacts[] = $orderContact->serialize();
            }

            return new SuccessResponse(
                new Message('origin_product_quote', $originProductQuote),
                new Message('reprotection_product_quote', $productQuote->serialize()),
                new Message('order', $orderSerialized),
                new Message('order_contacts', $orderContacts)
            );
        } catch (NotFoundException | \RuntimeException $e) {
            return new ErrorResponse(
                new StatusFailedMessage(),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'API:FlightController:actionReprotectionCreate:Throwable');
            return new ErrorResponse(
                new ErrorsMessage('Internal Server Error'),
                new CodeMessage($e->getCode())
            );
        }
    }
}
