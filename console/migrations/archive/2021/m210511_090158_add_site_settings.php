<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m210511_090158_add_site_settings
 */
class m210511_090158_add_site_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'phone_blacklist_enabled',
                's_name' => 'Enable phone blacklist logic',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'phone_blacklist_last_time_period',
                's_name' => 'Period for which to watch the number of records from the table',
                's_type' => Setting::TYPE_INT,
                's_value' => 1440,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'phone_blacklist_period',
                's_name' => 'Phone black list period',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    1 => 5,
                    2 => 10,
                    3 => 60
                ]),
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
            'phone_blacklist_enabled',
            'phone_blacklist_last_time_period',
            'phone_blacklist_period',
        ]]);
    }
}
