<?php

use yii\db\Migration;

/**
 * Class m211209_093852_remove_communication_call_log_old_setting
 */
class m211209_093852_remove_communication_call_log_old_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'new_communication_block_lead',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'new_communication_block_lead',
            's_name' => 'Communication Log on Lead page',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
