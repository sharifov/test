<?php

use yii\db\Migration;

/**
 * Class m200902_184529_create_tbl_voice_mail_record
 */
class m200902_184529_create_tbl_voice_mail_record extends Migration
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

        $this->createTable('{{%voice_mail_record}}', [
            'vmr_call_id' => $this->integer(),
            'vmr_record_sid' => $this->string(34),
            'vmr_client_id' => $this->integer(),
            'vmr_user_id' => $this->integer(),
            'vmr_created_dt' => $this->dateTime(),
            'vmr_duration' => $this->smallInteger(6),
            'vmr_new' => $this->boolean(),
            'vmr_deleted' => $this->boolean(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-voice_mail_record-vmr_call_id', '{{%voice_mail_record}}', ['vmr_call_id']);
        $this->addForeignKey('FK-voice_mail_record-vmr_client_id', '{{%voice_mail_record}}', ['vmr_client_id'], '{{%clients}}', ['id']);
        $this->addForeignKey('FK-voice_mail_record-vmr_user_id', '{{%voice_mail_record}}', ['vmr_user_id'], '{{%employees}}', ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-voice_mail_record-vmr_user_id', '{{%voice_mail_record}}');
        $this->dropForeignKey('FK-voice_mail_record-vmr_client_id', '{{%voice_mail_record}}');
        $this->dropPrimaryKey('PK-voice_mail_record-vmr_call_id', '{{%voice_mail_record}}');
        $this->dropTable('{{%voice_mail_record}}');
    }
}
