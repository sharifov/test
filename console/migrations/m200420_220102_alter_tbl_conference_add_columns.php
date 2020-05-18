<?php

use yii\db\Migration;

/**
 * Class m200420_220102_alter_tbl_conference_add_columns
 */
class m200420_220102_alter_tbl_conference_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%conference}}', 'cf_friendly_name', $this->string(50));
		$this->addColumn('{{%conference}}', 'cf_call_sid', $this->string(34));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%conference}}', 'cf_friendly_name');
    	$this->dropColumn('{{%conference}}', 'cf_call_sid');
    }
}
