<?php

use yii\db\Migration;

/**
 * Class m200324_110834_create_tables_call_log
 */
class m200324_110834_create_tables_call_log extends Migration
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

        $this->createTable('{{%call_log}}', [
            'cl_id' => $this->primaryKey(),
            'cl_parent_id' => $this->integer()->null(),
            'cl_call_sid' => $this->string(34)->null(),
            'cl_type_id' => $this->tinyInteger()->null(),
            'cl_category_id' => $this->tinyInteger()->null(),
            'cl_is_transfer' => $this->boolean()->null(),
            'cl_duration' => $this->smallInteger()->null(),
            'cl_phone_from' => $this->string(15)->null(),
            'cl_phone_to' => $this->string(15)->null(),
            'cl_phone_list_id' => $this->integer()->null(),
            'cl_user_id' => $this->integer()->null(),
            'cl_department_id' => $this->integer()->null(),
            'cl_project_id' => $this->integer()->null(),
            'cl_call_created_dt' => $this->dateTime()->null(),
            'cl_call_finished_dt' => $this->dateTime()->null(),
            'cl_status_id' => $this->tinyInteger()->null(),
            'cl_client_id' => $this->integer()->null(),
            'cl_price' => $this->decimal(9, 5)->null(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-call_log-cl_parent_id',
            '{{%call_log}}',
            'cl_parent_id',
            '{{%call_log}}',
            'cl_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log-cl_phone_list_id',
            '{{%call_log}}',
            'cl_phone_list_id',
            '{{%phone_list}}',
            'pl_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log-cl_user_id',
            '{{%call_log}}',
            'cl_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log-cl_department_id',
            '{{%call_log}}',
            'cl_department_id',
            '{{%department}}',
            'dep_id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log-cl_project_id',
            '{{%call_log}}',
            'cl_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log-cl_client_id',
            '{{%call_log}}',
            'cl_client_id',
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

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

        $this->createTable('{{%call_log_queue}}', [
            'clq_cl_id' => $this->integer()->notNull(),
            'clq_queue_time' => $this->smallInteger()->null(),
            'clq_access_count' => $this->tinyInteger()->null(),
            'clq_is_transfer' => $this->boolean()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_queue-clq_cl_id', '{{%call_log_queue}}', ['clq_cl_id']);
        $this->addForeignKey(
            'FK-call_log_queue-clq_cl_id',
            '{{%call_log_queue}}',
            'clq_cl_id',
            '{{%call_log}}',
            'cl_id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%call_log_lead}}', [
            'cll_cl_id' => $this->integer()->notNull(),
            'cll_lead_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_lead-cll-cl_id-lead_id', '{{%call_log_lead}}', ['cll_cl_id', 'cll_lead_id']);
        $this->addForeignKey(
            'FK-call_log_lead-cll_cl_id',
            '{{%call_log_lead}}',
            'cll_cl_id',
            '{{%call_log}}',
            'cl_id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log_lead-cll_lead_id',
            '{{%call_log_lead}}',
            'cll_lead_id',
            '{{%leads}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%call_log_case}}', [
            'clc_cl_id' => $this->integer()->notNull(),
            'clc_case_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_case-clc-cl_id-case_id', '{{%call_log_case}}', ['clc_cl_id', 'clc_case_id']);
        $this->addForeignKey(
            'FK-call_log_case-clc_cl_id',
            '{{%call_log_case}}',
            'clc_cl_id',
            '{{%call_log}}',
            'cl_id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-call_log_case-clc_case_id',
            '{{%call_log_case}}',
            'clc_case_id',
            '{{%cases}}',
            'cs_id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%call_log_record}}', [
            'clr_cl_id' => $this->integer()->notNull(),
            'clr_record_sid' => $this->string(34)->null(),
            'clr_duration' => $this->smallInteger()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-call_log_record-clr_cl_id', '{{%call_log_record}}', ['clr_cl_id']);
        $this->addForeignKey(
            'FK-call_log_record-clr_cl_id',
            '{{%call_log_record}}',
            'clr_cl_id',
            '{{%call_log}}',
            'cl_id',
            'CASCADE',
            'CASCADE'
        );

        $this->insert('{{%setting}}', [
            's_key' => 'call_log_enable',
            's_name' => 'Call log enable',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_log_enable'
        ]]);

        $this->dropTable('{{%call_log_record}}');
        $this->dropTable('{{%call_log_case}}');
        $this->dropTable('{{%call_log_lead}}');
        $this->dropTable('{{%call_log_queue}}');
        $this->dropTable('{{%call_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
