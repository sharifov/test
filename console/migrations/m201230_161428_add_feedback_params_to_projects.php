<?php

use common\models\Project;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201230_161428_add_feedback_params_to_projects
 */
class m201230_161428_add_feedback_params_to_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = Project::find()->all();
        foreach ($projects as $project) {
            /** @var Project $project */
            $params = [];
            if ($project->custom_data) {
                $params = json_decode($project->custom_data, true);
            }
            $params['object']['case']['sendFeedback'] = false;
            $params['object']['case']['feedbackTemplateTypeKey'] = '';
            $params['object']['case']['feedbackEmailFrom'] = '';
            $params['object']['case']['feedbackNameFrom'] = '';
            $params['object']['case']['feedbackBookingIdRequired'] = false;
            $project->custom_data = json_encode($params);
            if (!$project->save(false)) {
                VarDumper::dump($project->getErrors());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $projects = Project::find()->all();
        foreach ($projects as $project) {
            /** @var Project $project */
            $params = [];
            if (!$project->custom_data) {
                continue;
            }
            if ($project->custom_data) {
                $params = json_decode($project->custom_data, true);
            }
            if (!array_key_exists('object', $params)) {
                continue;
            }
            unset($params['object']);
            $project->custom_data = json_encode($params);
            if (!$project->save(false)) {
                VarDumper::dump($project->getErrors());
            }
        }
    }
}
