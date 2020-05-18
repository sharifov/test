<?php

use yii\db\Migration;

/**
 * Class m181129_133248_add_columns_tbl_email
 */
class m181129_133248_add_columns_tbl_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email}}', 'e_message_id', $this->string(255));
        $this->addColumn('{{%email}}', 'e_ref_message_id', $this->text());
        $this->addColumn('{{%email}}', 'e_inbox_created_dt', $this->dateTime());
        $this->addColumn('{{%email}}', 'e_inbox_email_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%email}}', 'e_inbox_email_id');
        $this->dropColumn('{{%email}}', 'e_inbox_created_dt');
        $this->dropColumn('{{%email}}', 'e_ref_message_id');
        $this->dropColumn('{{%email}}', 'e_message_id');
    }

}
