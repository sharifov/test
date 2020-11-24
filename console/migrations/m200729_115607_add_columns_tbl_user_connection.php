<?php

use yii\db\Migration;

/**
 * Class m200729_115607_add_columns_tbl_user_connection
 */
class m200729_115607_add_columns_tbl_user_connection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_connection}}', 'uc_window_state', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%user_connection}}', 'uc_window_state_dt', $this->dateTime());
        $this->addColumn('{{%user_connection}}', 'uc_idle_state', $this->boolean()->defaultValue(true));
        $this->addColumn('{{%user_connection}}', 'uc_idle_state_dt', $this->dateTime());

        $this->createIndex('IND-user_connection-uc_window_state', '{{%user_connection}}', ['uc_window_state']);
        $this->createIndex('IND-user_connection-uc_idle_state', '{{%user_connection}}', ['uc_idle_state']);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_connection}}');

        $this->insert('{{%setting}}', [
            's_key' => 'idle_monitor_enabled',
            's_name' => 'IDLE Monitor enable',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => null
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'idle_seconds',
            's_name' => 'IDLE seconds',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => null
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
        $this->dropColumn('{{%user_connection}}', 'uc_window_state');
        $this->dropColumn('{{%user_connection}}', 'uc_window_state_dt');
        $this->dropColumn('{{%user_connection}}', 'uc_idle_state');
        $this->dropColumn('{{%user_connection}}', 'uc_idle_state_dt');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_connection}}');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'idle_monitor_enabled', 'idle_seconds'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
