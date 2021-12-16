<?php

use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210906_151906_change_project_settings_client_notification_name
 */
class m210906_151906_change_project_settings_client_notification_name extends Migration
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

            if (isset($settings['clientNotification']['productQuoteChangeCreatedEvent'])) {
                $settings['clientNotification']['productQuoteChangeAutoDecisionPendingEvent'] = $settings['clientNotification']['productQuoteChangeCreatedEvent'];
                unset($settings['clientNotification']['productQuoteChangeCreatedEvent']);
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

            if (isset($settings['clientNotification']['productQuoteChangeAutoDecisionPendingEvent'])) {
                $settings['clientNotification']['productQuoteChangeCreatedEvent'] = $settings['clientNotification']['productQuoteChangeAutoDecisionPendingEvent'];
                unset($settings['clientNotification']['productQuoteChangeAutoDecisionPendingEvent']);
            }

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
