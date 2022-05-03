<?php

use yii\db\Migration;

/**
 * Class m211124_103825_create_email_sms_template_type_project
 */
class m211124_103825_create_email_sms_template_type_project extends Migration
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

        $this->createTable('{{%email_template_type_project}}', [
            'ettp_etp_id' => $this->integer(),
            'ettp_project_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-email_template_type_project', '{{%email_template_type_project}}', ['ettp_etp_id', 'ettp_project_id']);

        $this->addForeignKey(
            'FK-email_template_type_project-ettp_etp_id',
            '{{%email_template_type_project}}',
            'ettp_etp_id',
            '{{%email_template_type}}',
            'etp_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-email_template_type_project-ettp_project_id',
            '{{%email_template_type_project}}',
            'ettp_project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%sms_template_type_project}}', [
            'sttp_stp_id' => $this->integer(),
            'sttp_project_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-sms_template_type_project', '{{%sms_template_type_project}}', ['sttp_stp_id', 'sttp_project_id']);

        $this->addForeignKey(
            'FK-sms_template_type_project-sttp_stp_id',
            '{{%sms_template_type_project}}',
            'sttp_stp_id',
            '{{%sms_template_type}}',
            'stp_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-sms_template_type_project-sttp_project_id',
            '{{%sms_template_type_project}}',
            'sttp_project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email_template_type_project}}');
        $this->dropTable('{{%sms_template_type_project}}');

        Yii::$app->cache->flush();
    }
}
