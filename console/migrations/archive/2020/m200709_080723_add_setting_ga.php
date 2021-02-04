<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200709_080723_add_setting_ga
 * \
 *
 */
class m200709_080723_add_setting_ga extends Migration
{
    private $scName = 'Google Analytics';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = new SettingCategory();
        $settingCategory->sc_name = $this->scName;
        $settingCategory->save();

        $this->insert('{{%setting}}', [
            's_key' => 'ga_enable',
            's_name' => 'Enable Google Analytics',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'ga_create_lead',
            's_name' => 'Enable Google Analytics create lead',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'ga_send_quote',
            's_name' => 'Enable Google Analytics send quote',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $settingCategory ? $settingCategory->sc_id : null,
        ]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting_category}}', ['sc_name' => $this->scName]);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'ga_enable', 'ga_create_lead', 'ga_send_quote'
        ]]);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
