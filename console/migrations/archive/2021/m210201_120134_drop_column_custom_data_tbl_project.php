<?php

use yii\db\Migration;

/**
 * Class m210201_120134_drop_column_custom_data_tbl_project
 */
class m210201_120134_drop_column_custom_data_tbl_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%projects}}', 'custom_data');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%projects}}', 'custom_data', $this->text());
    }
}
