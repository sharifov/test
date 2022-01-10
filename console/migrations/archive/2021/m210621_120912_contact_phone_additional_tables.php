<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210621_120912_contact_phone_additional_tables
 */
class m210621_120912_contact_phone_additional_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%contact_phone_list}}', [
            'cpl_id' => $this->primaryKey(),
            'cpl_phone_number' => $this->string(20)->notNull()->unique(),
            'cpl_uid' => $this->string(36)->notNull(),
            'cpl_title' => $this->string(50),
            'cpl_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-contact_phone_list-cpl_uid', '{{%contact_phone_list}}', ['cpl_uid'], true);

        $this->createTable('{{%contact_phone_data}}', [
            'cpd_cpl_id' => $this->integer()->notNull(),
            'cpd_key' => $this->string(30)->notNull(),
            'cpd_value' => $this->string(100)->notNull(),
            'cpd_created_dt' => $this->dateTime(),
            'cpd_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-contact_phone_data', '{{%contact_phone_data}}', ['cpd_cpl_id', 'cpd_key']);

        $this->addForeignKey(
            'FK-contact_phone_data-cpd_cpl_id',
            '{{%contact_phone_data}}',
            ['cpd_cpl_id'],
            '{{%contact_phone_list}}',
            ['cpl_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%contact_phone_service_info}}', [
            'cpsi_cpl_id' => $this->integer()->notNull(),
            'cpsi_service_id' => $this->tinyInteger(),
            'cpsi_data_json' => $this->json(),
            'cpsi_created_dt' => $this->dateTime(),
            'cpsi_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-contact_phone_service_info', '{{%contact_phone_service_info}}', ['cpsi_cpl_id', 'cpsi_service_id']);

        $this->addForeignKey(
            'FK-contact_phone_service_info-cpsi_cpl_id',
            '{{%contact_phone_service_info}}',
            ['cpsi_cpl_id'],
            '{{%contact_phone_list}}',
            ['cpl_id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-contact_phone_data-cpd_cpl_id', 'contact_phone_data');
        $this->dropForeignKey('FK-contact_phone_service_info-cpsi_cpl_id', 'contact_phone_service_info');

        $this->dropIndex('IND-contact_phone_list-cpl_uid', '{{%contact_phone_list}}');

        $this->dropTable('{{%contact_phone_service_info}}');
        $this->dropTable('{{%contact_phone_data}}');
        $this->dropTable('{{%contact_phone_list}}');
    }
}
