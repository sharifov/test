<?php

use common\models\Department;
use common\models\Setting;
use common\models\SettingCategory;
use frontend\helpers\JsonHelper;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeService;
use src\entities\cases\CaseCategory;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m211012_072848_add_case_category_voluntary_exchange
 */
class m211012_072848_add_case_category_voluntary_exchange extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Cases');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'voluntary_exchange_case_category',
                's_name' => 'Voluntary Exchange case category',
                's_type' => Setting::TYPE_STRING,
                's_value' => CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Case category for Voluntary Exchange processing flow',
            ]
        );

        $query = (new Query())->select(['id', 'p_params_json'])->from('projects');
        foreach ($query->all() as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            $settings['object']['case']['sendEmailOnApiCaseCreate'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY] = [
                'enabled' => false,
                'emailFrom' => '',
                'emailFromName' => '',
                'templateTypeKey' => CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY
            ];

            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
        }

        if (!CaseCategory::findOne(['cc_key' => CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY])) {
            $caseCategory = new CaseCategory();
            $caseCategory->cc_key = CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY;
            $caseCategory->cc_name = 'Voluntary Exchange';
            $caseCategory->cc_enabled = false;
            $caseCategory->cc_system = true;
            $caseCategory->cc_dep_id = Department::DEPARTMENT_SUPPORT;
            if (!$caseCategory->save()) {
                $message['errors'] = $caseCategory->getErrors();
                $message['attributes'] = $caseCategory->getAttributes();
                \Yii::error($message, 'migrate:add_case_category_voluntary_exchange:throwable');
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'voluntary_exchange_case_category',
        ]]);

        $query = (new Query())->select(['id', 'p_params_json'])->from('projects');
        foreach ($query->all() as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['object']['case']['sendEmailOnApiCaseCreate'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY])) {
                unset($settings['object']['case']['sendEmailOnApiCaseCreate'][CaseVoluntaryExchangeService::CASE_CREATE_CATEGORY_KEY]);
                $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
