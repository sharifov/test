<?php

use common\models\Department;
use modules\flight\src\useCases\reprotectionCreate\service\ReprotectionCreateService;
use src\entities\cases\CaseCategory;
use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m210803_081032_add_case_category_flight_schedule_change
 */
class m210803_081032_add_case_category_flight_schedule_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (ReprotectionCreateService::CASE_CATEGORY_LIST as $key => $name) {
            if (!CaseCategory::findOne(['cc_key' => $key])) {
                $caseCategory = new CaseCategory();
                $caseCategory->cc_key = $key;
                $caseCategory->cc_name = $name;
                $caseCategory->cc_enabled = false;
                $caseCategory->cc_system = true;
                $caseCategory->cc_dep_id = Department::DEPARTMENT_SUPPORT;
                if (!$caseCategory->save()) {
                    $message['errors'] = $caseCategory->getErrors();
                    $message['attributes'] = $caseCategory->getAttributes();
                    \Yii::error(
                        $message,
                        'Migrate:add_case_category_flight_schedule_change:throwable'
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_081032_add_case_category_flight_schedule_change cannot be reverted.\n";
    }
}
