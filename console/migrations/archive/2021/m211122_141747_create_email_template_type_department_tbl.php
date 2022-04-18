<?php

use common\models\EmailTemplateType;
use common\models\EmailTemplateTypeDepartment;
use common\models\SmsTemplateType;
use common\models\SmsTemplateTypeDepartment;
use yii\db\Migration;

/**
 * Class m211122_141747_create_email_template_type_department_tbl
 */
class m211122_141747_create_email_template_type_department_tbl extends Migration
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

        $this->createTable('{{%email_template_type_department}}', [
            'ettd_etp_id' => $this->integer(),
            'ettd_department_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-email_template_type_department', '{{%email_template_type_department}}', ['ettd_etp_id', 'ettd_department_id']);

        $this->addForeignKey(
            'FK-email_template_type_department-ettd_etp_id',
            '{{%email_template_type_department}}',
            'ettd_etp_id',
            '{{%email_template_type}}',
            'etp_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-email_template_type_department-ettd_department_id',
            '{{%email_template_type_department}}',
            'ettd_department_id',
            '{{%department}}',
            'dep_id',
            'CASCADE',
            'CASCADE'
        );

        $eTemplateTypes = EmailTemplateType::find()->all();
        foreach ($eTemplateTypes as $ett) {
            $eTemplateTypesDep = new EmailTemplateTypeDepartment();
            $eTemplateTypesDep->ettd_etp_id = $ett->etp_id;
            $eTemplateTypesDep->ettd_department_id = $ett->etp_dep_id;
            $eTemplateTypesDep->save();
        }

        $this->createTable('{{%sms_template_type_department}}', [
            'sttd_stp_id' => $this->integer(),
            'sttd_department_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-sms_template_type_department', '{{%sms_template_type_department}}', ['sttd_stp_id', 'sttd_department_id']);

        $this->addForeignKey(
            'FK-sms_template_type_department-sttd_stp_id',
            '{{%sms_template_type_department}}',
            'sttd_stp_id',
            '{{%sms_template_type}}',
            'stp_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-sms_template_type_department-sttd_department_id',
            '{{%sms_template_type_department}}',
            'sttd_department_id',
            '{{%department}}',
            'dep_id',
            'CASCADE',
            'CASCADE'
        );

        $sTemplateTypes = SmsTemplateType::find()->all();
        foreach ($sTemplateTypes as $stt) {
            $sTemplateTypesDep = new SmsTemplateTypeDepartment();
            $sTemplateTypesDep->sttd_stp_id = $stt->stp_id;
            $sTemplateTypesDep->sttd_department_id = $stt->stp_dep_id;
            $sTemplateTypesDep->save();
        }

        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sms_template_type_department}}');
        $this->dropTable('{{%email_template_type_department}}');
        Yii::$app->cache->flush();
    }
}
