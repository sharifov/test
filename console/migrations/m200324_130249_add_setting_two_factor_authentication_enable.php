<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200324_130249_add_setting_two_factor_authentication_enable
 */
class m200324_130249_add_setting_two_factor_authentication_enable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_profile}}', 'up_2fa_enable', $this->boolean()->defaultValue(0));
        $this->addColumn('{{%user_profile}}', 'up_2fa_secret', $this->string(50));
        $this->addColumn('{{%user_profile}}', 'up_2fa_timestamp', $this->timestamp());

        $settingCategory = new SettingCategory();
        $settingCategory->sc_name = 'Two factor auth';
        $settingCategory->save();

        $this->insert('{{%setting}}', [
            's_key' => 'two_factor_authentication_enable',
            's_name' => 'Enable two factor authentication',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'two_factor_company_name',
            's_name' => 'Two factor auth company name',
            's_type' => \common\models\Setting::TYPE_STRING,
            's_value' => 'travelinsides.com',
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'two_factor_counter',
            's_name' => 'Two factor auth counter',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profile}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_profile}}', 'up_2fa_enable');
        $this->dropColumn('{{%user_profile}}', 'up_2fa_secret');
        $this->dropColumn('{{%user_profile}}', 'up_2fa_timestamp');

        $this->delete('{{%setting_category}}', ['sc_name' => 'Two factor auth']);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'two_factor_authentication_enable', 'two_factor_company_name', 'two_factor_counter'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profile}}');
    }
}
