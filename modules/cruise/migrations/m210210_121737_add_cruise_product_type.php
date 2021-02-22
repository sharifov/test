<?php

namespace modules\cruise\migrations;

use yii\db\Migration;

/**
 * Class m210210_121737_add_cruise_product_type
 */
class m210210_121737_add_cruise_product_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%product_type}}', [
            'pt_id' => 4,
            'pt_key' => 'cruise',
            'pt_name' => 'Cruise',
            'pt_enabled' => false,
            'pt_created_dt' => date('Y-m-d H:i:s'),
            'pt_updated_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%product_type}}', [
            'pt_id' => 4
        ]);
    }
}
