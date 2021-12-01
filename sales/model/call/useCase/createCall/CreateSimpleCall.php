<?php

namespace sales\model\call\useCase\createCall;

use common\models\ClientPhone;
use frontend\widgets\newWebPhone\AvailablePhones;
use sales\auth\Auth;
use sales\helpers\UserCallIdentity;
use sales\model\call\services\FriendlyName;
use sales\model\call\services\RecordManager;

class CreateSimpleCall
{
    public function __invoke(CreateCallForm $form): array
    {
        try {
            $availablePhones = new AvailablePhones($form->getCreatedUserId());
            $phone = $availablePhones->getPhone($form->from);
            if (!$phone) {
                throw new \DomainException('Phone From (' . $form->from . ') is not available.');
            }

//            $clientId = ClientPhone::find()->select(['client_id'])->where(['phone' => $form->to])->orderBy(['id' => SORT_DESC])->limit(1)->scalar();
//            if ($clientId) {
//                $clientId = (int)$clientId;
//            }

            $clientId = null;
            //todo: validate can created user simple call

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $phone->projectId,
                $phone->departmentId,
                $form->from,
                $clientId,
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => UserCallIdentity::getClientId($form->getCreatedUserId()),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => $phone->projectId,
                    'department_id' => $phone->departmentId,
                    'client_id' => $clientId,
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                ])
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
