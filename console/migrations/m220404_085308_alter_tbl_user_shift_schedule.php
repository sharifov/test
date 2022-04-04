<?php

use yii\db\Migration;

/**
 * Class m220404_085308_alter_tbl_user_shift_schedule
 */
class m220404_085308_alter_tbl_user_shift_schedule extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user_shift_schedule}}', 'uss_sst_id', $this->integer());

        $this->addForeignKey(
            'FK-user_shift_schedule-uss_sst_id',
            '{{%user_shift_schedule}}',
            'uss_sst_id',
            '{{%shift_schedule_type}}',
            'sst_id',
            'SET NULL',
            'CASCADE'
        );


        $this->addColumn('{{%shift_schedule_rule}}', 'ssr_sst_id', $this->integer());

        $this->addForeignKey(
            'FK-shift_schedule_rule-ssr_sst_id',
            '{{%shift_schedule_rule}}',
            'ssr_sst_id',
            '{{%shift_schedule_type}}',
            'sst_id',
            'SET NULL',
            'CASCADE'
        );
    }


    public function safeDown()
    {
        $this->dropForeignKey(
            'FK-shift_schedule_rule-ssr_sst_id',
            '{{%shift_schedule_rule}}'
        );

        $this->dropForeignKey(
            'FK-user_shift_schedule-uss_sst_id',
            '{{%user_shift_schedule}}'
        );

        $this->dropColumn('{{%shift_schedule_rule}}', 'ssr_sst_id');
        $this->dropColumn('{{%user_shift_schedule}}', 'uss_sst_id');
    }
}
