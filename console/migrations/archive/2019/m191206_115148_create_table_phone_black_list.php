<?php

use yii\db\Migration;

/**
 * Class m191206_115148_create_table_phone_black_list
 */
class m191206_115148_create_table_phone_black_list extends Migration
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

        $this->createTable('{{%phone_blacklist}}', [
            'pbl_id' => $this->primaryKey(),
            'pbl_phone' => $this->string(30)->notNull()->unique(),
            'pbl_description' => $this->string(),
            'pbl_enabled' => $this->boolean(),
            'pbl_created_dt' => $this->dateTime(),
            'pbl_updated_dt' => $this->dateTime(),
            'pbl_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('FK-phone_blacklist_pbl_updated_user_id', '{{%phone_blacklist}}', ['pbl_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropForeignKey('FK-phone_blacklist_pbl_updated_user_id', '{{%phone_blacklist}}');
       $this->dropTable('{{%phone_blacklist}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
