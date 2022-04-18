<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m211019_095758_add_setting_to_voluntary_exchange_bo_endpoint
 */
class m211019_095758_add_setting_to_voluntary_exchange_bo_endpoint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'voluntary_exchange_bo_endpoint',
                's_name' => 'Voluntary exchange BO endpoint',
                's_type' => Setting::TYPE_STRING,
                's_value' => '',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_description' => 'Voluntary Exchange Back Office endpoint in API create flow. (If empty request not send)',
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
            'voluntary_exchange_bo_endpoint',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
