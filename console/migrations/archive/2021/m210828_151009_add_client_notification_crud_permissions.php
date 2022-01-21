<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210828_151009_add_client_notification_crud_permissions
 */
class m210828_151009_add_client_notification_crud_permissions extends Migration
{
    private $routes = [
        'crud' => [
            '/client-notification/*',
            '/client-notification-phone-list/*',
            '/client-notification-sms-list/*',
        ],
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
        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
