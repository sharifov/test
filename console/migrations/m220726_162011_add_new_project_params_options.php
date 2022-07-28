<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220726_162011_add_new_project_params_options
 */
class m220726_162011_add_new_project_params_options extends Migration
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

            if (!isset($settings['object']['case']['schedule_change'])) {
                $settings['object']['case']['schedule_change'] = [
                    'notification_intervals' => [
                        [
                            'days_from' => 0,
                            'days_to' => 10,
                            'frequency' => 1,
                        ],
                        [
                            'days_from' => 10,
                            'days_to' => 30,
                            'frequency' => 3,
                        ],
                        [
                            'days_from' => 30,
                            'days_to' => 60,
                            'frequency' => 7,
                        ]
                    ]
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

            if (isset($settings['object']['case']['schedule_change'])) {
                unset($settings['object']['case']['schedule_change']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
