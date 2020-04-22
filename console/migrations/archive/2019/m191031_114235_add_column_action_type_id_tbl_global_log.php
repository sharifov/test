<?php

use yii\db\Migration;

/**
 * Class m191031_114235_add_column_action_type_id_tbl_global_log
 */
class m191031_114235_add_column_action_type_id_tbl_global_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('global_log', 'gl_action_type', $this->smallInteger(1)->after('gl_formatted_attr'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('global_log', 'gl_action_type');
    }
}
