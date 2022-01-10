<?php

use yii\db\Migration;

/**
 * Class m210226_115045_alter_tbl_payment_add_new_column
 */
class m210226_115045_alter_tbl_payment_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment}}', 'pay_code', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment}}', 'pay_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_115045_alter_tbl_payment_add_new_column cannot be reverted.\n";

        return false;
    }
    */
}
