<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m201009_092841_add_setting_cc_inactive
 */
class m201009_092841_add_setting_cc_inactive extends Migration
{
    private string $categoryName = 'Client Chat';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName($this->categoryName);

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_inactive_minutes',
                's_name' => 'Client Chat inactive minutes',
                's_type' => Setting::TYPE_INT,
                's_value' => 0,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_inactive_minutes',
        ]]);
    }
}
