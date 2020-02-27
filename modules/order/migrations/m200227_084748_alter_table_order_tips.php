<?php
namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m200227_084748_alter_table_order_tips
 */
class m200227_084748_alter_table_order_tips extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}');
		$this->addForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}', 'ot_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
		$this->dropForeignKey('fk-order_tips_user_profit-otup_order_id', '{{%order_tips_user_profit}}');
		$this->addForeignKey('fk-order_tips_user_profit-otup_order_id', '{{%order_tips_user_profit}}', 'otup_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
