<?php

use yii\db\Migration;

/**
 * Class m200901_181718_add_column_duration_tbl_conference
 */
class m200901_181718_add_column_duration_tbl_conference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference}}', 'cf_duration', $this->smallInteger(6));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%conference}}', 'cf_duration');
    }
}
