<?php

namespace modules\rentCar\migrations;

use yii\db\Migration;

/**
 * Class m210212_124516_add_pricing_columns_to_rc_quote
 */
class m210212_124516_add_pricing_columns_to_rc_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%rent_car_quote}}', 'rcq_system_mark_up', $this->decimal(10, 2)->defaultValue(0.00));
        $this->addColumn('{{%rent_car_quote}}', 'rcq_agent_mark_up', $this->decimal(10, 2)->defaultValue(0.00));
        $this->addColumn('{{%rent_car_quote}}', 'rcq_service_fee_percent', $this->decimal(10, 2)->defaultValue(0.00));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_system_mark_up');
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_agent_mark_up');
        $this->dropColumn('{{%rent_car_quote}}', 'rcq_service_fee_percent');
    }
}
