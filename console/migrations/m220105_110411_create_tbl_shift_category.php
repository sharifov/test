<?php

use yii\db\Migration;

/**
 * Class m220105_110411_create_tbl_shift_category
 */
class m220105_110411_create_tbl_shift_category extends Migration
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

        $this->createTable('{{%shift_category}}', [
            'sc_id' => $this->primaryKey(),
            'sc_name' => $this->string(50)->notNull(),
            'sc_created_user_id' => $this->integer(),
            'sc_updated_user_id' => $this->integer(),
            'sc_created_dt' => $this->dateTime(),
            'sc_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-shift_category-sc_created_user_id', '{{%shift_category}}', 'sc_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-shift_category-sc_updated_user_id', '{{%shift_category}}', 'sc_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shift_category}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
