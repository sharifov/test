<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201118_140539_fix_permission_cc_form_crud
 */
class m201118_140539_fix_permission_cc_form_crud extends Migration
{
    private $brockenRoutes = [
        '/client-chat-forms-crud/view',
        '/client-chat-forms-crud/index',
        '/client-chat-forms-crud/create',
        '/client-chat-forms-crud/update',
        '/client-chat-forms-crud/builder',
        '/client-chat-forms-crud/delete',
    ];

    private $routes = [
        '/client-chat-form-crud/view',
        '/client-chat-form-crud/index',
        '/client-chat-form-crud/create',
        '/client-chat-form-crud/update',
        '/client-chat-form-crud/builder',
        '/client-chat-form-crud/delete',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->down($this->brockenRoutes, $this->roles);
        (new RbacMigrationService())->up($this->routes, $this->roles);

        $auth = Yii::$app->authManager;
        foreach ($this->brockenRoutes as $name) {
            if ($permission = $auth->getPermission($name)) {
                $result = $auth->remove($permission);
                echo $name . ' removed (' . (int) $result . ").\n";
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
