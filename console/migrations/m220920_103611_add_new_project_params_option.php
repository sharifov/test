<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220920_103611_add_new_project_params_option
 */
class m220920_103611_add_new_project_params_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = new Query();
        $query->select(['id', 'p_params_json']);
        $query->from('projects');

        $projects = $query->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (!isset($settings['object']['case']['cancel_sale'])) {
                $settings['object']['case']['cancel_sale']['reject'] = [
                    'enabled' => false,
                    'emailFrom' => '',
                    'emailFromName' => '',
                    'templateTypeKey' => 'reject_confirmation'
                ];
                $settings['object']['case']['cancel_sale']['void'] = [
                    'enabled' => false,
                    'emailFrom' => '',
                    'emailFromName' => '',
                    'templateTypeKey' => 'void_confirmation'
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
        $query = new Query();
        $query->select(['id', 'p_params_json']);
        $query->from('projects');

        $projects = $query->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['object']['case']['cancel_sale'])) {
                unset($settings['object']['case']['cancel_sale']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
