<?php

use yii\db\Migration;

/**
 * Class m190925_141245_add_column_owner_id_to_lead_flow_table
 */
class m190925_141245_add_column_owner_id_to_lead_flow_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_flow}}', 'lf_owner_id', $this->integer()->after('employee_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lead_flow}}', 'lf_owner_id');
    }

}
