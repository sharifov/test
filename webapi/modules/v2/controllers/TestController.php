<?php

namespace webapi\modules\v2\controllers;

use webapi\src\logger\ApiLogger;
use webapi\src\request\RequestBo;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\ProxyResponse;
use yii\httpclient\Response;

/**
 * Class TestController
 *
 * @property RequestBo $request
 */
class TestController extends BaseController
{
    private $request;

    public function __construct($id, $module, ApiLogger $logger, RequestBo $request, $config = [])
    {
        parent::__construct($id, $module, $logger, $config);
        $this->request = $request;
    }
//
//    public function behaviors(): array
//    {
//        $behaviors = parent::behaviors();
//        unset($behaviors['authenticator'], $behaviors['access']);
////        $behaviors['logger'] = ['class' => SimpleLoggerBehavior::class];
//        $behaviors['technical'] = array_merge($behaviors['technical'], ['only' => ['test']]);
//        return $behaviors;
//    }

    protected function verbs(): array
    {
        return ['test' => ['GET', 'POST']];
    }

//    public function actionTest(): \webapi\src\response\Response
//    {
//        $data = \Yii::$app->request->post();
//
//        try {
//            /** @var Response $response */
//        $response = $this->request->sendClickToBook($data);
//        } catch (\Throwable $e) {
//            return new ErrorResponse(
//                new MessageMessage($e->getMessage()),
//                new ErrorsMessage($e->getMessage()),
//                new CodeMessage($e->getCode())
//            );
//        }
//
//        $result = new ProxyResponse($response);
//        $result->sort('status', 'message');
//        return $result;
//    }
}
