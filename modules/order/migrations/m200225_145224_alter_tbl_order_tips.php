<?php
namespace modules\order\migrations;

use yii\db\Migration;

/**
 * Class m200225_145224_alter_tbl_order_tips
 */
class m200225_145224_alter_tbl_order_tips extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->truncateTable('{{%order_tips}}');
		$this->dropForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}');
		$this->dropColumn('{{%order_tips}}', 'ot_id');
    	$this->alterColumn('{{%order_tips}}', 'ot_order_id', $this->primaryKey());
    	$this->addForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}', 'ot_order_id', '{{%order}}', 'or_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}');
    	$this->alterColumn('{{%order_tips}}', 'ot_order_id', $this->integer());
    	$this->dropPrimaryKey('PRIMARY', '{{%order_tips}}');
    	$this->addColumn('{{%order_tips}}', 'ot_id', $this->primaryKey()->first());
		$this->addForeignKey('FK-order_tips-ot_order_id', '{{%order_tips}}', 'ot_order_id', '{{%order}}', 'or_id');
	}
}
