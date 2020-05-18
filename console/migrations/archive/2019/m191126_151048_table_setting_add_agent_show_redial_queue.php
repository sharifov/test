<?php

use common\models\Setting;
use yii\db\Migration;

/**
 * Class m191126_151048_table_setting_add_agent_show_redial_queue
 */
class m191126_151048_table_setting_add_agent_show_redial_queue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'agent_show_redial_queue',
            's_name' => 'Agent show redial queue',
            's_type' => Setting::TYPE_BOOL,
            's_value' => true,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
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
            'agent_show_redial_queue'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
