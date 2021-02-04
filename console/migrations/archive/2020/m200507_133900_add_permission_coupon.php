<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200507_133900_add_permission_coupon
 */
class m200507_133900_add_permission_coupon extends Migration
{

    public $route = [
        '/coupon-case',
        '/coupon-case/index',
        '/coupon-case/create',
        '/coupon-case/view',
        '/coupon-case/update',
        '/coupon-case/delete',
        '/coupon',
        '/coupon/index',
        '/coupon/create',
        '/coupon/view',
        '/coupon/update',
        '/coupon/delete',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        (new RbacMigrationService())->up($this->route, $this->roles);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_profit_type}}');

        (new RbacMigrationService())->down($this->route, $this->roles);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
