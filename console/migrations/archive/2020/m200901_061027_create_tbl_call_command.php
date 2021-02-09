<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200901_061027_create_tbl_call_command
 */
class m200901_061027_create_tbl_call_command extends Migration
{
    public $route = [
        '/call-command/*',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
    ];

    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%call_command}}', [
            'ccom_id' => $this->primaryKey(),
            'ccom_parent_id' => $this->integer(),
            'ccom_project_id' => $this->integer(),
            'ccom_lang_id' => $this->string(5),
            'ccom_name' => $this->string(100),
            'ccom_type_id' => $this->smallInteger()->notNull(),
            'ccom_params_json' => $this->json(),
            'ccom_sort_order' => $this->smallInteger()->defaultValue(5),
            'ccom_user_id' => $this->integer(),
            'ccom_created_user_id' => $this->integer(),
            'ccom_updated_user_id' => $this->integer(),
            'ccom_created_dt' => $this->dateTime(),
            'ccom_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-call_command-ccom_parent_id', '{{%call_command}}', ['ccom_parent_id'], '{{%call_command}}', ['ccom_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-call_command-ccom_project_id', '{{%call_command}}', ['ccom_project_id'], '{{%projects}}', ['id'], 'CASCADE', 'CASCADE');

        //$this->addForeignKey('FK-call_command-ccom_lang_id', '{{%call_command}}', ['ccom_lang_id'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-call_command-ccom_user_id', '{{%call_command}}', ['ccom_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-call_command-ccom_created_user_id', '{{%call_command}}', ['ccom_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-call_command-ccom_updated_user_id', '{{%call_command}}', ['ccom_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {

        $this->dropTable('{{%call_command}}');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
