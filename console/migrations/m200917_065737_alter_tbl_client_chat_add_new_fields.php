<?php

use yii\db\Migration;

/**
 * Class m200917_065737_alter_tbl_client_chat_add_new_fields
 */
class m200917_065737_alter_tbl_client_chat_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%client_chat}}', 'cch_source_type_id', $this->tinyInteger(1));
		$this->addColumn('{{%client_chat}}', 'cch_missed', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%client_chat}}', 'cch_source_type_id');
    	$this->dropColumn('{{%client_chat}}', 'cch_missed');
    }
}
