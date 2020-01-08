<?php

namespace webapi\src\response\behaviors;

use webapi\src\behaviors\BaseBehavior;
use webapi\src\logger\behaviors\filters\Filterable;
use webapi\src\response\messages\RequestMessage;
use Yii;
use yii\base\ActionEvent;
use yii\rest\Controller;

/**
 * Class RequestBehavior
 *
 * @property Filterable $filter
 */
class RequestBehavior extends BaseBehavior
{
    public $filter;

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
            Yii::error('Response must be instanceof ' . \webapi\src\response\Response::class, 'RequestBehavior');
            return $result;
        }

        $result->addMessage(new RequestMessage($this->filterData(Yii::$app->request->post())));

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
        } catch (\Throwable $e) {
            Yii::error('Error create filter. ' . $e->getMessage(), 'RequestBehavior');
        }

        return $data;
    }
}
