<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210719_084826_alter_projects_configs
 */
class m210719_084826_alter_projects_configs extends Migration
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

            if (isset($settings['airSearch']['sid'])) {
                $settings['airSearch']['cid'] = $settings['airSearch']['sid'] ?? "";
                unset($settings['airSearch']['sid']);
            } else {
                $settings['airSearch']['cid'] = "";
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

            if (isset($settings['airSearch']['cid'])) {
                $settings['airSearch']['sid'] = $settings['airSearch']['cid'] ?? "";
                unset($settings['airSearch']['cid']);
            } else {
                $settings['airSearch']['sid'] = "";
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
