<?php

namespace modules\rentCar\migrations;

use yii\db\Migration;

/**
 * Class m210311_065858_add_columns_to_rent_car_quote
 */
class m210311_065858_add_columns_to_rent_car_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%rent_car_quote}}', 'rcq_pick_up_dt', $this->dateTime());
        $this->addColumn('{{%rent_car_quote}}', 'rcq_drop_off_dt', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_pick_up_dt');
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_drop_off_dt');
    }
}
