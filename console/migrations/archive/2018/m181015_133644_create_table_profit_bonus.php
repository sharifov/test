<?php

use yii\db\Migration;

/**
 * Class m181015_133644_create_table_profit_bonus
 */
class m181015_133644_create_table_profit_bonus extends Migration
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

        $this->createTable('profit_bonus', [
            'pb_id' => $this->primaryKey(),
            'pb_user_id' => $this->integer()->notNull(),
            'pb_min_profit' => $this->integer()->notNull(),
            'pb_bonus' => $this->integer()->notNull(),
            'pb_updated_dt' => $this->dateTime(),
            'pb_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk-pb-updated_by', 'profit_bonus', 'pb_updated_user_id', 'employees', 'id');
        $this->addForeignKey('fk-pb-user', 'profit_bonus', 'pb_user_id', 'employees', 'id');
        $this->createIndex('uniq_idx_user_profit', 'profit_bonus', ['pb_user_id','pb_min_profit'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-pb-updated_by', 'profit_bonus');
        $this->dropForeignKey('fk-pb-user', 'profit_bonus');
        $this->dropIndex('uniq_idx_user_profit', 'profit_bonus');
        $this->dropTable('profit_bonus');
    }
}
