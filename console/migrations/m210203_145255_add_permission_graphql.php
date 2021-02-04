<?php

use yii\db\Migration;

/**
 * Class m210203_145255_add_permission_graphql
 */
class m210203_145255_add_permission_graphql extends Migration
{
    private $route = [
        '/graphql/index'
    ];

    private $roles = [
        \common\models\Employee::ROLE_SUPERVISION,
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
