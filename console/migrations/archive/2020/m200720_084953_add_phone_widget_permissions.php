<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200720_084953_add_phone_widget_permissions
 */
class m200720_084953_add_phone_widget_permissions extends Migration
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

        $phoneWidget = $auth->createPermission('PhoneWidget');
        $phoneWidget->description = 'Phone widget';
        $auth->add($phoneWidget);

        $phoneWidgetTransfer = $auth->createPermission('PhoneWidget_Transfer');
        $phoneWidgetTransfer->description = 'Phone widget transfer';
        $auth->add($phoneWidgetTransfer);

        $phoneWidgetOnHold = $auth->createPermission('PhoneWidget_OnHold');
        $phoneWidgetOnHold->description = 'Phone widget on hold';
        $auth->add($phoneWidgetOnHold);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $phoneWidget);
                $auth->addChild($role, $phoneWidgetTransfer);
                $auth->addChild($role, $phoneWidgetOnHold);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('PhoneWidget')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('PhoneWidget_Transfer')) {
            $auth->remove($permission);
        }

        if ($permission = $auth->getPermission('PhoneWidget_OnHold')) {
            $auth->remove($permission);
        }
    }
}
