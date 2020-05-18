<?php

use yii\db\Migration;

/**
 * Class m200110_132843_add_column_e_email_body_blob_tbl_email
 */
class m200110_132843_add_column_e_email_body_blob_tbl_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email}}', 'e_email_body_blob', $this->binary());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%email}}', 'e_email_body_blob');
    }
}
