<?php

use yii\db\Migration;

/**
 * Class m190523_114325_add_indexes3
 */
class m190523_114325_add_indexes3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-client_phone', '{{%client_phone}}', ['phone']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-client_phone', '{{%client_phone}}');
    }

}
