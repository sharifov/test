<?php
namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m200204_081415_create_tbl_product_type_payment_method
 */
class m200204_081415_create_tbl_product_type_payment_method extends Migration
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

		$this->createTable('{{%product_type_payment_method}}', [
			'ptpm_produt_type_id' => $this->integer()->notNull(),
			'ptpm_payment_method_id' => $this->integer()->notNull(),
			'ptpm_payment_fee_percent' => $this->decimal(5,2),
			'ptpm_payment_fee_amount' => $this->decimal(8,2),
			'ptpm_enabled' => $this->boolean()->defaultValue(false),
			'ptpm_default' => $this->boolean()->defaultValue(false),
			'ptpm_created_user_id' => $this->integer(),
			'ptpm_updated_user_id' => $this->integer(),
			'ptpm_created_dt' => $this->dateTime(),
			'ptpm_updated_dt' => $this->dateTime(),
		], $tableOptions);

		$this->addPrimaryKey('pk-product_type_payment_method', '{{%product_type_payment_method}}', ['ptpm_produt_type_id', 'ptpm_payment_method_id']);

		$this->addForeignKey(
			'fk-product_type_payment_method-product_type_id',
			'{{%product_type_payment_method}}',
			'ptpm_produt_type_id',
			'{{%product_type}}',
			'pt_id',
			'CASCADE'
		);

		$this->addForeignKey('fk-product_type_payment_method-payment_method_id',
			'{{%product_type_payment_method}}',
			'ptpm_payment_method_id',
			'{{%payment_method}}',
			'pm_id',
			'CASCADE'
		);
		$this->addForeignKey('fk-product_type_payment_method-created_user_id',
			'{{%product_type_payment_method}}',
			'ptpm_created_user_id',
			'{{%employees}}',
			'id',
			'SET NULL',
			'CASCADE'
		);

		$this->addForeignKey('fk-product_type_payment_method-updated_user_id',
			'{{%product_type_payment_method}}',
			'ptpm_updated_user_id',
			'{{%employees}}',
			'id',
			'SET NULL',
			'CASCADE'
		);


		\Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type_payment_method}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('fk-product_type_payment_method-product_type_id', '{{%product_type_payment_method}}');
    	$this->dropForeignKey('fk-product_type_payment_method-payment_method_id', '{{%product_type_payment_method}}');
    	$this->dropForeignKey('fk-product_type_payment_method-created_user_id', '{{%product_type_payment_method}}');
    	$this->dropForeignKey('fk-product_type_payment_method-updated_user_id', '{{%product_type_payment_method}}');
    	$this->dropTable('{{%product_type_payment_method}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }
}
