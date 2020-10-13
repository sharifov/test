<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use sales\repositories\NotFoundException;
use yii\queue\JobInterface;
use Yii;

/**
 * @property int $requestId
 */
class ClientChatRequestCreateJob implements JobInterface
{
    public int $requestId;

    public function execute($queue): void
    {
        try {
            if (!$request = ClientChatRequest::find()->andWhere(['ccr_id' => $this->requestId])->one()) {
                throw new NotFoundException('Not found ClientChatRequest ID: ' . $this->requestId);
            }
            $service = Yii::createObject(ClientChatRequestService::class);
            $service->processRequest($request);
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'ClientChatRequestCreateJob:Execute:Throwable', false);
        }
    }
}
