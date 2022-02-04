<?php

use yii\db\Migration;

/**
 * Class m220202_140348_add_column_lppl_description
 */
class m220202_140348_add_column_lppl_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('lead_poor_processing_log', 'lppl_description', $this->string(500));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('lead_poor_processing_log', 'lppl_description');
    }
}
