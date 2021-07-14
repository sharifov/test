<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210714_064748_add_new_option_to_projects_settings
 */
class m210714_064748_add_new_option_to_projects_settings extends Migration
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

            if (!isset($settings['airSearch'])) {
                $settings['airSearch']['sid'] = "";
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

            if (isset($settings['airSearch'])) {
                unset($settings['airSearch']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
