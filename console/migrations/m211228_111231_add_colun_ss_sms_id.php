<?php

use yii\db\Migration;

/**
 * Class m211228_111231_add_colun_ss_sms_id
 */
class m211228_111231_add_colun_ss_sms_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sms_subscribe}}', 'ss_sms_id', $this->integer());

        $this->addForeignKey(
            'FK-sms_subscribe-ss_sms_id',
            '{{%sms_subscribe}}',
            'ss_sms_id',
            '{{%sms}}',
            's_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-sms_subscribe-ss_sms_id', '{{%sms_subscribe}}');
        $this->dropColumn('{{%sms_subscribe}}', 'ss_sms_id');
    }
}
