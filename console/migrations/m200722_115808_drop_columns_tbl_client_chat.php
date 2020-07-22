<?php

use yii\db\Migration;

/**
 * Class m200722_115808_drop_columns_tbl_client_chat
 */
class m200722_115808_drop_columns_tbl_client_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-cch_case_id', '{{%client_chat}}');
        $this->dropForeignKey('FK-cch_lead_id', '{{%client_chat}}');
        $this->dropColumn('{{%client_chat}}', 'cch_case_id');
        $this->dropColumn('{{%client_chat}}', 'cch_lead_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%client_chat}}', 'cch_case_id', $this->integer());
        $this->addColumn('{{%client_chat}}', 'cch_lead_id', $this->integer());
        $this->addForeignKey('FK-cch_case_id', '{{%client_chat}}', ['cch_case_id'], '{{%cases}}', ['cs_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-cch_lead_id', '{{%client_chat}}', ['cch_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');
    }
}
