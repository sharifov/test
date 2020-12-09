<?php

use frontend\helpers\JsonHelper;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m201209_081159_add_cc_project_config_auto_message
 */
class m201209_081159_add_cc_project_config_auto_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $autoMessage['autoMessage'] = [
            'enabled' => false,
            'message' => '',
            'delay' => 1,
            'repeatDelay' => 1,
            'botName' => '',
            'botAvatar' => ''
        ];

        foreach (ClientChatProjectConfig::find()->all() as $projectConfig) {
            /** @var ClientChatProjectConfig $projectConfig */
            $paramsJson = JsonHelper::decode($projectConfig->ccpc_params_json);
            $projectConfig->ccpc_params_json = JsonHelper::encode(ArrayHelper::merge($paramsJson, $autoMessage));

            if (!$projectConfig->save()) {
                echo 'ClientChatProjectConfig (' . $projectConfig->ccpc_project_id . ') not saved';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach (ClientChatProjectConfig::find()->all() as $projectConfig) {
            /** @var ClientChatProjectConfig $projectConfig */
            $paramsJson = JsonHelper::decode($projectConfig->ccpc_params_json);
            unset($paramsJson['autoMessage']);
            $projectConfig->ccpc_params_json = JsonHelper::encode($paramsJson);

            if (!$projectConfig->save()) {
                echo 'ClientChatProjectConfig (' . $projectConfig->ccpc_project_id . ') not saved';
            }
        }
    }
}
