<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m220517_104326_add_agent_call_queue_job_setting
 */
class m220517_104326_add_agent_call_queue_job_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Call');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'enable_agent_call_queue_job_after_change_call_status_ready',
                's_name' => 'Agent call queue job enable',
                's_type' => Setting::TYPE_BOOL,
                's_value' => 1,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'enable_agent_call_queue_job_after_change_call_status_ready',
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
