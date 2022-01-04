<?php

use yii\db\Migration;

/**
 * Class m211224_103940_create_tbl_auth_client
 */
class m211224_103940_create_tbl_auth_client extends Migration
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

        $this->createTable('{{%auth_client}}', [
            'ac_id' => $this->primaryKey(),
            'ac_user_id' => $this->integer()->notNull(),
            'ac_source' => $this->string()->notNull(),
            'ac_source_id' => $this->string()->notNull(),
            'ac_email' => $this->string(100),
            'ac_ip' => $this->string(20),
            'ac_useragent' => $this->string(),
            'ac_created_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-auth_client-ac_user_id', '{{%auth_client}}', 'ac_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');

        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auth_client}}');
        Yii::$app->cache->flush();
    }
}
