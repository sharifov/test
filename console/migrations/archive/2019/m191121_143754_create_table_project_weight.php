<?php

use yii\db\Migration;

/**
 * Class m191121_143754_create_table_project_weight
 */
class m191121_143754_create_table_project_weight extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%project_weight}}',	[
            'pw_project_id'   => $this->integer()->notNull(),
            'pw_weight'     => $this->integer()->defaultValue(0),
        ], $tableOptions);

        $this->addPrimaryKey('PK-project_weight_pw_project_id', '{{%project_weight}}', ['pw_project_id']);
        $this->addForeignKey('FK-project_weight_pw_weight', '{{%project_weight}}', ['pw_project_id'], '{{%projects}}', ['id'], 'CASCADE', 'CASCADE');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%project_weight}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%project_weight}}');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%project_weight}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
