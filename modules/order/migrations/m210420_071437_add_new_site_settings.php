<?php

namespace modules\order\migrations;

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m210420_071437_add_new_site_settings
 */
class m210420_071437_add_new_site_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'webhook_order_update_bo_enabled',
                's_name' => 'Webhook to BackOffice on order update',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'webhook_order_update_hybrid_enabled',
                's_name' => 'Webhook to Hybrid on order update',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'webhook_order_update_bo_endpoint',
                's_name' => 'Webhook endpoint to BackOffice on order update',
                's_type' => Setting::TYPE_STRING,
                's_value' => '',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'webhook_order_update_hybrid_endpoint',
                's_name' => 'Webhook endpoint to Hybrid on order update',
                's_type' => Setting::TYPE_STRING,
                's_value' => '/offer/v1/order-update-status',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'webhook_order_update_bo_enabled',
            'webhook_order_update_hybrid_enabled',
            'webhook_order_update_bo_endpoint',
            'webhook_order_update_hybrid_endpoint',
        ]]);
    }
}
