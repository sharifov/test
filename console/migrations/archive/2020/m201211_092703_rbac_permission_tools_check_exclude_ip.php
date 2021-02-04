<?php

use yii\db\Migration;

/**
 * Class m201211_092703_rbac_permission_tools_check_exclude_ip
 */
class m201211_092703_rbac_permission_tools_check_exclude_ip extends Migration
{
    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/tools/check-exclude-ip',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
