<?php

use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\services\phone\checkPhone\CheckPhoneService;
use yii\db\Migration;

/**
 * Class m210628_101222_change_cpl_uid_to_md4
 */
class m210628_101222_change_cpl_uid_to_md4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (ContactPhoneList::find()->all() as $contactPhoneList) {
            $contactPhoneList->detachBehaviors();
            $contactPhoneList->cpl_uid = CheckPhoneService::uidGenerator($contactPhoneList->cpl_phone_number);
            $contactPhoneList->save(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach (ContactPhoneList::find()->all() as $contactPhoneList) {
            $contactPhoneList->detachBehaviors();
            $contactPhoneList->cpl_uid = md5($contactPhoneList->cpl_phone_number);
            $contactPhoneList->save(false);
        }
    }
}
