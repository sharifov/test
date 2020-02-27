<?php
namespace modules\order\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200214_142333_create_tbl_order_user_profit
 */
class m200214_142333_create_tbl_order_user_profit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}
		$this->createTable('{{%order_user_profit}}', [
			'oup_order_id' => $this->integer()->notNull(),
			'oup_user_id' => $this->integer()->notNull(),
			'oup_percent' => $this->tinyInteger()->notNull(),
			'oup_amount' => $this->decimal(8, 2),
			'oup_created_dt' => $this->dateTime(),
			'oup_updated_dt' => $this->dateTime(),
			'oup_created_user_id' => $this->integer(),
			'oup_updated_user_id' => $this->integer()
		], $tableOptions);

		$this->addPrimaryKey('pk-order_user_profit-oup_order_id-oup_user_id', '{{%order_user_profit}}', ['oup_order_id', 'oup_user_id']);

		$this->addForeignKey('fk-order_user_profit-oup_order_id', '{{%order_user_profit}}', 'oup_order_id', '{{%order}}', 'or_id');

		$this->addForeignKey('fk-order_user_profit-oup_user_id', '{{%order_user_profit}}', 'oup_user_id', '{{%employees}}', 'id');

		$this->addForeignKey('fk-order_user_profit-oup_created_user_id', '{{%order_user_profit}}', 'oup_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		$this->addForeignKey('fk-order_user_profit-oup_updated_user_id', '{{%order_user_profit}}', 'oup_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%order_status_log}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('fk-order_user_profit-oup_order_id', '{{%order_user_profit}}');
    	$this->dropForeignKey('fk-order_user_profit-oup_user_id', '{{%order_user_profit}}');
    	$this->dropForeignKey('fk-order_user_profit-oup_created_user_id', '{{%order_user_profit}}');
    	$this->dropForeignKey('fk-order_user_profit-oup_updated_user_id', '{{%order_user_profit}}');

    	$this->dropTable('{{%order_user_profit}}');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%order_status_log}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

}
