<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210810_122611_add_new_project_params_optiions
 */
class m210810_122611_add_new_project_params_optiions extends Migration
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

            if (!isset($settings['object']['case']['reprotection_quote'])) {
                $settings['object']['case']['reprotection_quote'] = [
                    'emailFrom' => '',
                    'emailFromName' => '',
                    'templateTypeKey' => 'reprotection_quote'
                ];
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

            if (isset($settings['object']['case']['reprotection_quote'])) {
                unset($settings['object']['case']['reprotection_quote']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
