<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m210412_134919_add_new_site_settings_params
 */
class m210412_134919_add_new_site_settings_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'order_processing_email_template_key',
                's_name' => ' Order processing confirmation email template key',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'order_status',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'order_complete_email_template_key',
                's_name' => ' Order complete confirmation email template key',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'order_status',
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
            'order_processing_email_template_key',
            'order_complete_email_template_key'
        ]]);
    }
}
