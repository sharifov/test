<?php

namespace modules\offer\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210512_123836_add_permission_for_cancel_offer_alternative
 */
class m210512_123836_add_permission_for_cancel_offer_alternative extends Migration
{
    private array $routes = [
        '/offer/offer/ajax-cancel-alternative'
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
