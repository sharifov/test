<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210614_140529_create_lead_user_conversion_tbl
 */
class m210614_140529_create_lead_user_conversion_tbl extends Migration
{
    private $routes = [
        '/lead-user-conversion-crud/index',
        '/lead-user-conversion-crud/create',
        '/lead-user-conversion-crud/view',
        '/lead-user-conversion-crud/update',
        '/lead-user-conversion-crud/delete',
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

        $this->createTable('{{%lead_user_conversion}}', [
            'luc_lead_id' => $this->integer()->notNull(),
            'luc_user_id' => $this->integer()->notNull(),
            'luc_description' => $this->string(100),
            'luc_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-lead_user_conversion', '{{%lead_user_conversion}}', ['luc_lead_id', 'luc_user_id']);

        $this->addForeignKey(
            'FK-lead_user_conversion-luc_lead_id',
            '{{%lead_user_conversion}}',
            ['luc_lead_id'],
            '{{%leads}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-lead_user_conversion-luc_user_id',
            '{{%lead_user_conversion}}',
            ['luc_user_id'],
            '{{%employees}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_user_conversion}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
