<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200113_154753_change_group_route_to_routes_for_admin
 */
class m200113_154753_change_group_route_to_routes_for_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $report = (Yii::createObject(RbacMigrationService::class))->changeGroupRouteToRoutes(Employee::ROLE_ADMIN);
        foreach ($report as $item) {
            echo $item . PHP_EOL;
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
