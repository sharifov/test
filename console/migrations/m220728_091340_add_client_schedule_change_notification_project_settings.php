<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220728_091340_add_client_schedule_change_notification_project_settings
 */
class m220728_091340_add_client_schedule_change_notification_project_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = new Query();
        $query->select(['id', 'p_params_json']);
        $query->from('projects');

        $projects = $query->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (!isset($settings['clientNotification']['productQuoteChangeClientRemainderNotificationEvent'])) {
                $settings['clientNotification']['productQuoteChangeClientRemainderNotificationEvent'] = [
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
                        'messageTemplateKey' => null,
                    ],
                    'sendEmailNotification' => [
                        'enabled' => false,
                        'emailFrom' => null,
                        'emailFromName' => null,
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
        $query = new Query();
        $query->select(['id', 'p_params_json']);
        $query->from('projects');

        $projects = $query->all();

        foreach ($projects as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['clientNotification']['productQuoteChangeClientRemainderNotificationEvent'])) {
                unset($settings['clientNotification']['productQuoteChangeClientRemainderNotificationEvent']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
