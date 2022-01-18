<?php

use common\models\Department;
use common\models\Lead;
use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210525_061226_add_setting_lead_api_google
 */
class m210525_061226_add_setting_lead_api_google extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Api');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'lead_api_google',
                's_name' => 'Lead API google ',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'allow_create' => 0,
                    'default_status_id' => Lead::STATUS_PENDING,
                    'default_department_id' => Department::DEPARTMENT_SALES,
                ]),
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
            'lead_api_google',
        ]]);
    }
}
