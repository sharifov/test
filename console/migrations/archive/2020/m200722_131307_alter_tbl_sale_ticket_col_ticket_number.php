<?php

use yii\db\Migration;

/**
 * Class m200722_131307_alter_tbl_sale_ticket_col_ticket_number
 */
class m200722_131307_alter_tbl_sale_ticket_col_ticket_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%sale_ticket}}', 'st_ticket_number', $this->string(30));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%sale_ticket}}', 'st_ticket_number', $this->string(30));
    }
}
