<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m211102_095755_add_new_project_params_option
 */
class m211102_095755_add_new_project_params_option extends Migration
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

            if (!isset($settings['object']['case']['voluntary_refund'])) {
                $settings['object']['case']['voluntary_refund'] = [
                    'emailFrom' => '',
                    'emailFromName' => '',
                    'templateTypeKey' => 'voluntary_refund_offer'
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

            if (isset($settings['object']['case']['voluntary_refund'])) {
                unset($settings['object']['case']['voluntary_refund']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
