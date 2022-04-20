<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m211224_112300_add_project_params_sms_subscribe
 */
class m211224_112300_add_project_params_sms_subscribe extends Migration
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
            if (!isset($settings['sms']['subscribe'])) {
                $settings['sms']['subscribe'] = [
                    'phone_from' => '',
                    'subscribe_code' => '',
                    'unsubscribe_code' => '',
                    'subscribe_template' => '',
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
            if (isset($settings['sms']['subscribe'])) {
                unset($settings['sms']['subscribe']);
            }
            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
