<?php

namespace webapi\src\behaviors;

use webapi\src\ApiCodeException;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\rest\Controller;

/**
 * Class CheckPostBehavior
 */
class CheckPostBehavior extends Behavior
{
    public function events(): array
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'checkPost',
        ];
    }

    public function checkPost(ActionEvent $event): ?ErrorResponse
    {
        if (!Yii::$app->request->isPost) {
            return null;
        }
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
            );
        }
        return null;
    }
}
