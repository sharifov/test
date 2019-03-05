<?php

use yii\db\Migration;

/**
 * Class m190227_092519_lead_agents_processing_fee_add
 */
class m190227_092519_lead_agents_processing_fee_add extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'agents_processing_fee', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'agents_processing_fee');
    }

}
