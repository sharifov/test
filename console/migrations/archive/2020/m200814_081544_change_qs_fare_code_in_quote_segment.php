<?php

use yii\db\Migration;

/**
 * Class m200814_081544_change_qs_fare_code_in_quote_segment
 */
class m200814_081544_change_qs_fare_code_in_quote_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quote_segment}}', 'qs_fare_code', $this->string(20));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%quote_segment}}', 'qs_fare_code', $this->string(15));
    }
}
