<?php

use yii\db\Migration;

/**
 * Class m200803_145148_change_css_sale_pnr
 */
class m200803_145148_change_css_sale_pnr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%case_sale}}', 'css_sale_pnr', $this->string(20));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('{{%case_sale}}', 'css_sale_pnr', $this->string(8));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }
}
