<?php

use common\models\Department;
use src\entities\cases\CaseCategoryDictionary;
use yii\db\Migration;

/**
 * Class m220914_080519_add_case_category_cross_sale
 */
class m220914_080519_add_case_category_cross_sale extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%case_category}}', [
            'cc_key' => CaseCategoryDictionary::CROSS_SALE,
            'cc_name' => 'Cross Sale',
            'cc_enabled' => true,
            'cc_system' => true,
            'cc_dep_id' => Department::DEPARTMENT_CROSS_SELL,
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%case_category}}', ['IN', 'cc_key', [
            CaseCategoryDictionary::CROSS_SALE,
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
