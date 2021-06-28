<?php

use yii\db\Migration;

/**
 * Class m210616_052646_add_column_lr_lead_id
 */
class m210616_052646_add_column_lr_lead_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_request}}', 'lr_lead_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lead_request}}', 'lr_lead_id');
    }
}
