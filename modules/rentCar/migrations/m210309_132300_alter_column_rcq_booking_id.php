<?php

namespace modules\rentCar\migrations;

use yii\db\Migration;

/**
 * Class m210309_132300_alter_column_rcq_booking_id
 */
class m210309_132300_alter_column_rcq_booking_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%rent_car_quote}}', 'rcq_booking_id', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210309_132300_alter_column_rcq_booking_id cannot be reverted.\n";
    }
}
