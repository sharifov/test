<?php

use yii\db\Migration;

/**
 * Class m200519_055213_add_column_tbl_credit_card
 */
class m200519_055213_add_column_tbl_credit_card extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%credit_card}}', 'cc_security_hash', $this->string(32));
        $this->addColumn('{{%credit_card}}', 'cc_bo_link', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%credit_card}}', 'cc_bo_link');
        $this->dropColumn('{{%credit_card}}', 'cc_security_hash');
    }
}
