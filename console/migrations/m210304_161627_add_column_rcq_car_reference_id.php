<?php

use yii\db\Migration;

/**
 * Class m210304_161627_add_column_rcq_car_reference_id
 */
class m210304_161627_add_column_rcq_car_reference_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%rent_car_quote}}', 'rcq_car_reference_id', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_car_reference_id');
    }
}
