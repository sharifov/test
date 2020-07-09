<?php

use yii\db\Migration;

/**
 * Class m200709_094651_add_column_cch_id_to_visitor_log
 */
class m200709_094651_add_column_cch_id_to_visitor_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%visitor_log}}', 'vl_cch_id', $this->integer());
		$this->addForeignKey('FK-visitor_log-vl_cch_id', '{{%visitor_log}}', ['vl_cch_id'], '{{%client_chat}}', ['cch_id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-visitor_log-vl_cch_id', '{{%visitor_log}}');
    	$this->dropColumn('{{%visitor_log}}', 'vl_cch_id');
    }
}
