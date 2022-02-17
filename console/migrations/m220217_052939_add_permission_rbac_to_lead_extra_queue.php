<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220217_052939_add_permition_rbac_to_lead_extra_queue
 */
class m220217_052939_add_permition_rbac_to_lead_extra_queue extends Migration
{
    private $routes = [
        '/lead/extra-queue',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,

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
        (new RbacMigrationService())->up($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
