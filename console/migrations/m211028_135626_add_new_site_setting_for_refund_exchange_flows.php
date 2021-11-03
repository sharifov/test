<?php

use yii\db\Migration;

/**
 * Class m211028_135626_add_new_site_setting_for_refund_exchange_flows
 */
class m211028_135626_add_new_site_setting_for_refund_exchange_flows extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'product_quote_change_client_status_mapping',
                's_name' => 'Voluntary change client statuses map',
                's_type' => \common\models\Setting::TYPE_ARRAY,
                's_value' => json_encode([]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_description' => 'Used to send status in Webhook for OTA in change flow',
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'product_quote_refund_client_status_mapping',
                's_name' => 'Voluntary refund client statuses map',
                's_type' => \common\models\Setting::TYPE_ARRAY,
                's_value' => json_encode([]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_description' => 'Used to send status in Webhook for OTA in refund flow',
            ]
        );

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
            'product_quote_change_client_status_mapping',
            'product_quote_refund_client_status_mapping'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
