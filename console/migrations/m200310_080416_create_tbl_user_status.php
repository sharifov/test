<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200310_080416_create_tbl_user_status
 */
class m200310_080416_create_tbl_user_status extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/user-status/*',
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

        $this->createTable('{{%user_status}}', [
            'us_user_id' => $this->primaryKey(),
            'us_gl_call_count' => $this->integer()->defaultValue(0),
            'us_call_phone_status' => $this->boolean()->defaultValue(true),
            'us_is_on_call' => $this->boolean()->defaultValue(true),
            'us_has_call_access' => $this->boolean()->defaultValue(true),
            'us_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-user_status-us_user_id',
            '{{%user_status}}',
            'us_user_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('IND-user_status-us_gl_call_count', '{{%user_status}}', ['us_gl_call_count']);

        \Yii::$app->db->getSchema()->refreshTableSchema('{{%user_status}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-user_status-us_gl_call_count', '{{%user_status}}');
        $this->dropForeignKey('FK-user_status-us_user_id', '{{%user_status}}');
        $this->dropTable('{{%user_status}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
