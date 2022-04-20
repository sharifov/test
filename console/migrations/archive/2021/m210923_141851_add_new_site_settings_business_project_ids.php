<?php

use yii\db\Migration;

/**
 * Class m210923_141851_add_new_site_settings_business_project_ids
 */
class m210923_141851_add_new_site_settings_business_project_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'business_project_ids',
                's_name' => 'Business project ids',
                's_type' => \common\models\Setting::TYPE_ARRAY,
                's_value' => \frontend\helpers\JsonHelper::encode([7]),
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
            'business_project_ids',
        ]]);
    }
}
