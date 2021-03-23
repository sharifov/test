<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210303_120035_add_permission_for_send_offer_from_chat_dashboard
 */
class m210303_120035_add_permission_for_send_offer_from_chat_dashboard extends Migration
{
    private $route = [
        '/client-chat/send-quote-list',
        '/client-chat/send-quote-generate',
        '/client-chat/send-quote',
        '/client-chat/send-offer',
    ];

    private $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->route, $this->roles);
    }
}
