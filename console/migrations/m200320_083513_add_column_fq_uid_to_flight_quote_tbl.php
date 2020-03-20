<?php

use yii\db\Migration;

/**
 * Class m200320_083513_add_column_fq_uid_to_flight_quote_tbl
 */
class m200320_083513_add_column_fq_uid_to_flight_quote_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200320_083513_add_column_fq_uid_to_flight_quote_tbl cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200320_083513_add_column_fq_uid_to_flight_quote_tbl cannot be reverted.\n";

        return false;
    }
    */
}
