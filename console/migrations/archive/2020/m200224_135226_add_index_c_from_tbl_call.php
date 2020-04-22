<?php

use yii\db\Migration;

/**
 * Class m200224_135226_add_index_c_from_tbl_call
 */
class m200224_135226_add_index_c_from_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-call-c_from', '{{%call}}', ['c_from']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call-c_from', '{{%call}}');
    }
}
