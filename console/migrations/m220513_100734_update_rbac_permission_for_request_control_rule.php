<?php

use yii\db\Migration;
use common\models\Employee;
use console\migrations\RbacMigrationService;

/**
 * Class m220513_100734_update_rbac_permission_for_request_control_rule
 */
class m220513_100734_update_rbac_permission_for_request_control_rule extends Migration
{
    private $notActualRoutes = [
        '/requestControl/*'
    ];

    private $actualRoutes = [
        '/request-control/*'
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
        (new RbacMigrationService())->down($this->notActualRoutes, $this->roles);
        (new RbacMigrationService())->up($this->actualRoutes, $this->roles);
    }

    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->actualRoutes, $this->roles);
        (new RbacMigrationService())->up($this->notActualRoutes, $this->roles);
    }
}
