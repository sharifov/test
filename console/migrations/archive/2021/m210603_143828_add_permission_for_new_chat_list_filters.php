<?php

use yii\db\Migration;

/**
 * Class m210603_143828_add_permission_for_new_chat_list_filters
 */
class m210603_143828_add_permission_for_new_chat_list_filters extends Migration
{
    private array $route = [
        'client-chat/dashboard/filter/user_groups',
        'client-chat/dashboard/filter/chat_id',
    ];

    private array $roles = [];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/client-chat/dashboard-v2') {
                    $this->roles[] = $role->name;
                }
            }
        }

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        foreach ($auth->getRoles() as $role) {
            foreach ($auth->getPermissionsByRole($role->name) as $authPermission) {
                if ($authPermission->name === '/client-chat/dashboard-v2') {
                    $this->roles[] = $role->name;
                }
            }
        }

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
