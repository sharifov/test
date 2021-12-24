<?php

namespace sales\behaviors\clientPhone;

use common\models\ClientPhone;
use sales\helpers\app\AppHelper;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\model\contactPhoneList\repository\ContactPhoneListRepository;
use sales\repositories\client\ClientPhoneRepository;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class ContactPhoneListBehavior
 */
class ContactPhoneListBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'linkToContactPhoneList',
        ];
    }

    public function linkToContactPhoneList(): void
    {
        if (!empty($this->owner->cp_cpl_id)) {
            return;
        }
        try {
            if (!$this->owner instanceof ClientPhone) {
                throw new \RuntimeException('Owner class must by instanceof "ClientPhone"');
            }
            $clientPhoneRepository = \Yii::createObject(ClientPhoneRepository::class);
            $contactPhoneList = $this->findOrCreate($this->owner->cp_cpl_uid, $this->owner->phone);

            $this->owner->cp_cpl_id = $contactPhoneList->cpl_id;
            $clientPhoneRepository->save($this->owner);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                'clientPhoneId' => $this->owner->id
            ]);
            \Yii::warning($message, 'ContactPhoneListBehavior:linkToContactPhoneList:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), [
                'clientPhoneId' => $this->owner->id
            ]);
            \Yii::error($message, 'ContactPhoneListBehavior:linkToContactPhoneList:Throwable');
        }
    }

    private function findOrCreate(string $uid, string $phone): ContactPhoneList
    {
        if (!$contactPhoneList = ContactPhoneList::find()->where(['cpl_uid' => $uid])->limit(1)->one()) {
            $contactPhoneList = ContactPhoneList::create($phone);
            (new ContactPhoneListRepository())->save($contactPhoneList);
        }
        return $contactPhoneList;
    }
}
