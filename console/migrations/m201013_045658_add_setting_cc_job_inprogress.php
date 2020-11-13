<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m201013_045658_add_setting_cc_job_inprogress
 */
class m201013_045658_add_setting_cc_job_inprogress extends Migration
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
                's_key' => 'client_chat_job_hold_to_in_progress_enable',
                's_name' => 'Client Chat Job Hold to InProgress Enable',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
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
            'client_chat_job_hold_to_in_progress_enable',
        ]]);
    }
}
