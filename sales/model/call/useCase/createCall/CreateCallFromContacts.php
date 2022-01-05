<?php

namespace sales\model\call\useCase\createCall;

use common\models\search\ContactsSearch;
use frontend\widgets\newWebPhone\AvailablePhones;
use sales\auth\Auth;
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

            $contactResult = (new ContactsSearch($form->createdUserId))->getClientContactsById($form->clientId);
            if (!$contactResult) {
                throw new \DomainException('Not found contact. ID(' . $form->clientId . ')');
            }
            $contactPhoneFound = false;
            foreach ($contactResult as $contact) {
                if ($contact['phone'] === $form->to) {
                    if (!$contact['project_id'] || (int)$contact['project_id'] === $phone->projectId) {
                        $contactPhoneFound = true;
                        break;
                    }
                    throw new \DomainException('Phone From project (' . $phone->projectId . ') is not equal with Client project (' . $contact['project_id'] . ')');
                }
            }
            if (!$contactPhoneFound) {
                throw new \DomainException('Not found relation Contact ID (' . $form->clientId . ') with phone ' . $form->to);
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $phone->projectId,
                $phone->departmentId,
                $form->from,
                $form->clientId,
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'device' => $form->getVoipDevice(),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => $phone->projectId,
                    'department_id' => $phone->departmentId,
                    'client_id' => $form->clientId,
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
