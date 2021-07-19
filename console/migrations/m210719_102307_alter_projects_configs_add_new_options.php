<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210719_102307_alter_projects_configs_add_new_options
 */
class m210719_102307_alter_projects_configs_add_new_options extends Migration
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

            if (!isset($settings['object']['case']['sendEmailOnApiCaseCreate'])) {
                $settings['object']['case']['sendEmailOnApiCaseCreate'] = [
                    'major_change' => [
                        'enabled' => false,
                        'emailFrom' => '',
                        'emailFromName' => '',
                        'templateTypeKey' => 'schd_mjr'
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
        $query = new \yii\db\Query();
        $query->select(['id', 'p_params_json']);
        $query->from('projects');

        $projects = $query->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['object']['case']['sendEmailOnApiCaseCreate'])) {
                unset($settings['object']['case']['sendEmailOnApiCaseCreate']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
