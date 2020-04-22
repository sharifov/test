<?php

use yii\db\Migration;

/**
 * Class m200408_192733_add_setting_new_communication_block_case
 */
class m200408_192733_add_setting_new_communication_block_case extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insert('{{%setting}}', [
			's_key' => 'new_communication_block_case',
			's_name' => 'Communication Log on Case page',
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
			'new_communication_block_case'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200408_192733_add_setting_new_communication_block_case cannot be reverted.\n";

        return false;
    }
    */
}
