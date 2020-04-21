<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191220_140714_add_permissions_phone_black_list
 */
class m191220_140714_add_permissions_phone_black_list extends Migration
{

    public $routes = [
        '/phone/check-black-phone',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_AGENT,
        \common\models\Employee::ROLE_EX_AGENT,
        \common\models\Employee::ROLE_EX_SUPER,
        \common\models\Employee::ROLE_SUP_AGENT,
        \common\models\Employee::ROLE_SUP_SUPER,
        \common\models\Employee::ROLE_SUPERVISION,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
