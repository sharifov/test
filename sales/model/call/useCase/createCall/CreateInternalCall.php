<?php

namespace sales\model\call\useCase\createCall;

use common\models\Call;
use common\models\Employee;
use sales\model\call\services\FriendlyName;
use sales\model\call\services\RecordManager;
use sales\model\voip\phoneDevice\device\ReadyVoipDevice;

class CreateInternalCall
{
    public function __invoke(Employee $createdUser, CreateCallForm $form): array
    {
        try {
            //todo: validate can created user cal to other user?

            $key = 'call_user_to_user_' . $createdUser->id;

            if ($result = \Yii::$app->cache->get($key)) {
                throw new \DomainException('Please wait ' . abs($result - time()) . ' seconds.');
            }

            if (!$user = Employee::findOne(['id' => $form->toUserId])) {
                throw new \DomainException('Not found user. Id: ' . $form->toUserId);
            }

            if (!$user->isOnline()) {
                throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is offline');
            }
            if (!$user->isCallFree()) {
                throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is occupied');
            }

            $toUserVoipDevice = (new ReadyVoipDevice())->findAny($user);

            \Yii::$app->cache->set($key, (time() + 10), 10);

            $recordingManager = RecordManager::toUser($createdUser->id);

            $result = \Yii::$app->communication->callToUser(
                $form->getVoipDevice(),
                $toUserVoipDevice,
                $form->toUserId,
                $createdUser->id,
                [
                    'status' => 'Ringing',
                    'duration' => 0,
                    'typeId' => Call::CALL_TYPE_IN,
                    'type' => 'Incoming',
                    'source_type_id' => Call::SOURCE_INTERNAL,
                    'fromInternal' => 'false',
                    'isInternal' => 'true',
                    'isHold' => 'false',
                    'holdDuration' => 0,
                    'isListen' => 'false',
                    'isCoach' => 'false',
                    'isMute' => 'false',
                    'isBarge' => 'false',
                    'project' => '',
                    'source' => Call::SOURCE_LIST[Call::SOURCE_INTERNAL],
                    'isEnded' => 'false',
                    'contact' => [
                        'name' => $createdUser->nickname ?: $createdUser->username,
                        'phone' => '',
                        'company' => '',
                    ],
                    'department' => '',
                    'queue' => Call::QUEUE_DIRECT,
                    'conference' => [],
                    'isConferenceCreator' => 'false',
                    'recordingDisabled' => $recordingManager->isDisabledRecord(),
                ],
                FriendlyName::next(),
                $recordingManager->isDisabledRecord()
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }
}
