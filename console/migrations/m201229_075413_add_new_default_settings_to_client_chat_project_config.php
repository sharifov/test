<?php

use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfigDefaultParams;
use yii\db\Migration;
use frontend\helpers\JsonHelper;

/**
 * Class m201229_075413_add_new_default_settings_to_client_chat_project_config
 */
class m201229_075413_add_new_default_settings_to_client_chat_project_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $projects = ClientChatProjectConfig::find()->all();

        foreach ($projects as $project) {
            $params = JsonHelper::decode($project->ccpc_params_json);

            if (!isset($params['registrationEnabled'])) {
                $params['registrationEnabled'] = ClientChatProjectConfigDefaultParams::getParams()['registrationEnabled'];

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
