<?php

use yii\db\Migration;
use common\models\Employee;

/**
 * Class m200618_140540_add_permissions_quotes_ajax_capture
 */
class m200618_140540_add_permissions_quotes_ajax_capture extends Migration
{
    public $route = ['/quotes/ajax-capture'];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
        Employee::ROLE_AGENT
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
