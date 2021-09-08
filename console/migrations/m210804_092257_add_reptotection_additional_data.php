<?php

use common\models\Setting;
use common\models\SettingCategory;
use frontend\helpers\JsonHelper;
use modules\flight\src\useCases\reprotectionCreate\service\ReprotectionCreateService;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class m210804_092257_add_reptotection_additional_data
 */
class m210804_092257_add_reptotection_additional_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'is_automate', $this->boolean()->defaultValue(false));
        $this->createIndex('IND-cases-is_automate', '{{%cases}}', ['is_automate']);

        $settingCategory = SettingCategory::getOrCreateByName('Cases');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'reprotection_case_category',
                's_name' => 'Reprotection case category',
                's_type' => Setting::TYPE_STRING,
                's_value' => ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Case category for Reprotection processing flow',
            ]
        );

        $query = (new Query())->select(['id', 'p_params_json'])->from('projects');
        foreach ($query->all() as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (!$majorChange = ArrayHelper::getValue($settings, 'object.case.sendEmailOnApiCaseCreate.major_change')) {
                $settings['object']['case']['sendEmailOnApiCaseCreate'][ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE] = [
                    'enabled' => false,
                    'emailFrom' => '',
                    'emailFromName' => '',
                    'templateTypeKey' => ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE
                ];
            } else {
                $settings['object']['case']['sendEmailOnApiCaseCreate'][ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE] = [
                    'enabled' => false,
                    'emailFrom' => $majorChange['emailFrom'],
                    'emailFromName' => $majorChange['emailFromName'],
                    'templateTypeKey' => ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE
                ];
            }
            $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
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
        $this->dropIndex('IND-cases-is_automate', '{{%cases}}');
        $this->dropColumn('{{%cases}}', 'is_automate');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'reprotection_case_category',
        ]]);

        $query = (new Query())->select(['id', 'p_params_json'])->from('projects');
        foreach ($query->all() as $project) {
            $settings = JsonHelper::decode($project['p_params_json']);

            if (isset($settings['object']['case']['sendEmailOnApiCaseCreate'][ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE])) {
                unset($settings['object']['case']['sendEmailOnApiCaseCreate'][ReprotectionCreateService::CASE_CATEGORY_SCHEDULE_CHANGE]);
                $this->update('projects', ['p_params_json' => $settings], ['id' => $project['id']]);
            }
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
