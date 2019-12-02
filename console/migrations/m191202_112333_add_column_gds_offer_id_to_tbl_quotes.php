<?php

use yii\db\Migration;

/**
 * Class m191202_112333_add_column_gds_offer_id_to_tbl_quotes
 */
class m191202_112333_add_column_gds_offer_id_to_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%quotes}}', 'gds_offer_id', $this->string(255)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%quotes}}', 'gds_offer_id');
    }
}
