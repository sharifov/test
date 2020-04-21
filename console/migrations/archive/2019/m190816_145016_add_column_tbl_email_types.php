<?php

use yii\db\Migration;

/**
 * Class m190816_145016_add_column_tbl_email_types
 */
class m190816_145016_add_column_tbl_email_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email_template_type}}', 'etp_dep_id', $this->integer());
        $this->addForeignKey('FK-email_template_type_etp_dep_id', '{{%email_template_type}}', ['etp_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');

        $this->execute('UPDATE email_template_type SET etp_dep_id = 1');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-email_template_type_etp_dep_id', '{{%email_template_type}}');
        $this->dropColumn('{{%email_template_type}}', 'etp_dep_id');
    }
}
