<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191028_114518_add_permission_for_lead_redial
 */
class m191028_114518_add_permission_for_lead_redial extends Migration
{
    public $routes = [
        '/lead-redial/*',
    ];

    public $roles = [
        'admin', 'agent', 'supervision'
    ];

    public $old = [
        '/lead-redial/index',
        '/lead-redial/redial',
        '/lead-redial/reservation',
        '/lead-redial/show',
        '/lead-redial/take',
        '/lead-redial/call',
    ];

    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->old as $permissionOld) {
            if ($permission = $auth->getPermission($permissionOld)) {
                $auth->remove($permission);
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);

    }

    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
