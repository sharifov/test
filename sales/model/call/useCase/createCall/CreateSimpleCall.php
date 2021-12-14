<?php

namespace sales\model\call\useCase\createCall;

use common\models\search\ContactsSearch;
use frontend\widgets\newWebPhone\AvailablePhones;
use sales\auth\Auth;
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

            $contactId = null;
            $contacts = (new ContactsSearch($form->createdUserId))->getClientsContactByPhone($form->to);
            foreach ($contacts as $contact) {
                if (!$contact['project_id'] || (int)$contact['project_id'] === $phone->projectId) {
                    $contactId = (int)$contact['id'];
                    break;
                }
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $phone->projectId,
                $phone->departmentId,
                $form->from,
                $contactId,
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => $form->getClientDeviceIdentity(),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => $phone->projectId,
                    'department_id' => $phone->departmentId,
                    'client_id' => $contactId,
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
