<?php

use yii\db\Migration;

/**
 * Class m191224_093414_add_foreign_key_to_tbl_currency_history
 */
class m191224_093414_add_foreign_key_to_tbl_currency_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addForeignKey('FK-currency_history-ch_code', '{{%currency_history}}', 'ch_code', '{{%currency}}', 'cur_code', 'CASCADE', 'CASCADE');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%currency_history}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-currency_history-ch_code', '{{%currency_history}}');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%currency_history}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
