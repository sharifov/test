<?php

use common\models\Department;
use src\entities\cases\CaseCategoryKeyDictionary;
use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220914_080519_add_case_category_cross_sale
 */
class m220914_080519_add_case_category_cross_sale extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%case_category}}', [
            'cc_key' => CaseCategoryKeyDictionary::CROSS_SALE,
            'cc_name' => 'Cross Sale',
            'cc_enabled' => true,
            'cc_system' => true,
            'cc_dep_id' => Department::DEPARTMENT_CROSS_SELL,
            'cc_lft' => 1,
            'cc_rgt' => 2,
            'cc_depth' => 0,
        ]);
        /*update cc_tree attribute for a new cross_sale category with it's primary key */
        $caseCategory = (new \yii\db\Query())->from('{{%case_category}}')->where(['cc_key' => CaseCategoryKeyDictionary::CROSS_SALE])->limit(1)->one();
        (new Query())->createCommand()->update('{{%case_category}}', [
            'cc_tree' => $caseCategory['cc_id']
        ], [
            'cc_key' => CaseCategoryKeyDictionary::CROSS_SALE
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%case_category}}', ['IN', 'cc_key', [
            CaseCategoryKeyDictionary::CROSS_SALE,
        ]]);
    }
}
