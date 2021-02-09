<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200423_113152_add_permissions_lead_failed_bookings
 */
class m200423_113152_add_permissions_lead_failed_bookings extends Migration
{
    public $route = ['/lead/failed-bookings'];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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
