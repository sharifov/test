<?php

use common\models\Project;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m210422_154013_update_project_contact_info
 */
class m210422_154013_update_project_contact_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = Project::find()->all();
        foreach ($projects as $project) {
            $contactInfo = ArrayHelper::merge($project->contactInfo->attributes, json_decode($project->contact_info));
            $project->contact_info = $contactInfo;
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
