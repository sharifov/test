<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200504_073843_add_setting_allow_contact_internal
 */
class m200504_073843_add_setting_allow_contact_internal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = SettingCategory::findOne(['sc_name' => 'Enable']);

        $this->insert('{{%setting}}', [
            's_key' => 'allow_contact_internal_phone',
            's_name' => 'Allow Contact Internal Phone',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);
        $this->insert('{{%setting}}', [
            's_key' => 'allow_contact_internal_email',
            's_name' => 'Allow Contact Internal Email',
            's_type' => Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'allow_contact_internal_phone', 'allow_contact_internal_email',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
