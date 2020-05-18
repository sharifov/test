<?php

use yii\db\Migration;

/**
 * Class m190626_133027_update_tinyint_to_smallint
 */
class m190626_133027_update_tinyint_to_smallint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%call}}', 'c_call_type_id', $this->smallInteger(1));
        $this->alterColumn('{{%call}}', 'c_source_type_id', $this->smallInteger(1));

        $this->alterColumn('{{%email}}', 'e_priority', $this->smallInteger(1)->defaultValue(2));
        $this->alterColumn('{{%email}}', 'e_status_id', $this->smallInteger(1)->defaultValue(1));

        $this->alterColumn('{{%lead_call_expert}}', 'lce_status_id', $this->smallInteger(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%call}}', 'c_call_type_id', $this->tinyInteger(1));
        $this->alterColumn('{{%call}}', 'c_source_type_id', $this->tinyInteger(3));

        $this->alterColumn('{{%email}}', 'e_priority', $this->tinyInteger(1)->defaultValue(2));
        $this->alterColumn('{{%email}}', 'e_status_id', $this->tinyInteger(1)->defaultValue(1));

        $this->alterColumn('{{%lead_call_expert}}', 'lce_status_id', $this->tinyInteger(1));
    }
}

