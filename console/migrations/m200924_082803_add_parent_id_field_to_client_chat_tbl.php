<?php

use yii\db\Migration;

/**
 * Class m200924_082803_add_parent_id_field_to_client_chat_tbl
 */
class m200924_082803_add_parent_id_field_to_client_chat_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%client_chat}}', 'cch_parent_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%client_chat}}', 'cch_parent_id');
    }
}
