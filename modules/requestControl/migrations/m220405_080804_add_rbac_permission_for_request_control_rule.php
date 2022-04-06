<?php

namespace modules\requestControl\migrations;

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m220405_080804_add_rbac_permission_for_request_control_rule
 */
class m220405_080804_add_rbac_permission_for_request_control_rule extends Migration
{
    private $routes = [
        '/requestControl/*'
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * @return false|mixed|void
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
