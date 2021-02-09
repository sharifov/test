<?php

use yii\db\Migration;

/**
 * Class m200618_115456_add_settings_allow_cross_department_call_transfers
 */
class m200618_115456_add_settings_allow_cross_department_call_transfers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'allow_cross_department_call_transfers',
            's_name' => 'Allow cross department call transfers',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => null
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
            'allow_cross_department_call_transfers'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
