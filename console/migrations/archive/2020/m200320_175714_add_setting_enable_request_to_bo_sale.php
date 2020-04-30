<?php

use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m200320_175714_add_setting_enable_request_to_bo_sale
 */
class m200320_175714_add_setting_enable_request_to_bo_sale extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = SettingCategory::findOne(['sc_name' => 'Api']);

        $this->insert('{{%setting}}', [
            's_key' => 'enable_request_to_bo_sale',
            's_name' => 'Enable requests to BO sale',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 1,
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
            'enable_request_to_bo_sale'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }


}
