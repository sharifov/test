<?php

use yii\db\Migration;

/**
 * Class m200414_133106_create_index_created_tbl_quotes
 */
class m200414_133106_create_index_created_tbl_quotes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-quotes-created', '{{%quotes}}', ['created']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-quotes-created', '{{%quotes}}');
    }
}
