<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210922_114758_add_project_params_allow_auto_lead_create
 */
class m210922_114758_add_project_params_allow_auto_lead_create extends Migration
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

            if (!isset($settings['object']['lead']['allow_auto_lead_create'])) {
                $settings['object']['lead']['allow_auto_lead_create'] = true;
            }
            if (!isset($settings['object']['case']['allow_auto_case_create'])) {
                $settings['object']['case']['allow_auto_case_create'] = true;
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

            if (isset($settings['object']['lead']['allow_auto_lead_create'])) {
                unset($settings['object']['lead']['allow_auto_lead_create']);
            }
            if (isset($settings['object']['case']['allow_auto_case_create'])) {
                unset($settings['object']['case']['allow_auto_case_create']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
