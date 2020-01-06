<?php

namespace webapi\modules\v2\controllers;

use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\filters\CreditCardFilter;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\request\RequestBo;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusFailedMessage;
use webapi\src\response\ProxyResponse;
use Yii;
use yii\httpclient\Response;

/**
 * Class OrderController
 *
 * @property RequestBo $request
 */
class OrderController extends BaseController
{
    private $request;

    public function __construct($id, $module, ApiLogger $logger, RequestBo $request, $config = [])
    {
        parent::__construct($id, $module, $logger, $config);
        $this->request = $request;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'filter' => CreditCardFilter::class,
        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'filter' => CreditCardFilter::class,
        ];
        return $behaviors;
    }

    public function actionCreate()
    {
        $data = Yii::$app->request->post();

        try {
            /** @var Response $response */
            if ($this->isClickToBook($data)) {
                $response = $this->request->sendClickToBook($data);
            } else {
                $response = $this->request->sendPhoneToBook($data);
            }
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new StatusFailedMessage(),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        $result = new ProxyResponse($response);
        $result->sort('status', 'message');
        return $result;
    }

    private function isClickToBook($data): bool
    {
        return isset($data['FlightRequest']);
    }
}
