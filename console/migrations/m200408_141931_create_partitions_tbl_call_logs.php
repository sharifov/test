<?php

use yii\db\Migration;

/**
 * Class m200408_141931_create_partitions_tbl_call_logs
 */
class m200408_141931_create_partitions_tbl_call_logs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->dropTable('{{%call_log_record}}');
        $this->dropTable('{{%call_log_case}}');
        $this->dropTable('{{%call_log_lead}}');
        $this->dropTable('{{%call_log_queue}}');
        $this->dropTable('{{%call_log}}');

        $this->createTable('{{%call_log}}', [
            'cl_id' => $this->integer()->notNull(),
            'cl_parent_id' => $this->integer()->null(),
            'cl_call_sid' => $this->string(34)->null(),
            'cl_type_id' => $this->tinyInteger()->null(),
            'cl_category_id' => $this->tinyInteger()->null(),
            'cl_is_transfer' => $this->boolean()->null(),
            'cl_duration' => $this->smallInteger()->null(),
            'cl_phone_from' => $this->string(18)->null(),
            'cl_phone_to' => $this->string(18)->null(),
            'cl_phone_list_id' => $this->integer()->null(),
            'cl_user_id' => $this->integer()->null(),
            'cl_department_id' => $this->integer()->null(),
            'cl_project_id' => $this->integer()->null(),
            'cl_call_created_dt' => $this->dateTime()->null(),
            'cl_call_finished_dt' => $this->dateTime()->null(),
            'cl_status_id' => $this->tinyInteger()->null(),
            'cl_client_id' => $this->integer()->null(),
            'cl_price' => $this->decimal(9, 5)->null(),
            'cl_year' => $this->smallInteger()->notNull(),
            'cl_month' => $this->tinyInteger()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log-cl_id_cl_y_m', '{{%call_log}}', ['cl_id', 'cl_year', 'cl_month']);

        $this->createIndex('IND-call_log-cl_call_sid', '{{%call_log}}', ['cl_call_sid']);
        $this->createIndex('IND-call_log-cl_type_id', '{{%call_log}}', ['cl_type_id']);
        $this->createIndex('IND-call_log-cl_category_id', '{{%call_log}}', ['cl_category_id']);
        $this->createIndex('IND-call_log-cl_phone_from', '{{%call_log}}', ['cl_phone_from']);
        $this->createIndex('IND-call_log-cl_phone_to', '{{%call_log}}', ['cl_phone_to']);
        $this->createIndex('IND-call_log-cl_phone_list_id', '{{%call_log}}', ['cl_phone_list_id']);
        $this->createIndex('IND-call_log-cl_user_id', '{{%call_log}}', ['cl_user_id']);
        $this->createIndex('IND-call_log-cl_department_id', '{{%call_log}}', ['cl_department_id']);
        $this->createIndex('IND-call_log-cl_project_id', '{{%call_log}}', ['cl_project_id']);
        $this->createIndex('IND-call_log-cl_call_created_dt', '{{%call_log}}', ['cl_call_created_dt']);
        $this->createIndex('IND-call_log-cl_status_id', '{{%call_log}}', ['cl_status_id']);
        $this->createIndex('IND-call_log-cl_year', '{{%call_log}}', ['cl_year']);
        $this->createIndex('IND-call_log-cl_month', '{{%call_log}}', ['cl_month']);

        Yii::$app->db->createCommand('ALTER TABLE `call_log` PARTITION BY RANGE (`cl_year`)
SUBPARTITION BY LINEAR HASH (`cl_month`)
SUBPARTITIONS 12
(
PARTITION y19 VALUES LESS THAN (2019) ENGINE = InnoDB,
PARTITION y20 VALUES LESS THAN (2020)ENGINE = InnoDB ,
PARTITION y21 VALUES LESS THAN (2021)ENGINE = InnoDB,
PARTITION y22 VALUES LESS THAN (2022)ENGINE = InnoDB,
PARTITION y23 VALUES LESS THAN (2023)ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE
 );')->execute();

        $this->createTable('{{%call_log_queue}}', [
            'clq_cl_id' => $this->integer()->notNull(),
            'clq_queue_time' => $this->smallInteger()->null(),
            'clq_access_count' => $this->tinyInteger()->null(),
            'clq_is_transfer' => $this->boolean()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_queue-clq_cl_id', '{{%call_log_queue}}', ['clq_cl_id']);

        $this->createTable('{{%call_log_lead}}', [
            'cll_cl_id' => $this->integer()->notNull(),
            'cll_lead_id' => $this->integer()->notNull(),
            'cll_lead_flow_id' => $this->integer()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_lead-cll-cl_id-lead_id', '{{%call_log_lead}}', ['cll_cl_id', 'cll_lead_id']);
        $this->addForeignKey(
            'FK-call_log_lead-cll_lead_id',
            '{{%call_log_lead}}',
            'cll_lead_id',
            '{{%leads}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log_lead-cll_lead_flow_id',
            '{{%call_log_lead}}',
            'cll_lead_flow_id',
            '{{%lead_flow}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%call_log_case}}', [
            'clc_cl_id' => $this->integer()->notNull(),
            'clc_case_id' => $this->integer()->notNull(),
            'clc_case_status_log_id' => $this->integer()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_case-clc-cl_id-case_id', '{{%call_log_case}}', ['clc_cl_id', 'clc_case_id']);
        $this->addForeignKey(
            'FK-call_log_case-clc_case_id',
            '{{%call_log_case}}',
            'clc_case_id',
            '{{%cases}}',
            'cs_id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log_case-clc_case_status_log_id',
            '{{%call_log_case}}',
            'clc_case_status_log_id',
            '{{%case_status_log}}',
            'csl_id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%call_log_record}}', [
            'clr_cl_id' => $this->integer()->notNull(),
            'clr_record_sid' => $this->string(34)->null(),
            'clr_duration' => $this->smallInteger()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_record-clr_cl_id', '{{%call_log_record}}', ['clr_cl_id']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
