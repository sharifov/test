<?php

use yii\db\Migration;

/**
 * Class m201111_110108_add_column_c_data_json_tbl_call
 */
class m201111_110108_add_column_c_data_json_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_data_json', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'c_data_json');
    }
}
