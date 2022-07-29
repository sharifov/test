<?php

use yii\db\Migration;

/**
 * Class m220729_112244_increase_lrp_condition_column_on_lead_rating_parameter_table
 */
class m220729_112244_increase_lrp_condition_column_on_lead_rating_parameter_table extends Migration
{
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE lead_rating_parameter MODIFY lrp_condition VARCHAR(3000)")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand('ALTER TABLE lead_rating_parameter MODIFY lrp_condition VARCHAR(255)')->execute();
    }
}
