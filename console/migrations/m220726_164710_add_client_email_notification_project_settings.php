<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220726_164710_add_client_email_notification_project_settings
 */
class m220726_164710_add_client_email_notification_project_settings extends Migration
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

            if (!isset($settings['clientNotification']['productQuoteChangeCreatedEvent']['sendEmailNotification'])) {
                $settings['clientNotification']['productQuoteChangeCreatedEvent']['sendEmailNotification'] = [
                    'enabled' => false,
                    'emailFrom' => null,
                    'emailFromName' => null,
                    'messageTemplateKey' => null,
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

            if (isset($settings['clientNotification']['productQuoteChangeCreatedEvent']['sendEmailNotification'])) {
                unset($settings['clientNotification']['productQuoteChangeCreatedEvent']['sendEmailNotification']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
