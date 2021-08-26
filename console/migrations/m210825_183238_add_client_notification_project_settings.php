<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210825_183238_add_client_notification_project_settings
 */
class m210825_183238_add_client_notification_project_settings extends Migration
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

            if (!isset($settings['clientNotification']['productQuoteChange'])) {
                $settings['clientNotification']['productQuoteChange'] = [
                    'sendPhoneNotification' => [
                        'enabled' => false,
                        'phoneFrom' => null,
                        'messageSay' => 'You have a schedule change for your flight. Please check your email for more details.',
                        'messageSayVoice' => 'alice',
                        'messageSayLanguage' => 'en-US',
                        'fileUrl' => null,
                        'messageTemplateKey' => null,
                    ],
                    'sendSmsNotification' => [
                        'enabled' => false,
                        'phoneFrom' => null,
                        'nameFrom' => null,
                        'message' => 'You have a schedule change for your flight. Please check your email for more details.',
                        'messageTemplateKey' => null,
                    ],
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

            if (isset($settings['clientNotification']['productQuoteChange'])) {
                unset($settings['clientNotification']['productQuoteChange']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
