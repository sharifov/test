<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use src\repositories\NotFoundException;
use yii\queue\JobInterface;
use Yii;

/**
 * @property int $requestId
 * @property string $requestEventClass
 */
class ClientChatRequestCreateJob extends BaseJob implements JobInterface
{
    public int $requestId;

    public string $requestEventClass;

    public function execute($queue): void
    {
        $this->waitingTimeRegister();
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
