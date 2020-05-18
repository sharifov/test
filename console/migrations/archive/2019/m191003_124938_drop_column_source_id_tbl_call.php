<?php

use yii\db\Migration;

/**
 * Class m191003_124938_drop_column_source_id_tbl_call
 */
class m191003_124938_drop_column_source_id_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropForeignKey('FK-call_c_source_id', '{{%call}}');
		$this->dropColumn('{{%call}}', 'c_source_id');

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
    }
}
