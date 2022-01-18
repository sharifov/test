<?php

use yii\db\Migration;

/**
 * Class m210301_141412_add_column_fq_ticket_json
 */
class m210301_141412_add_column_fq_ticket_json extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote}}', 'fq_ticket_json', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_quote}}', 'fq_ticket_json');
    }
}
