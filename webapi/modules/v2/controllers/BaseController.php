<?php

namespace webapi\modules\v2\controllers;

use webapi\src\behaviors\HttpBasicAuth;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\logger\behaviors\TechnicalInfoBehavior;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\behaviors\ResponseStatusCodeBehavior;
use webapi\src\logger\ApiLogger;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Class BaseController
 *
 * @property ApiLogger $logger
 */
class BaseController extends Controller
{
    private $logger;

    public function __construct($id, $module, ApiLogger $logger, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->logger = $logger;
    }

    public function getLogger(): ApiLogger
    {
        return $this->logger;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['authenticator'] = ['class' => HttpBasicAuth::class];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        $behaviors['logger'] = ['class' => SimpleLoggerBehavior::class];
        $behaviors['technical'] = ['class' => TechnicalInfoBehavior::class];
        $behaviors['request'] = ['class' => RequestBehavior::class];
        $behaviors['responseStatusCode'] = ['class' => ResponseStatusCodeBehavior::class];
        return $behaviors;
    }

    protected function verbs(): array
    {
        return ['*' => ['POST']];
    }
}
