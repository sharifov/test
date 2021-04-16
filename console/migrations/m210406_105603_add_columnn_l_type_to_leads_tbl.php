<?php

use yii\db\Migration;

/**
 * Class m210406_105603_add_columnn_l_type_to_leads_tbl
 */
class m210406_105603_add_columnn_l_type_to_leads_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_type', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_type');
    }
}
