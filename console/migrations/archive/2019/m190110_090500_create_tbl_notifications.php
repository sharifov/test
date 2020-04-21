<?php

use yii\db\Migration;

class m190110_090500_create_tbl_notifications extends Migration
{
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%notifications}}', [
            'n_id'         => $this->primaryKey(),
            'n_unique_id'   => $this->string(40),
            'n_user_id'    => $this->integer()->notNull(),
            'n_type_id'    => $this->smallInteger()->notNull(),
            'n_title'      => $this->string(100),
            'n_message'    => $this->text(),
            'n_new'        => $this->boolean()->defaultValue(true),
            'n_deleted'    => $this->boolean()->defaultValue(false),
            'n_popup'      => $this->boolean()->defaultValue(false),
            'n_popup_show' => $this->boolean()->defaultValue(false),
            'n_read_dt'    => $this->dateTime(),
            'n_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-notifications_n_user_id', '{{%notifications}}', ['n_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%notifications}}');
    }
}
