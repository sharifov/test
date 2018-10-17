<?php

use yii\db\Migration;

/**
 * Class m181016_110715_create_table_profit_split
 */
class m181016_110715_create_table_profit_split extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('profit_split', [
            'ps_id' => $this->primaryKey(),
            'ps_lead_id' => $this->integer()->notNull(),
            'ps_user_id' => $this->integer()->notNull(),
            'ps_percent' => $this->integer()->null(),
            'ps_amount' => $this->integer()->null(),
            'ps_updated_dt' => $this->dateTime(),
            'ps_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk-ps-updated_by', 'profit_split', 'ps_updated_user_id', 'employees', 'id');
        $this->addForeignKey('fk-ps-user', 'profit_split', 'ps_user_id', 'employees', 'id');
        $this->addForeignKey('fk-ps-lead', 'profit_split', 'ps_lead_id', 'leads', 'id');
        $this->createIndex('uniq_idx_user_profit_split', 'profit_split', ['ps_user_id','ps_lead_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-ps-updated_by', 'profit_split');
        $this->dropForeignKey('fk-ps-user', 'profit_split');
        $this->dropForeignKey('fk-ps-lead', 'profit_split');
        $this->dropIndex('uniq_idx_user_profit_split', 'profit_split');
        $this->dropTable('profit_split');
    }
}
