<?php

use yii\db\Migration;

/**
 * Class m200526_111506_add_column_tbl_credit_card_is_sync_bo
 */
class m200526_111506_add_column_tbl_credit_card_is_sync_bo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%credit_card}}', 'cc_is_sync_bo', $this->tinyInteger(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%credit_card}}', 'cc_is_sync_bo');
    }
}
