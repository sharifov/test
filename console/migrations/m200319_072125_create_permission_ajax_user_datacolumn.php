<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200319_072125_create_permission_ajax_user_datacolumn
 */
class m200319_072125_create_permission_ajax_user_datacolumn extends Migration
{
    public $route = ['/employee/list-ajax'];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SUP_SUPER,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_AGENT,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_SUP_AGENT,
    ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
