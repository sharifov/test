<?php

use yii\db\Migration;

/**
 * Class m220531_183919_create_email_relation_tables
 */
class m220606_183919_create_email_relation_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%email_lead}}', [
            'el_email_id' => $this->integer(),
            'el_lead_id' => $this->integer(),
        ]);
        $this->addPrimaryKey('PK-email_lead', '{{%email_lead}}', ['el_email_id', 'el_lead_id']);
        $this->addForeignKey('FK-email_lead-el_email_id', '{{%email_lead}}', 'el_email_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_lead-el_lead_id', '{{%email_lead}}', 'el_lead_id', '{{%leads}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%email_case}}', [
            'ec_email_id' => $this->integer(),
            'ec_case_id' => $this->integer(),
        ]);
        $this->addPrimaryKey('PK-email_case', '{{%email_case}}', ['ec_email_id', 'ec_case_id']);
        $this->addForeignKey('FK-email_case-ec_email_id', '{{%email_case}}', 'ec_email_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_case-ec_case_id', '{{%email_case}}', 'ec_case_id', '{{%cases}}', 'cs_id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%email_client}}', [
            'ecl_email_id' => $this->integer(),
            'ecl_client_id' => $this->integer(),
        ]);
        $this->addPrimaryKey('PK-email_client', '{{%email_client}}', ['ecl_email_id', 'ecl_client_id']);
        $this->addForeignKey('FK-email_client-ecl_email_id', '{{%email_client}}', 'ecl_email_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_client-ecl_client_id', '{{%email_client}}', 'ecl_client_id', '{{%clients}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email_lead}}');
        $this->dropTable('{{%email_case}}');
        $this->dropTable('{{%email_client}}');
    }
}
