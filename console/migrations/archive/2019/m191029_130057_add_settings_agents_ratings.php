<?php

use yii\db\Migration;

/**
 * Class m191029_130057_add_settings_agents_ratings
 */
class m191029_130057_add_settings_agents_ratings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'agents_ratings',
            's_name' => 'Agents ratings boards',
            's_type' => \common\models\Setting::TYPE_ARRAY,
            's_value' => json_encode([
                'finalProfit' =>  true,  //board div id
                'soldLeads' =>  true,
                'profitPerPax' =>  true,
                'tips' =>  true,
                'leadConversion' => true
            ]),
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'teams_ratings',
            's_name' => 'Teams ratings boards',
            's_type' => \common\models\Setting::TYPE_ARRAY,
            's_value' => json_encode([
                'teamsProfit' =>  true,  //board div id
                'teamsSoldLeads' =>  true,
                'teamsProfitPerPax' =>  true,
                'teamsProfitPerAgent' =>  true,
                'teamsConversion' => true
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
            'agents_ratings'
        ]]);

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'teams_ratings'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
