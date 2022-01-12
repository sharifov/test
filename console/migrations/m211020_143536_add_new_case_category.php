<?php

use common\models\Department;
use src\entities\cases\CaseCategory;
use yii\db\Migration;

/**
 * Class m211020_143536_add_new_case_category
 */
class m211020_143536_add_new_case_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!CaseCategory::findOne(['cc_key' => 'voluntary_refund'])) {
            $caseCategory = new CaseCategory();
            $caseCategory->cc_key = 'voluntary_refund';
            $caseCategory->cc_name = 'Voluntary Refund';
            $caseCategory->cc_enabled = false;
            $caseCategory->cc_system = true;
            $caseCategory->cc_dep_id = Department::DEPARTMENT_SUPPORT;
            if (!$caseCategory->save()) {
                $message['errors'] = $caseCategory->getErrors();
                $message['attributes'] = $caseCategory->getAttributes();
                \Yii::error($message, 'migrate:add_case_category_voluntary_refund:throwable');
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
