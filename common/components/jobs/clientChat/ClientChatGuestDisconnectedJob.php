<?php

namespace common\components\jobs\clientChat;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use src\repositories\NotFoundException;
use Yii;
use yii\queue\JobInterface;

/**
 * Class ClientChatGuestDisconnectedJob
 * @package common\components\jobs\clientChat
 *
 * @property ClientChatRequest $request
 * @property string $requestEventClass
 */
class ClientChatGuestDisconnectedJob extends BaseJob implements JobInterface
{
    public ClientChatRequest $request;

    public string $requestEventClass;

    public function execute($queue): void
    {
        $this->waitingTimeRegister();
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
