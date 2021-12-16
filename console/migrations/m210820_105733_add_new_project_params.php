<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210820_105733_add_new_project_params
 */
class m210820_105733_add_new_project_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = new \yii\db\Query();
        $query->select(['id', 'p_params_json']);
        $query->from('projects');

        $projects = $query->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (!isset($settings['object']['quote']['enable_random_project_provider_id'])) {
                $settings['object']['quote']['enable_random_project_provider_id'] = false;
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $query = new \yii\db\Query();
        $query->select(['id', 'p_params_json']);
        $query->from('projects');

        $projects = $query->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['object']['quote']['enable_random_project_provider_id'])) {
                unset($settings['object']['quote']['enable_random_project_provider_id']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
