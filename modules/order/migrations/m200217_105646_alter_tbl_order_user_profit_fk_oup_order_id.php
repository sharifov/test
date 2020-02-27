<?php
namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m200217_105646_alter_tbl_order_user_profit_fk_oup_order_id
 */
class m200217_105646_alter_tbl_order_user_profit_fk_oup_order_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropForeignKey('fk-order_user_profit-oup_order_id', '{{%order_user_profit}}');
		$this->addForeignKey('fk-order_user_profit-oup_order_id', '{{%order_user_profit}}', 'oup_order_id', '{{%order}}', 'or_id', 'CASCADE', 'CASCADE');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('fk-order_user_profit-oup_order_id', '{{%order_user_profit}}');
		$this->addForeignKey('fk-order_user_profit-oup_order_id', '{{%order_user_profit}}', 'oup_order_id', '{{%order}}', 'or_id');
	}
}
