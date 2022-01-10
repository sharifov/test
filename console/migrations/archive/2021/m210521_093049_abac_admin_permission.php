<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210521_093049_abac_admin_permission
 */
class m210521_093049_abac_admin_permission extends Migration
{
    private array $routes = [
        '/abac/*',
        '/abac/abac-policy/*',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
