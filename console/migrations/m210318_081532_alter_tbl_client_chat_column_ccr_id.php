<?php

use yii\db\Migration;

/**
 * Class m210318_081532_alter_tbl_client_chat_column_ccr_id
 */
class m210318_081532_alter_tbl_client_chat_column_ccr_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%client_chat}}', 'cch_ccr_id', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%client_chat}}', 'cch_ccr_id', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210318_081532_alter_tbl_client_chat_column_ccr_id cannot be reverted.\n";

        return false;
    }
    */
}
