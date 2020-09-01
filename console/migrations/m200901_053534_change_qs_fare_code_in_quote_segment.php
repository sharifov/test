<?php

use yii\db\Migration;

/**
 * Class m200901_053534_change_qs_fare_code_in_quote_segment
 */
class m200901_053534_change_qs_fare_code_in_quote_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quote_segment}}', 'qs_fare_code', $this->string(50));
        $this->alterColumn('{{%flight_quote_segment}}', 'fqs_fare_code', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200901_053534_change_qs_fare_code_in_quote_segment cannot be reverted.\n";

        return false;
    }
}
