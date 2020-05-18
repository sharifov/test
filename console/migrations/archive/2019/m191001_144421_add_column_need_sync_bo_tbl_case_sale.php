<?php

use yii\db\Migration;

/**
 * Class m191001_144421_add_column_need_sync_bo_tbl_case_sale
 */
class m191001_144421_add_column_need_sync_bo_tbl_case_sale extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%case_sale}}', 'css_need_sync_bo', $this->tinyInteger(1)->defaultValue(0));

		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('{{%case_sale}}', 'css_need_sync_bo');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		return true;
    }
}
