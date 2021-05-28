<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210527_143931_add_route_permissions_to_cc
 */
class m210527_143931_add_route_permissions_to_cc extends Migration
{
    private $oldRoutes = [
        '/client-chat/index',
    ];

    private $newRoutes = [
        '/client-chat/index',
        '/client-chat/detail',
        '/client-chat/room',
        '/client-chat/message-body-view',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->down($this->oldRoutes, $this->roles);
        (new RbacMigrationService())->up($this->newRoutes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->newRoutes, $this->roles);
        (new RbacMigrationService())->up($this->oldRoutes, $this->roles);
        Yii::$app->cache->flush();
    }
}
