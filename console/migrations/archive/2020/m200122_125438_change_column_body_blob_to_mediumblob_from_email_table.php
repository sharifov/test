<?php

use yii\db\Migration;

/**
 * Class m200122_125438_change_column_body_blob_to_mediumblob_from_email_table
 */
class m200122_125438_change_column_body_blob_to_mediumblob_from_email_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%email}}', 'e_email_body_blob', 'MEDIUMBLOB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%email}}', 'e_email_body_blob', $this->binary());
    }

}
