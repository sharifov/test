<?php

use yii\db\Migration;

/**
 * Class m191014_081932_add_index_tbl_lead_flow
 */
class m191014_081932_add_index_tbl_lead_flow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createIndex('fk-lead_flow-owner', 'lead_flow', 'lf_owner_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropIndex('fk-lead_flow-owner', 'lead_flow');

        return true;
    }
}
