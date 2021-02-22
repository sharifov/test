<?php

namespace modules\cruise\migrations;

use yii\db\Migration;

/**
 * Class m210212_122056_add_columns_cruise_quote
 */
class m210212_122056_add_columns_cruise_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cruise_quote}}', 'crq_amount', $this->decimal(10, 2));
        $this->addColumn('{{%cruise_quote}}', 'crq_amount_per_person', $this->decimal(10, 2));
        $this->addColumn('{{%cruise_quote}}', 'crq_currency', $this->string(3));
        $this->addColumn('{{%cruise_quote}}', 'crq_adults', $this->tinyInteger());
        $this->addColumn('{{%cruise_quote}}', 'crq_children', $this->tinyInteger());
        $this->addColumn('{{%cruise_quote}}', 'crq_system_mark_up', $this->decimal(10, 2));
        $this->addColumn('{{%cruise_quote}}', 'crq_agent_mark_up', $this->decimal(10, 2));
        $this->addColumn('{{%cruise_quote}}', 'crq_service_fee_percent', $this->decimal(5, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%cruise_quote}}', 'crq_amount');
        $this->dropColumn('{{%cruise_quote}}', 'crq_amount_per_person');
        $this->dropColumn('{{%cruise_quote}}', 'crq_currency');
        $this->dropColumn('{{%cruise_quote}}', 'crq_adults');
        $this->dropColumn('{{%cruise_quote}}', 'crq_children');
        $this->dropColumn('{{%cruise_quote}}', 'crq_system_mark_up');
        $this->dropColumn('{{%cruise_quote}}', 'crq_agent_mark_up');
        $this->dropColumn('{{%cruise_quote}}', 'crq_service_fee_percent');
    }
}
