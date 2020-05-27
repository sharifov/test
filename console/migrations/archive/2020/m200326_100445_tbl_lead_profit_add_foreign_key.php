<?php

use yii\db\Migration;

/**
 * Class m200326_100445_tbl_lead_profit_add_foreign_key
 */
class m200326_100445_tbl_lead_profit_add_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addForeignKey('FK-lead_profit_type-create_user', '{{%lead_profit_type}}', 'lpt_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-lead_profit_type-update_user', '{{%lead_profit_type}}', 'lpt_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-lead_profit_type-create_user', '{{%lead_profit_type}}');
    	$this->dropForeignKey('FK-lead_profit_type-update_user', '{{%lead_profit_type}}');
    }
}
