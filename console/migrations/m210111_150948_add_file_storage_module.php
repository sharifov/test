<?php

use yii\db\Migration;

/**
 * Class m210111_150948_add_file_storage_module
 */
class m210111_150948_add_file_storage_module extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file_storage}}', [
            'fs_id' => $this->primaryKey(),
            'fs_uid' => $this->string(32),
            'fs_mime_type' => $this->string(127),
            'fs_name' => $this->string(100),
            'fs_title' => $this->string(100),
            'fs_path' => $this->string(250),
            'fs_size' => $this->integer(),
            'fs_private' => $this->boolean(),
            'fs_expired_dt' => $this->dateTime(),
            'fs_created_dt' => $this->dateTime(),
        ], $tableOptions);
        $this->createIndex('IND-file_storage-fs_uid', '{{%file_storage}}', ['fs_uid'], true);
        $this->createIndex('IND-file_storage-fs_expired_dt', '{{%file_storage}}', ['fs_expired_dt']);
        $this->createIndex('IND-file_storage-fs_created_dt', '{{%file_storage}}', ['fs_created_dt']);

        $this->createTable('{{%file_user}}', [
            'fus_fs_id' => $this->integer(),
            'fus_user_id' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('PK-file_user-fs_id-user_id', '{{%file_user}}', ['fus_fs_id', 'fus_user_id']);
        $this->addForeignKey(
            'FK-file_user-fus_fs_id',
            '{{%file_user}}',
            ['fus_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%file_client}}', [
            'fcl_fs_id' => $this->integer(),
            'fcl_client_id' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('PK-file_client-fs_id-client_id', '{{%file_client}}', ['fcl_fs_id', 'fcl_client_id']);
        $this->addForeignKey(
            'FK-file_client-fcl_fs_id',
            '{{%file_client}}',
            ['fcl_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%file_share}}', [
            'fsh_id' => $this->primaryKey(),
            'fsh_fs_id' => $this->integer(),
            'fsh_code' => $this->string(32),
            'fsh_expired_dt' => $this->dateTime(),
            'fsh_created_dt' => $this->dateTime(),
            'fsh_created_user_id' => $this->integer(),
        ], $tableOptions);
        $this->addForeignKey(
            'FK-file_share-fsh_fs_id',
            '{{%file_share}}',
            ['fsh_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('IND-file_share-fsh_expired_dt', '{{%file_share}}', ['fsh_expired_dt']);
        $this->createIndex('IND-file_share-fsh_code', '{{%file_share}}', ['fsh_code'], true);

        $this->createTable('{{%file_log}}', [
            'fl_id' => $this->primaryKey(),
            'fl_fs_id' => $this->integer(),
            'fl_fsh_id' => $this->integer(),
            'fl_type_id' => $this->tinyInteger(1),
            'fl_created_dt' => $this->dateTime(),
            'fl_ip_address' => $this->string(40),
            'fl_user_agent' => $this->string(500),
        ], $tableOptions);
        $this->addForeignKey(
            'FK-file_log-fl_fs_id',
            '{{%file_log}}',
            ['fl_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-file_log-fl_fsh_id',
            '{{%file_log}}',
            ['fl_fsh_id'],
            '{{%file_share}}',
            ['fsh_id'],
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%file_lead}}', [
            'fld_fs_id' => $this->integer(),
            'fld_lead_id' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('PK-file_lead-fs_id-lead_id', '{{%file_lead}}', ['fld_fs_id', 'fld_lead_id']);
        $this->addForeignKey(
            'FK-file_lead-fld_fs_id',
            '{{%file_lead}}',
            ['fld_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%file_case}}', [
            'fc_fs_id' => $this->integer(),
            'fc_case_id' => $this->integer(),
        ], $tableOptions);
        $this->addPrimaryKey('PK-file_case-fs_id-case_id', '{{%file_case}}', ['fc_fs_id', 'fc_case_id']);
        $this->addForeignKey(
            'FK-file_case-fc_fs_id',
            '{{%file_case}}',
            ['fc_fs_id'],
            '{{%file_storage}}',
            ['fs_id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%file_case}}');
        $this->dropTable('{{%file_lead}}');
        $this->dropTable('{{%file_log}}');
        $this->dropTable('{{%file_share}}');
        $this->dropTable('{{%file_client}}');
        $this->dropTable('{{%file_user}}');
        $this->dropTable('{{%file_storage}}');
    }
}
