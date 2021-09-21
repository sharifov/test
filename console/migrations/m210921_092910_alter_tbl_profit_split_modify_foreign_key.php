<?php

use yii\db\Migration;

/**
 * Class m210921_092910_alter_tbl_profit_split_modify_foreign_key
 */
class m210921_092910_alter_tbl_profit_split_modify_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM `profit_split` WHERE ps_lead_id NOT IN (SELECT id FROM leads)');
        $this->dropForeignKey('fk-ps-lead', '{{%profit_split}}');
        $this->addForeignKey('fk-ps-lead', '{{%profit_split}}', 'ps_lead_id', '{{%leads}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-ps-lead', '{{%profit_split}}');
        $this->addForeignKey('fk-ps-lead', '{{%profit_split}}', 'ps_lead_id', '{{%leads}}', 'id');
    }
}
