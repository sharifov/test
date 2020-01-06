<?php

namespace webapi\src\logger\behaviors;

use webapi\src\response\messages\TechnicalMessage;
use Yii;
use yii\base\ActionEvent;
use yii\rest\Controller;

class TechnicalInfoBehavior extends LoggerBehavior
{
    public function events(): array
    {
        return [
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
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

        $result->addMessage(New TechnicalMessage($logger->getTechnicalInfo()));

        return $result;
    }
}
