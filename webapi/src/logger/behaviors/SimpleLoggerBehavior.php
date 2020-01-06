<?php

namespace webapi\src\behaviors;

use Yii;
use webapi\src\logger\EndDTO;
use webapi\src\response\Response;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\StartDTO;
use yii\base\Action;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\base\Request;
use yii\rest\Controller;
use yii\web\IdentityInterface;

class SimpleLoggerBehavior extends LoggerBehavior
{
    public function events(): array
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }

    public function beforeAction(ActionEvent $event): bool
    {
        if (!$logger = $this->checkLogger($event->action->controller)) {
            return $event->isValid;
        }

        /** @var Action $action */
        $action = $event->action;

        /** @var Request $request */
        $request = Yii::$app->request;

        /** @var IdentityInterface $user */
        $user = Yii::$app->user;

        $logger->start(
            new StartDTO([
                'data' => @json_encode($request->post()),
                'action' => $action->uniqueId,
                'userId' => $user->getId(),
                'ip' => $request->getRemoteIP(),
                'startTime' => microtime(true),
                'startMemory' => memory_get_usage(),
            ])
        );

        return $event->isValid;
    }

    public function afterAction(ActionEvent $event)
    {
        $result = $event->result;

        if (!$logger = $this->checkLogger($event->action->controller)) {
            return $result;
        }

        if (!$result instanceof \webapi\src\response\Response) {
            Yii::error('Response must be instanceof ' . \webapi\src\response\Response::class, 'SimpleLoggerBehavior');
            return $result;
        }

        $logger->end(
            new EndDTO([
                'result' => @json_encode($result->getResponse()),
                'endTime' => microtime(true),
                'endMemory' => memory_get_usage(),
                'profiling' => Yii::getLogger()->getDbProfiling(),
            ])
        );

        return $result;
    }
}
