<?php

namespace modules\abac\migrations;

use yii\db\Migration;

/**
 * Class m220804_053347_correction_abac_permission_processing_task
 */
class m220804_053347_correction_abac_permission_processing_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220804_053347_correction_abac_permission_processing_task cannot be reverted.\n";

        return false;
    }
}
