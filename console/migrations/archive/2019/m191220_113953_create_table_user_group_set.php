<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191220_113953_create_table_user_group_set
 */
class m191220_113953_create_table_user_group_set extends Migration
{
    public $routes = [
        '/user-group-set/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
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

        $this->createTable('{{%user_group_set}}',	[
            'ugs_id'             => $this->primaryKey(),
            'ugs_name'           => $this->string(255),
            'ugs_enabled'        => $this->boolean()->defaultValue(false),
            'ugs_created_dt'     => $this->dateTime(),
            'ugs_updated_dt'     => $this->dateTime(),
            'ugs_updated_user_id'     => $this->integer()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-user_group_set_updated_user_id',
            '{{%user_group_set}}',
            'ugs_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_group_set}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        $this->addColumn('{{%user_group}}', 'ug_user_group_id', $this->integer());

        $this->addForeignKey(
            'FK-user_group_ug_user_group_id',
            '{{%user_group}}',
            'ug_user_group_id',
            '{{%user_group_set}}',
            'ugs_id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_group}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_group_ug_user_group_id', '{{%user_group}}');
        $this->dropColumn('{{%user_group}}', 'ug_user_group_id');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_group}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        $this->dropForeignKey('FK-user_group_set_updated_user_id', '{{%user_group_set}}');
        $this->dropTable('{{%user_group_set}}');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_group_set}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
