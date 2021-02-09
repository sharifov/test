<?php

use yii\db\Migration;

/**
 * Class m201229_133306_add_column_record_disabled_tbl_conference
 */
class m201229_133306_add_column_record_disabled_tbl_conference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference}}', 'cf_recording_disabled', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%conference}}', 'cf_recording_disabled');
    }
}
