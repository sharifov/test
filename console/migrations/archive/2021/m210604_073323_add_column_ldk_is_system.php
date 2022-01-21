<?php

use yii\db\Migration;

/**
 * Class m210604_073323_add_column_ldk_is_system
 */
class m210604_073323_add_column_ldk_is_system extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_data_key}}', 'ldk_is_system', $this->boolean()->defaultValue(false));

        Yii::$app->db->createCommand()->upsert('lead_data_key', [
            'ldk_key' => 'example_system_key',
            'ldk_name' => 'Example system key (only for test)',
            'ldk_enable' => true,
            'ldk_is_system' => true,
            'ldk_created_dt' => date('Y-m-d H:i:s')
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%lead_data_key}}', ['IN', 'ldk_key', [
            'example_system_key'
        ]]);
        $this->dropColumn('{{%lead_data_key}}', 'ldk_is_system');
    }
}
