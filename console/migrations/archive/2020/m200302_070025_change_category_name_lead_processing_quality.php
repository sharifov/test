<?php

use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory;
use yii\db\Migration;

/**
 * Class m200302_070025_change_category_name_lead_processing_quality
 */
class m200302_070025_change_category_name_lead_processing_quality extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        QaTaskCategory::updateAll(['tc_key' => 'qa_lead_processing_quality'], 'tc_key = \'lead_processing_quality\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        QaTaskCategory::updateAll(['tc_key' => 'lead_processing_quality'], 'tc_key = \'qa_lead_processing_quality\'');
    }
}
