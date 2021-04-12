<?php

namespace modules\order\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m210412_095854_add_new_setting_order_free_cancel_email_template
 */
class m210412_095854_add_new_setting_order_free_cancel_email_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'order_free_cancel_email_template_key',
            's_name' => 'Email template key to send email for free cancellation',
            's_type' => \common\models\Setting::TYPE_STRING,
            's_value' => 'order_free_cancel_success',
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
            'order_free_cancel_success'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
