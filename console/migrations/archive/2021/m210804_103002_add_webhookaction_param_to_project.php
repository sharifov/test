<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210804_103002_add_webhookaction_param_to_project
 */
class m210804_103002_add_webhookaction_param_to_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = (new \yii\db\Query())
            ->select(['id', 'p_params_json'])
            ->from('projects')
            ->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['webHookEndpoint'])) {
                continue;
            }

            $settings['webHookEndpoint'] = "";

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }

    public function safeDown()
    {
        $projects = (new \yii\db\Query())
            ->select(['id', 'p_params_json'])
            ->from('projects')
            ->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (!isset($settings['webHookEndpoint'])) {
                continue;
            }

            unset($settings['webHookEndpoint']);

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
