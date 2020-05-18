<?php

use yii\db\Migration;

/**
 * Class m181001_130325_add_column_grade_answered_tbl_lead
 */
class m181001_130325_add_column_grade_answered_tbl_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_answered', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%leads}}', 'l_grade', $this->tinyInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_answered');
        $this->dropColumn('{{%leads}}', 'l_grade');
    }

}
