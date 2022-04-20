<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m211022_103225_add_site_setting_bo_request_endpoint
 */
class m211022_103225_add_site_setting_bo_request_endpoint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'voluntary_refund_bo_endpoint',
                's_name' => 'Voluntary refund BO endpoint',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'flight-request/create-refund-order',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_description' => 'Voluntary Refund Back Office endpoint in API create flow. (If empty request not send)',
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
            'voluntary_refund_bo_endpoint',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
