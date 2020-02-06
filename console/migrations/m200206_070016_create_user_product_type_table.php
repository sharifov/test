<?php

use yii\db\Migration;

/**
 * Class m200206_070016_create_user_product_type_table
 */
class m200206_070016_create_user_product_type_table extends Migration
{
    public $routes = [
        '/user-product-type/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    public $tableName = 'user_product_type';
    public $table = "{{%user_product_type}}";
    public $tableOptions = null;

    /**
     * @return bool|void
     * @throws \yii\base\Exception
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->table,	[
            'upt_user_id' => $this->integer()->notNull(),
            'upt_product_type_id' => $this->integer()->notNull(),
            'upt_commission_percent' => $this->decimal(5, 2),
            'upt_product_enabled' => $this->boolean()->defaultValue(true),
            'upt_created_user_id' => $this->integer(),
            'upt_updated_user_id' => $this->integer(),
            'upt_created_dt' => $this->dateTime(),
            'upt_updated_dt' => $this->dateTime(),
        ], $this->tableOptions);

        $this->addPrimaryKey('PK-' . $this->tableName . '-user-product-type', $this->table, ['upt_user_id', 'upt_product_type_id']);

        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_user_id', $this->table, ['upt_user_id'],
            '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_product_type_id', $this->table, ['upt_product_type_id'],
            '{{%product_type}}', ['pt_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_created_user_id', $this->table, ['upt_created_user_id'],
            '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey(
            'FK-' . $this->tableName . '-upt_updated_user_id', $this->table, ['upt_updated_user_id'],
            '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

        $this->resetCache();
    }

    /**
     * {@inheritdoc}
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_user_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_product_type_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_created_user_id', $this->table);
        $this->dropForeignKey('FK-' . $this->tableName . '-upt_updated_user_id', $this->table);

        $this->dropTable($this->table);

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

        $this->resetCache();
    }

    /**
     * @throws \yii\base\NotSupportedException
     */
    private function resetCache(): void
    {
        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
        \Yii::$app->db->getSchema()->refreshTableSchema($this->tableName);
    }
}
