<?php

use yii\db\Migration;

/**
 * Class m181115_115422_alter_quote_operating_length_
 */
class m181115_115422_alter_quote_operating_length_ extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quote_segment}}','qs_operating_airline', $this->string(45));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%quote_segment}}','qs_operating_airline', $this->string(25));
    }
}
