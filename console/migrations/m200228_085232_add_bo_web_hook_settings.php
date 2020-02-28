<?php

use yii\db\Migration;

/**
 * Class m200228_085232_add_bo_web_hook_settings
 */
class m200228_085232_add_bo_web_hook_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'bo_web_hook_url',
            's_name' => 'BackOffice WebHook Url',
            's_type' => \common\models\Setting::TYPE_STRING,
            's_value' => '',
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'bo_web_hook_enable',
            's_name' => 'BackOffice WebHook Url enable',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
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
            'bo_web_hook_url',
            'bo_web_hook_enable',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
