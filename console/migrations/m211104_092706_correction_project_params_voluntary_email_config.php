<?php

use frontend\helpers\JsonHelper;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeService;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m211104_092706_correction_project_params_voluntary_email_config
 */
class m211104_092706_correction_project_params_voluntary_email_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = (new Query())->select(['id', 'p_params_json'])->from('projects');
        foreach ($query->all() as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['object']['case']['sendEmailOnApiCaseCreate'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY])) {
                unset($settings['object']['case']['sendEmailOnApiCaseCreate'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY]);
            }

            $settings['object']['case'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY] = [
                'enabled' => false,
                'emailFrom' => '',
                'emailFromName' => '',
                'templateTypeKey' => 'cl_exchange_email_offer'
            ];

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $query = (new Query())->select(['id', 'p_params_json'])->from('projects');
        foreach ($query->all() as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['object']['case'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY])) {
                unset($settings['object']['case'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY]);
            }

            $settings['object']['case']['sendEmailOnApiCaseCreate'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY] = [
                'enabled' => false,
                'emailFrom' => '',
                'emailFromName' => '',
                'templateTypeKey' => CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY
            ];

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }
    }
}
