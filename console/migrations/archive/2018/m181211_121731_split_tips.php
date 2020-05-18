<?php

use yii\db\Migration;

/**
 * Class m181211_121731_split_tips
 */
class m181211_121731_split_tips extends Migration
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

        $this->createTable('tips_split', [
            'ts_id' => $this->primaryKey(),
            'ts_lead_id' => $this->integer()->notNull(),
            'ts_user_id' => $this->integer()->notNull(),
            'ts_percent' => $this->integer()->null(),
            'ts_amount' => $this->integer()->null(),
            'ts_updated_dt' => $this->dateTime(),
            'ts_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk-tps-updated_by', 'tips_split', 'ts_updated_user_id', 'employees', 'id');
        $this->addForeignKey('fk-ts-user', 'tips_split', 'ts_user_id', 'employees', 'id');
        $this->addForeignKey('fk-ts-lead', 'tips_split', 'ts_lead_id', 'leads', 'id');
        $this->createIndex('uniq_idx_user_tips_split', 'tips_split', ['ts_user_id','ts_lead_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-ts-updated_by', 'tips_split');
        $this->dropForeignKey('fk-ts-user', 'tips_split');
        $this->dropForeignKey('fk-ts-lead', 'tips_split');
        $this->dropIndex('uniq_idx_user_tips_split', 'tips_split');
        $this->dropTable('tips_split');
    }

}
