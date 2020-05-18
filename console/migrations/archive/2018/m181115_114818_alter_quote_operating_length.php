<?php

use yii\db\Migration;

/**
 * Class m181115_114818_alter_quote_operating_length
 */
class m181115_114818_alter_quote_operating_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quote_segment}}','qs_operating_airline', $this->string(25));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%quote_segment}}','qs_operating_airline', $this->string(2));
    }

}
