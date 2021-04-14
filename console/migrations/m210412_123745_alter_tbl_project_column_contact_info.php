<?php

use yii\db\Migration;

/**
 * Class m210412_123745_alter_tbl_project_column_contact_info
 */
class m210412_123745_alter_tbl_project_column_contact_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = \common\models\Project::find()->all();
        foreach ($projects as $project) {
            $contactInfo = empty($project->contact_info) ? $project->contactInfo : \frontend\helpers\JsonHelper::decode($project->contact_info);
            $contactInfo['email_no_reply_prefix'] = 'no-reply';
            $contactInfo['email_postfix'] = ((explode('@', $contactInfo['email'] ?? ''))[1] ?? '');
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
