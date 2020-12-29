<?php

use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use yii\db\Migration;
use frontend\helpers\JsonHelper;

/**
 * Class m201229_132257_remove_endpoint_from_chat_project_config_params
 */
class m201229_132257_remove_endpoint_from_chat_project_config_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = ClientChatProjectConfig::find()->all();
        foreach ($projects as $project) {
            $params = JsonHelper::decode($project->ccpc_params_json);


            if (isset($params['endpoint'])) {
                unset($params['endpoint']);
                $project->ccpc_params_json = JsonHelper::encode($params);
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
