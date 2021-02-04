<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200521_091202_add_params_for_editing_sale_ticket_markup
 */
class m200521_091202_add_params_for_editing_sale_ticket_markup extends Migration
{
    public $route = [
        '/sale-ticket/ajax-sale-ticket-edit-info',
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
