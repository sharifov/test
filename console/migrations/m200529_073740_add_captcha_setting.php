<?php

use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m200529_073740_add_captcha_setting
 */
class m200529_073740_add_captcha_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($category = SettingCategory::findOne(['sc_name' => 'Two factor auth'])) {
            $category->sc_name = 'Authentication';
            $category->save();
        }
        $name = 'Captcha Login Enable';
        $this->insert('{{%setting}}', [
            's_key' => Inflector::slug($name, '_'),
            's_name' => $name,
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);
        $name = 'Captcha Login Attemps';
        $this->insert('{{%setting}}', [
            's_key' => Inflector::slug($name, '_'),
            's_name' =>$name,
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);
        $name = 'User Notify failed login attemps';
        $this->insert('{{%setting}}', [
            's_key' => Inflector::slug($name, '_'),
            's_name' => $name,
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
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
        if ($category = SettingCategory::findOne(['sc_name' => 'Authentication'])) {
            $category->sc_name = 'Two factor auth';
            $category->save();
        }

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'enable_request_to_bo_sale'
        ]]);

    }
}
