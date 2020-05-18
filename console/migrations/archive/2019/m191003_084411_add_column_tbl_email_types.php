<?php

use yii\db\Migration;

/**
 * Class m191003_084411_add_column_tbl_email_types
 */
class m191003_084411_add_column_tbl_email_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sms_template_type}}', 'stp_dep_id', $this->integer());
        $this->addForeignKey('FK-sms_template_type_stp_dep_id', '{{%sms_template_type}}', ['stp_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');

        $this->execute('UPDATE sms_template_type SET stp_dep_id = 1');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-sms_template_type_stp_dep_id', '{{%sms_template_type}}');
        $this->dropColumn('{{%sms_template_type}}', 'stp_dep_id');
    }
}
