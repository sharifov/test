<?php

use yii\db\Migration;

/**
 * Class m201224_152327_create_tbl_call_log_user_access
 */
class m201224_152327_create_tbl_call_log_user_access extends Migration
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

        $this->createTable('{{%call_log_user_access}}', [
            'clua_id' => $this->primaryKey(),
            'clua_cl_id' => $this->integer()->notNull(),
            'clua_user_id' => $this->integer(),
            'clua_access_status_id' => $this->integer(),
            'clua_access_start_dt' => $this->dateTime(),
            'clua_access_finish_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-call_log_user_access-clua_cl_id', '{{%call_log_user_access}}', ['clua_cl_id']);
        $this->addForeignKey(
            'FK-call_log_user_access-clua_user_id',
            '{{%call_log_user_access}}',
            ['clua_user_id'],
            '{{%employees}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%call_log_user_access}}');
    }
}
