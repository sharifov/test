<?php

use yii\db\Migration;

/**
 * Class m190401_113734_add_column_tbl_user_profile
 */
class m190401_113734_add_column_tbl_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%user_profile}}', 'up_auto_redial', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%user_profile}}', 'up_kpi_enable', $this->boolean()->defaultValue(true));
        $this->addColumn('{{%user_profile}}', 'up_skill', $this->tinyInteger()->defaultValue(0));

        $this->addColumn('{{%leads}}', 'l_call_status_id', $this->tinyInteger()->defaultValue(0));
        //$this->addColumn('{{%leads}}', 'l_call_rating', $this->smallInteger()->defaultValue(0));
        $this->addColumn('{{%leads}}', 'l_pending_delay_dt', $this->dateTime());

        $this->createIndex('IND-leads_l_call_status_id', '{{%leads}}', ['l_call_status_id']);
        //$this->createIndex('IND-leads_l_call_rating', '{{%leads}}', ['l_call_rating']);
        $this->createIndex('IND-leads_l_pending_delay_dt', '{{%leads}}', ['l_pending_delay_dt']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_profile}}', 'up_auto_redial');
        $this->dropColumn('{{%user_profile}}', 'up_kpi_enable');
        $this->dropColumn('{{%user_profile}}', 'up_skill');

        $this->dropColumn('{{%leads}}', 'l_call_status_id');
        $this->dropColumn('{{%leads}}', 'l_call_rating');
        //$this->dropColumn('{{%leads}}', 'l_pending_delay_dt');
    }

}
