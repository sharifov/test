<?php

use yii\db\Migration;

/**
 * Class m190614_130531_create_index_quote_price
 */
class m190614_130531_create_index_quote_price extends Migration
{


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-quote_price_uid', '{{%quote_price}}', ['uid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-quote_price_uid', '{{%quote_price}}');
    }
}
