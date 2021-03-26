<?php

namespace webapi\src\logger\behaviors;

use sales\helpers\app\AppHelper;
use Throwable;
use webapi\src\logger\behaviors\filters\Filterable;
use Yii;
use webapi\src\logger\EndDTO;
use webapi\src\logger\StartDTO;
use yii\base\Action;
use yii\base\ActionEvent;
use yii\base\Request;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\IdentityInterface;

/**
 * Class SimpleLoggerBehavior
 *
 * @property Filterable $filter
 */
class SimpleLoggerBehavior extends LoggerBehavior
{
    public $filter;

    public function events(): array
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }

    public function beforeAction(ActionEvent $event): bool
    {
        if ($this->isDisabled($event->action)) {
            return $event->isValid;
        }

        if (!$logger = $this->checkLogger($event->action->controller)) {
            return $event->isValid;
        }

        /** @var Action $action */
        $action = $event->action;

        /** @var Request $request */
        $request = Yii::$app->request;

        /** @var IdentityInterface $user */
        $user = Yii::$app->user;

        try {
            $data = Json::encode($this->filterData($request->post()));
            $logger->start(
                new StartDTO([
                    'data' => $data,
                    'action' => $action->uniqueId,
                    'userId' => $user->getId(),
                    'ip' => $request->getRemoteIP(),
                    'startTime' => microtime(true),
                    'startMemory' => memory_get_usage(),
                ])
            );
        } catch (\Throwable $throwable) {
            $log = AppHelper::throwableLog($throwable);
            $log['action'] = $action->uniqueId;
            $log['userId'] = $user->getId();
            $log['ip'] = $request->getRemoteIP();
            Yii::error($log, 'SimpleLoggerBehavior:beforeAction:logger');
        }
        return $event->isValid;
    }

    public function afterAction(ActionEvent $event)
    {
        if ($this->isDisabled($event->action)) {
            return $event->result;
        }

        $result = $event->result;

        if (!$logger = $this->checkLogger($event->action->controller)) {
            return $result;
        }

        if (!$result instanceof \webapi\src\response\Response) {
            Yii::error('Response must be instanceof ' . \webapi\src\response\Response::class, 'SimpleLoggerBehavior');
            return $result;
        }

        try {
            $encodeResponse = Json::encode($result->getResponse());
            $logger->end(
                new EndDTO([
                    'result' => $encodeResponse,
                    'endTime' => microtime(true),
                    'endMemory' => memory_get_usage(),
                    'profiling' => Yii::getLogger()->getDbProfiling(),
                ])
            );
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'SimpleLoggerBehavior:afterAction:logger');
        }
        return $result;
    }

    protected function filterData($data)
    {
        if ($this->filter === null) {
            return $data;
        }

        try {
            $filter = Yii::createObject($this->filter);
            if ($filter instanceof Filterable) {
                return $filter->filterData($data);
            }
        } catch (Throwable $e) {
            Yii::error('Error create filter. ' . $e->getMessage(), 'SimpleLoggerBehavior');
        }

        return $data;
    }
}
