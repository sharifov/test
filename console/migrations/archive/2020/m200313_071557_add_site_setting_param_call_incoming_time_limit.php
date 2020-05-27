<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200313_071557_add_site_setting_param_call_incoming_time_limit
 */
class m200313_071557_add_site_setting_param_call_incoming_time_limit extends Migration
{

    public $routes = [
        '/call/cancel',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'call_incoming_time_limit',
            's_name' => 'Call incoming time limit (min)',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 15,
            's_updated_dt' => date('Y-m-d H:i:s'),
            //'s_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'call_incoming_time_limit_message',
            's_name' => 'Call incoming time limit Message',
            's_type' => \common\models\Setting::TYPE_STRING,
            's_value' => 'We\'re sorry. All circuits are busy now, please try your call again later.',
            's_updated_dt' => date('Y-m-d H:i:s'),
            //'s_updated_user_id' => 1,
        ]);

        (new RbacMigrationService())->up($this->routes, $this->roles);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_incoming_time_limit',
            'call_incoming_time_limit_message',
        ]]);

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
