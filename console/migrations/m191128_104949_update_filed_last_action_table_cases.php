<?php

use sales\entities\cases\Cases;
use yii\db\Migration;

/**
 * Class m191128_104949_update_filed_last_action_table_cases
 */
class m191128_104949_update_filed_last_action_table_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $cases = Cases::find()->select(['cs_updated_dt'])->andWhere(['IS', 'cs_last_action_dt', null])->asArray()->all();
        foreach ($cases as $case) {
            Cases::updateAll(['cs_last_action_dt' => $case['cs_updated_dt']], ['IS', 'cs_last_action_dt', null]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
