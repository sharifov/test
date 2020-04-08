<?php

use yii\db\Migration;

/**
 * Class m200408_083138_add_setting_new_communication_block_lead
 */
class m200408_083138_add_setting_new_communication_block_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insert('{{%setting}}', [
			's_key' => 'new_communication_block_lead',
			's_name' => 'Communication Log on Lead page',
			's_type' => \common\models\Setting::TYPE_BOOL,
			's_value' => 1,
			's_updated_dt' => date('Y-m-d H:i:s'),
			's_updated_user_id' => 1,
		]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->delete('{{%setting}}', ['IN', 's_key', [
			'new_communication_block_lead'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
