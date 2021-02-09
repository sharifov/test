<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210108_055334_add_permission_multiple_update_chat
 */
class m210108_055334_add_permission_multiple_update_chat extends Migration
{
    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPERVISION,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $assignManage = $auth->createPermission('client-chat/multiple/assign/manage');
        $assignManage->description = 'Assign Client Chats';
        $auth->add($assignManage);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $assignManage);
            }
        }

        $archiveManage = $auth->createPermission('client-chat/multiple/archive/manage');
        $archiveManage->description = 'Client Chats to archive';
        $auth->add($archiveManage);

        foreach ($this->roles as $item) {
            if ($role = $auth->getRole($item)) {
                $auth->addChild($role, $archiveManage);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        foreach (['client-chat/multiple/assign/manage', 'client-chat/multiple/archive/manage'] as $permissionName) {
            if ($permission = $auth->getPermission($permissionName)) {
                $auth->remove($permission);
            }
        }
    }
}
