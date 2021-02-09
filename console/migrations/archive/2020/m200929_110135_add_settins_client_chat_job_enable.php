<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200929_110135_add_settins_client_chat_job_enable
 */
class m200929_110135_add_settins_client_chat_job_enable extends Migration
{
    private string $categoryName = 'Client Chat';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$settingCategory = SettingCategory::findOne(['sc_name' => $this->categoryName])) {
            $settingCategory = new SettingCategory();
            $settingCategory->sc_name = $this->categoryName;
            $settingCategory->save();
        }

        $this->insert('{{%setting}}', [
            's_key' => 'enable_client_chat_job',
            's_name' => 'Enable Client Chat Job',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->addColumn('{{%client_chat_request}}', 'ccr_job_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_request}}', 'ccr_job_id');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'enable_client_chat_job',
        ]]);

        if ($settingCategory = SettingCategory::findOne(['sc_name' => $this->categoryName])) {
            $settingCategory->delete();
        }
    }
}
