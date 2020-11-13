<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m201022_064417_edit_site_setting_client_chat_soft_close_timeout_minutes
 */
class m201022_064417_edit_site_setting_client_chat_soft_close_timeout_minutes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_soft_close_timeout_minutes'
        ]]);

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_chat_soft_close_timeout_hours',
                's_name' => 'Client Chat Soft Close Timeout Hours',
                's_type' => Setting::TYPE_INT,
                's_value' => 1,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => null,
            ]
        );

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
    }
}
