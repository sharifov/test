<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%quote_search_cid}}`.
 */
class m220908_124200_create_quote_search_cid_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%quote_search_cid}}', [
            'qsc_id' => $this->primaryKey(),
            'qsc_q_id' => $this->integer(),
            'qsc_cid' => $this->string(),
        ]);

        $this->addForeignKey(
            'FK-quote_search_cid-qsc_q_id',
            '{{%quote_search_cid}}',
            'qsc_q_id',
            'quotes',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%quote_search_cid}}');
    }
}
