<?php

use yii\db\Migration;

/**
 * Class m180828_124146_add_iata_index_on_airports_table
 */
class m180828_124146_add_iata_index_on_airports_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx-airports-iata', 'airports', 'iata');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-airports-flight-iata', 'airports');
    }
}
