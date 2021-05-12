<?php

use yii\db\Migration;

/**
 * Class m210422_143758_remove_property_from_project_contact_info
 */
class m210422_143758_remove_property_from_project_contact_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = \common\models\Project::find()->all();
        foreach ($projects as $project) {
            $contactInfo = empty($project->contact_info) ? $project->contactInfo : \frontend\helpers\JsonHelper::decode($project->contact_info);
            if (isset($contactInfo['email_postfix'])) {
                unset($contactInfo['email_postfix']);
                $project->contact_info = \frontend\helpers\JsonHelper::encode($contactInfo);
                $project->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
