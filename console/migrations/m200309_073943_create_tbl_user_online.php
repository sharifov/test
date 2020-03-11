<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200309_073943_create_tbl_user_online
 */
class m200309_073943_create_tbl_user_online extends Migration
{

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/user-online/*',
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

        $this->createTable('{{%user_online}}', [
            'uo_user_id' => $this->primaryKey(),
            'uo_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-user_online-uo_user_id',
            '{{%user_online}}',
            'uo_user_id',
            '{{%employees}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        \Yii::$app->db->getSchema()->refreshTableSchema('{{%visitor_log}}');

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
        $this->dropForeignKey('FK-user_online-uo_user_id', '{{%user_online}}');
        $this->dropTable('{{%user_online}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
