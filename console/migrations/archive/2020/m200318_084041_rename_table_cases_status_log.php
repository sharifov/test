<?php

use yii\db\Migration;

/**
 * Class m200318_084041_rename_table_cases_status_log
 */
class m200318_084041_rename_table_cases_status_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'FK-cases_status_log_csl_case_id',
            '{{%cases_status_log}}');

        $this->dropForeignKey(
            'FK-cases_status_log_csl_owner_id',
            '{{%cases_status_log}}');

        $this->renameTable('{{%cases_status_log}}', '{{%case_status_log}}');

        $this->addForeignKey(
            'FK-case_status_log_csl_case_id',
            '{{%case_status_log}}',
            'csl_case_id',
            '{{%cases}}',
            'cs_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-case_status_log_csl_owner_id',
            '{{%case_status_log}}',
            'csl_owner_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'FK-case_status_log_csl_case_id',
            '{{%case_status_log}}');

        $this->dropForeignKey(
            'FK-case_status_log_csl_owner_id',
            '{{%case_status_log}}');

        $this->renameTable('{{%case_status_log}}', '{{%cases_status_log}}');

        $this->addForeignKey(
            'FK-cases_status_log_csl_case_id',
            '{{%cases_status_log}}',
            'csl_case_id',
            '{{%cases}}',
            'cs_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-cases_status_log_csl_owner_id',
            '{{%cases_status_log}}',
            'csl_owner_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }
}
