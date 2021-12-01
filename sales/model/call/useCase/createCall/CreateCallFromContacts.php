<?php

namespace sales\model\call\useCase\createCall;

use common\models\Client;
use frontend\widgets\newWebPhone\AvailablePhones;
use sales\auth\Auth;
use sales\helpers\UserCallIdentity;
use sales\model\call\services\FriendlyName;
use sales\model\call\services\RecordManager;

class CreateCallFromContacts
{
    public function __invoke(CreateCallForm $form): array
    {
        try {
            $availablePhones = new AvailablePhones($form->getCreatedUserId());
            $phone = $availablePhones->getPhone($form->from);
            if (!$phone) {
                throw new \DomainException('Phone From (' . $form->from . ') is not available.');
            }

            if (!$client = Client::findOne(['id' => $form->clientId])) {
                throw new \DomainException('Not found Client. ID: ' . $form->clientId);
            }

            if ($phone->projectId !== $client->cl_project_id) {
                throw new \DomainException('Phone From project (' . $phone->title . ') is not equal with Client project (' . $client->project->name . ')');
            }

            //todo: validate can created user call to this contact?

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $phone->projectId,
                $phone->departmentId,
                $form->from,
                $client->id
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
                    'client_id' => $client->id,
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
