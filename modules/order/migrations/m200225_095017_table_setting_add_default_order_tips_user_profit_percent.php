<?php
namespace modules\order\migrations;

use common\models\Setting;
use Yii;
use yii\db\Migration;

/**
 * Class m200225_095017_table_setting_add_default_order_tips_user_profit_percent
 */
class m200225_095017_table_setting_add_default_order_tips_user_profit_percent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->insert('{{%setting}}', [
			's_key' => 'order_tips_user_profit_percent',
			's_name' => 'Default user profit percent for Order Tips',
			's_type' => Setting::TYPE_INT,
			's_value' => 50,
			's_updated_dt' => date('Y-m-d H:i:s'),
		]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->delete('{{%setting}}', ['IN', 's_key', [
			'order_tips_user_profit_percent'
		]]);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

}
