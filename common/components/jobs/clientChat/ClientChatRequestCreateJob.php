<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use sales\repositories\NotFoundException;
use yii\queue\JobInterface;
use Yii;

/**
 * @property int $requestId
 * @property string $requestEventClass
 */
class ClientChatRequestCreateJob implements JobInterface
{
    public int $requestId;

    public string $requestEventClass;

    public function execute($queue): void
    {
        try {
            if (!$request = ClientChatRequest::find()->andWhere(['ccr_id' => $this->requestId])->one()) {
                throw new NotFoundException('Not found ClientChatRequest ID: ' . $this->requestId);
            }
            /** @var ChatRequestEvent $requestEvent */
            $requestEvent = Yii::createObject($this->requestEventClass);
            $requestEvent->process($request);
        } catch (\RuntimeException | \DomainException | NotFoundException $e) {
        } catch (\Throwable $e) {
            AppHelper::throwableLogger($e, 'ClientChatRequestCreateJob:Execute:Throwable', false);
        }
    }
}
