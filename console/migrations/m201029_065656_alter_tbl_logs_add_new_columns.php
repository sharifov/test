<?php

use yii\db\Migration;

/**
 * Class m201029_065656_alter_tbl_logs_add_new_columns
 */
class m201029_065656_alter_tbl_logs_add_new_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%api_log}}', 'al_created_dt', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%api_log}}', 'al_created_dt');
    }
}
