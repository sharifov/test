<?php

use yii\db\Migration;

/**
 * Class m190103_111117_add_cols_emails_names_tbl_email
 */
class m190103_111117_add_cols_emails_names_tbl_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email}}', 'e_email_from_name', $this->string(255));
        $this->addColumn('{{%email}}', 'e_email_to_name', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%email}}', 'e_email_from_name');
        $this->dropColumn('{{%email}}', 'e_email_to_name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190103_111117_add_cols_emails_names_tbl_email cannot be reverted.\n";

        return false;
    }
    */
}
