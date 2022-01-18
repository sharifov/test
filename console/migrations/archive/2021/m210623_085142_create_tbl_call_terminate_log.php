<?php

use common\models\Employee;
use common\models\Setting;
use common\models\SettingCategory;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210623_085142_create_tbl_call_terminate_log
 */
class m210623_085142_create_tbl_call_terminate_log extends Migration
{
    private $routes = [
        '/call-terminate-log-crud/index',
        '/call-terminate-log-crud/create',
        '/call-terminate-log-crud/view',
        '/call-terminate-log-crud/update',
        '/call-terminate-log-crud/delete',
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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%call_terminate_log}}', [
            'ctl_id' => $this->primaryKey(),
            'ctl_call_phone_number' => $this->string(100)->notNull(),
            'ctl_call_status_id' => $this->integer()->notNull(),
            'ctl_project_id' => $this->integer(),
            'ctl_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-call_terminate_log-phone', '{{%call_terminate_log}}', ['ctl_call_phone_number']);
        $this->createIndex('IND-call_terminate_log-status', '{{%call_terminate_log}}', ['ctl_call_status_id']);

        $this->addForeignKey(
            'FK-call_terminate_log-project',
            '{{%call_terminate_log}}',
            'ctl_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $settingCategory = SettingCategory::getOrCreateByName('Call');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_terminate_black_list',
                's_name' => 'Call terminate black list',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'enable_write_log' => 1,
                    'enable_to_black_list' => 0,
                    'limit_count' => 2,
                    'limit_minutes' => 15,
                    'black_list_expired_minutes' => 3 * 60,
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_terminate_log-phone', '{{%call_terminate_log}}');
        $this->dropIndex('IND-call_terminate_log-status', '{{%call_terminate_log}}');
        $this->dropForeignKey(
            'FK-call_terminate_log-project',
            '{{%call_terminate_log}}'
        );

        $this->dropTable('{{%call_terminate_log}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_terminate_black_list',
        ]]);
    }
}
