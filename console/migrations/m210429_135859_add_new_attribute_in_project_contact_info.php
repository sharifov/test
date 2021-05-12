<?php

use yii\db\Migration;

/**
 * Class m210429_135859_add_new_attribute_in_project_contact_info
 */
class m210429_135859_add_new_attribute_in_project_contact_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = \common\models\Project::find()->all();
        foreach ($projects as $project) {
            $contactInfo = empty($project->contact_info) ? $project->contactInfo : \frontend\helpers\JsonHelper::decode($project->contact_info);
            $contactInfo['email_from_name'] = $project->name;
            $project->contact_info = \frontend\helpers\JsonHelper::encode($contactInfo);
            $project->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
