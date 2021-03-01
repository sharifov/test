<?php

use yii\db\Migration;

/**
 * Class m210301_123247_add_column_fq_flight_request_uid
 */
class m210301_123247_add_column_fq_flight_request_uid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote}}', 'fq_flight_request_uid', $this->string(100));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_quote}}', 'fq_flight_request_uid');
    }
}
