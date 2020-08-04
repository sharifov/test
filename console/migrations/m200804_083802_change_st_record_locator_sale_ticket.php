<?php

use yii\db\Migration;

/**
 * Class m200804_083802_change_st_record_locator_sale_ticket
 */
class m200804_083802_change_st_record_locator_sale_ticket extends Migration
{
    public function safeUp()
    {
		$this->alterColumn('{{%sale_ticket}}', 'st_record_locator', $this->string(20));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%sale_ticket}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('{{%sale_ticket}}', 'st_record_locator', $this->string(8));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%sale_ticket}}');
    }
}
