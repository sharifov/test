<?php

use yii\db\Migration;

/**
 * Class m201217_113928_add_column_cl_locale_tbl_client
 */
class m201217_113928_add_column_cl_locale_tbl_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'cl_locale', $this->string(5));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%clients}}', 'cl_locale');
    }
}
