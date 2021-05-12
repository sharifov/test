<?php

use yii\db\Migration;

/**
 * Class m210426_183515_alter_column_original_fop_tbl_sale_ticket
 */
class m210426_183515_alter_column_original_fop_tbl_sale_ticket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sale_ticket}}', 'st_original_fop', $this->string(20));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%sale_ticket}}', 'st_original_fop', $this->string(5));
    }
}
