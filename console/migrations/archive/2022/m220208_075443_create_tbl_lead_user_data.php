<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220208_075443_create_tbl_lead_user_data
 */
class m220208_075443_create_tbl_lead_user_data extends Migration
{
    private $routes = [
        '/lead-user-data-crud/view',
        '/lead-user-data-crud/index',
        '/lead-user-data-crud/create',
        '/lead-user-data-crud/update',
        '/lead-user-data-crud/delete',
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

        $this->createTable('{{%lead_user_data}}', [
            'lud_id' => $this->primaryKey(),
            'lud_type_id' => $this->tinyInteger()->notNull(),
            'lud_lead_id' => $this->integer()->notNull(),
            'lud_user_id' => $this->integer()->notNull(),
            'lud_created_dt' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-lead_user_data-lud_user_id',
            '{{%lead_user_data}}',
            ['lud_user_id'],
            '{{%employees}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-lead_user_data-lud_lead_id',
            '{{%lead_user_data}}',
            ['lud_lead_id'],
            '{{%leads}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('IND-lud-type-lead-user', '{{%lead_user_data}}', ['lud_type_id', 'lud_lead_id', 'lud_user_id']);
        $this->createIndex('IND-lud_created_dt', '{{%lead_user_data}}', 'lud_created_dt');

        (new RbacMigrationService())->up($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_user_data}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
