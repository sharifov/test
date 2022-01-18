<?php

use common\models\Employee;
use common\models\UserProfile;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210129_094150_create_tbl_user_client_chat_data
 */
class m210129_094150_create_tbl_user_client_chat_data extends Migration
{
    private array $routes = [
        '/user-client-chat-data-crud/*',
        '/employee/activate-to-rocket-chat',
        '/employee/deactivate-from-rocket-chat',
    ];

    private array $roles = [
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

        $this->createTable('{{%user_client_chat_data}}', [
            'uccd_id' => $this->primaryKey(),
            'uccd_employee_id' => $this->integer()->unique()->notNull(),
            'uccd_active' => $this->boolean()->defaultValue(true)->notNull(),
            'uccd_created_dt' => $this->dateTime(),
            'uccd_updated_dt' => $this->dateTime(),
            'uccd_created_user_id' => $this->integer(),
            'uccd_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-user_client_chat_data_employees',
            '{{%user_client_chat_data}}',
            'uccd_employee_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $userProfile = UserProfile::find()
            ->select(['up_user_id'])
            ->where(['IS NOT', 'up_rc_user_id', null])
            ->indexBy('up_user_id')
            ->asArray()
            ->all();

        Yii::$app->db->createCommand()->batchInsert('{{%user_client_chat_data}}', ['uccd_employee_id'], $userProfile)->execute();

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_client_chat_data}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
