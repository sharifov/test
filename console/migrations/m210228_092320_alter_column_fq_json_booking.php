<?php

use yii\db\Migration;

/**
 * Class m210228_092320_alter_column_fq_json_booking
 */
class m210228_092320_alter_column_fq_json_booking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote}}', 'fq_json_booking', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_quote}}', 'fq_json_booking');
    }
}
