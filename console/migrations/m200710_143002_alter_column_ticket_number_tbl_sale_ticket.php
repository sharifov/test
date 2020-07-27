<?php

use yii\db\Migration;

/**
 * Class m200710_143002_alter_column_ticket_number_tbl_sale_ticket
 */
class m200710_143002_alter_column_ticket_number_tbl_sale_ticket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%sale_ticket}}', 'st_ticket_number', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->alterColumn('{{%sale_ticket}}', 'st_ticket_number', $this->string(30));
    }
}
