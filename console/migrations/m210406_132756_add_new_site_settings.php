<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210406_132756_add_new_site_settings
 */
class m210406_132756_add_new_site_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'order_cancellation_case_enabled',
                's_name' => ' Order. Create case when cancel order in api order/cancel',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'order_cancellation_case_category_key',
                's_name' => ' Case category key. Necessary to relate case with category when cancel order api order/cancel. ',
                's_type' => Setting::TYPE_STRING,
                's_value' => '',
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
            'order_cancellation_case_enabled',
            'order_cancellation_case_category_key'
        ]]);
    }
}
