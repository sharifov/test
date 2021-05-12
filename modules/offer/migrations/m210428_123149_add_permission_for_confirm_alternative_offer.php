<?php

namespace modules\offer\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210428_123149_add_permission_for_confirm_alternative_offer
 */
class m210428_123149_add_permission_for_confirm_alternative_offer extends Migration
{
    private array $routes = [
        '/offer/offer/ajax-confirm-alternative'
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
