<?php

use yii\db\Migration;

/**
 * Class m200715_145313_add_columns_tbl_conference
 */
class m200715_145313_add_columns_tbl_conference extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%conference}}', 'cf_start_dt', $this->dateTime());
        $this->addColumn('{{%conference}}', 'cf_end_dt', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%conference}}', 'cf_start_dt');
        $this->dropColumn('{{%conference}}', 'cf_end_dt');
    }
}
