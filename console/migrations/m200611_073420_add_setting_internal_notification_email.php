<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200611_073420_add_setting_internal_notification_email
 */
class m200611_073420_add_setting_internal_notification_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = SettingCategory::findOne(['sc_name' => 'User']);

        $this->insert('{{%setting}}', [
            's_key' => 'internal_notification_for_user_email',
            's_name' => 'Internal notification for User Email',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
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
            'internal_notification_for_user_email'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
