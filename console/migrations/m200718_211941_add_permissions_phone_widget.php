<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200718_211941_add_permissions_phone_widget
 */
class m200718_211941_add_permissions_phone_widget extends Migration
{
    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
        Employee::ROLE_EXCHANGE_SENIOR,
        Employee::ROLE_SUPPORT_SENIOR,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $dialpad = $auth->createPermission('PhoneWidget_Dialpad');
        $dialpad->description = 'Phone widget dialpad';
        $auth->add($dialpad);

        $dialpadSearch = $auth->createPermission('PhoneWidget_DialpadSearch');
        $dialpadSearch->description = 'Phone widget dialpad search';
        $auth->add($dialpadSearch);

        $tabContacts = $auth->createPermission('PhoneWidget_ContactsTab');
        $tabContacts->description = 'Phone widget contacts tab';
        $auth->add($tabContacts);

        $tabHistory = $auth->createPermission('PhoneWidget_HistoryTab');
        $tabHistory->description = 'Phone widget history tab';
        $auth->add($tabHistory);

        $tabSms = $auth->createPermission('PhoneWidget_SmsTab');
        $tabSms->description = 'Phone widget sms tab';
        $auth->add($tabSms);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $dialpad);
                $auth->addChild($role, $dialpadSearch);
                $auth->addChild($role, $tabContacts);
                $auth->addChild($role, $tabHistory);
                $auth->addChild($role, $tabSms);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('PhoneWidget_Dialpad')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('PhoneWidget_DialpadSearch')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('PhoneWidget_ContactsTab')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('PhoneWidget_HistoryTab')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('PhoneWidget_SmsTab')) {
            $auth->remove($permission);
        }
    }
}
