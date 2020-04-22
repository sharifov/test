<?php

use yii\db\Migration;

/**
 * Class m190417_060006_add_columns_tbl_lead
 */
class m190417_060006_add_columns_tbl_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_client_first_name', $this->string(50));
        $this->addColumn('{{%leads}}', 'l_client_last_name', $this->string(50));
        $this->addColumn('{{%leads}}', 'l_client_phone', $this->string(20));
        $this->addColumn('{{%leads}}', 'l_client_email', $this->string(160));
        $this->addColumn('{{%leads}}', 'l_client_lang', $this->string(5));
        $this->addColumn('{{%leads}}', 'l_client_ua', $this->text());

        $this->addColumn('{{%leads}}', 'l_request_hash', $this->string(32));
        $this->addColumn('{{%leads}}', 'l_duplicate_lead_id', $this->integer());

        $this->addForeignKey('FK-leads-l_duplicate_lead_id', '{{%leads}}', ['l_duplicate_lead_id'], '{{%leads}}', ['id'], 'SET NULL', 'CASCADE');
        $this->createIndex('IND-leads-l_request_hash', '{{%leads}}', ['l_request_hash']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_client_first_name');
        $this->dropColumn('{{%leads}}', 'l_client_last_name');
        $this->dropColumn('{{%leads}}', 'l_client_phone');
        $this->dropColumn('{{%leads}}', 'l_client_email');
        $this->dropColumn('{{%leads}}', 'l_client_lang');
        $this->dropColumn('{{%leads}}', 'l_request_hash');
        $this->dropColumn('{{%leads}}', 'l_duplicate_lead_id');
        $this->dropColumn('{{%leads}}', 'l_client_ua');

        $this->dropForeignKey('FK-leads-l_duplicate_lead_id', '{{%leads}}');
        $this->dropIndex('IND-leads-l_request_hash', '{{%leads}}');
    }


}
