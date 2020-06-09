<?php

use yii\db\Migration;

/**
 * Class m200528_064104_add_setting_service_fee
 */
class m200528_064104_add_setting_service_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'quote_service_fee_percent',
            's_name' => 'Quote Service Fee Percent',
            's_type' => \common\models\Setting::TYPE_DOUBLE,
            's_value' => 3.5,
            's_updated_dt' => date('Y-m-d H:i:s'),
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
            'enable_request_to_bo_sale'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
