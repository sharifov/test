<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200323_142031_add_to_setting_create_case_only_department_email
 */
class m200323_142031_add_to_setting_create_case_only_department_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = SettingCategory::findOne(['sc_name' => 'Create']);

        $this->insert('{{%setting}}', [
            's_key' => 'create_case_only_department_email',
            's_name' => 'Create Case only department email',
            's_type' => Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
            's_category_id' => $category ? $category->sc_id : null,
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'create_case_only_department_email'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
