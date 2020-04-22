<?php

use yii\db\Migration;

/**
 * Class m191209_090045_add_settings_exclude_agent_skill
 */
class m191209_090045_add_settings_exclude_agent_skill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'exclude_agent_skill',
            's_name' => 'Exclude Agent Skill',
            's_type' => \common\models\Setting::TYPE_ARRAY,
            's_value' => json_encode([
                'junior' =>  false,
                'middle' =>  false,
                'senior' =>  false,
            ]),
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
            'exclude_agent_skill'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
