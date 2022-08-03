<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\model\clientChatRequest\useCase\api\create\ClientChatFormResponseApiForm;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * Class ClientChatFormResponseJob
 *
 * @property ClientChatFormResponseApiForm $form
 * @package common\components\jobs\clientChat
 */
class ClientChatFormResponseJob extends BaseJob implements JobInterface
{
    public $form;

    /**
     * @param Queue $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();
        try {
            $service = Yii::createObject(ClientChatRequestService::class);
            $service->createOrUpdateFormRequest($this->form);
        } catch (\Throwable $e) {
            AppHelper::throwableLogger($e, 'ClientChatFormRequestJob:Execute:Throwable', false);
        }
    }
}
