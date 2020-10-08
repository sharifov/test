<?php

use yii\db\Migration;

/**
 * Class m201008_140045_alter_column_e_message_id_tbl_email
 */
class m201008_140045_alter_column_e_message_id_tbl_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%email}}', 'e_message_id', $this->string(500));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%email}}', 'e_message_id', $this->string(255));
    }
}
