<?php

use yii\db\Migration;

/**
 * Class m210126_113426_add_permission_cc_filter_client_email
 */
class m210126_113426_add_permission_cc_filter_client_email extends Migration
{
    private array $route = [
        'client-chat/dashboard/filter/client_email',
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
                if ($authPermission->name === '/client-chat/index') {
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
                if ($authPermission->name === '/client-chat/index') {
                    $this->roles[] = $role->name;
                }
            }
        }

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
