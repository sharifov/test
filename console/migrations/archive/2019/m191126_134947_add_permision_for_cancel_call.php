<?php

use yii\db\Migration;

/**
 * Class m191126_134947_add_permision_for_cancel_call
 */
class m191126_134947_add_permision_for_cancel_call extends Migration
{
    public $routes = [
        '/call/cancel-manual',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,

        \common\models\Employee::ROLE_AGENT,
        \common\models\Employee::ROLE_EX_AGENT,
        \common\models\Employee::ROLE_SUP_AGENT,

        \common\models\Employee::ROLE_SUPERVISION,
        \common\models\Employee::ROLE_EX_SUPER,
        \common\models\Employee::ROLE_SUP_SUPER,
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
