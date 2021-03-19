<?php

namespace common\components\jobs\clientChat;

use sales\helpers\app\AppHelper;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use sales\repositories\NotFoundException;
use Yii;
use yii\queue\JobInterface;

/**
 * Class ClientChatGuestDisconnectedJob
 * @package common\components\jobs\clientChat
 *
 * @property ClientChatRequest $request
 * @property string $requestEventClass
 */
class ClientChatGuestDisconnectedJob implements JobInterface
{
    public ClientChatRequest $request;

    public string $requestEventClass;

    public function execute($queue): void
    {
        try {
            /** @var ChatRequestEvent $requestEvent */
            $requestEvent = Yii::createObject($this->requestEventClass);
            $requestEvent->process($this->request);
        } catch (\RuntimeException | \DomainException | NotFoundException $e) {
        } catch (\Throwable $e) {
            AppHelper::throwableLogger($e, 'ClientChatRequestCreateJob:Execute:Throwable', false);
        }
    }
}
