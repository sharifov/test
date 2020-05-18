<?php

use common\models\LeadFlow;
use yii\db\Migration;

/**
 * Class m191113_155355_alter_lead_flow_out_calls
 */
class m191113_155355_alter_lead_flow_out_calls extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%lead_flow}}', 'lf_out_calls', $this->smallInteger()->defaultValue(0));
        LeadFlow::updateAll(['lf_out_calls' => 0], ['lf_out_calls' => null]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%lead_flow}}', 'lf_out_calls', $this->smallInteger());
    }
}
