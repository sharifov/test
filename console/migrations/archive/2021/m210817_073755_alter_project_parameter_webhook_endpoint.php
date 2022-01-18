<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210817_073755_alter_project_parameter_webhook_endpoint
 */
class m210817_073755_alter_project_parameter_webhook_endpoint extends Migration
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
                $endpoint = $settings['webHookEndpoint'];

                $settings['webhook'] = [
                    'endpoint' => $endpoint,
                    'username' => '',
                    'password' => ''
                ];

                unset($settings['webHookEndpoint']);
                $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $projects = (new \yii\db\Query())
            ->select(['id', 'p_params_json'])
            ->from('projects')
            ->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['webhook'])) {
                $endpoint = $settings['webhook']['endpoint'] ?? '';

                $settings['webHookEndpoint'] = $endpoint;

                unset($settings['webhook']);
                $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
            }
        }
    }
}
