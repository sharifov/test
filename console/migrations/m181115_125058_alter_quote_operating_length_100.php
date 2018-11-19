<?php

use yii\db\Migration;

/**
 * Class m181115_125058_alter_quote_operating_length_100
 */
class m181115_125058_alter_quote_operating_length_100 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quote_segment}}','qs_operating_airline', $this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%quote_segment}}','qs_operating_airline', $this->string(45));
    }

}
