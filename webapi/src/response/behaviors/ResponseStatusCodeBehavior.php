<?php

namespace webapi\src\response\behaviors;

use webapi\src\behaviors\BaseBehavior;
use Yii;
use yii\base\ActionEvent;
use yii\rest\Controller;

/**
 * Class ResponseStatusCodeBehavior
 */
class ResponseStatusCodeBehavior extends BaseBehavior
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

        if (!$result instanceof \webapi\src\response\Response) {
            Yii::error('Response must be instanceof ' . \webapi\src\response\Response::class, 'ResponseStatusCodeBehavior');
            return $result;
        }

        Yii::$app->response->setStatusCode($result->getResponseStatusCode());

        return $result;
    }
}
