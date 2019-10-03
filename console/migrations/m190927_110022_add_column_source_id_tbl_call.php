<?php

use yii\db\Migration;

/**
 * Class m190927_110022_add_column_source_id_tbl_call
 */
class m190927_110022_add_column_source_id_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%call}}', 'c_source_id', $this->integer());
		$this->addForeignKey('FK-call_c_source_id', '{{%call}}', ['c_source_id'], '{{%sources}}', ['id'], 'SET NULL', 'CASCADE');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-call_c_source_id', '{{%call}}');
    	$this->dropColumn('{{%call}}', 'c_source_id');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

        return false;
    }
}
