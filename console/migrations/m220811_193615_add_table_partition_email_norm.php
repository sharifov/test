<?php

use yii\db\Migration;

/**
 * Class m220811_193615_add_table_partition_email_norm
 */
class m220811_193615_add_table_partition_email_norm extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-ep_email_id', '{{%email_params}}');
        $this->dropForeignKey('FK-el_email_id', '{{%email_log}}');
        $this->dropForeignKey('FK-ec_email_id', '{{%email_contact}}');
        $this->dropForeignKey('FK-email_relation-er_email_id', '{{%email_relation}}');
        $this->dropForeignKey('FK-email_relation-er_reply_id', '{{%email_relation}}');
        $this->dropForeignKey('FK-email_lead-el_email_id', '{{%email_lead}}');
        $this->dropForeignKey('FK-email_case-ec_email_id', '{{%email_case}}');
        $this->dropForeignKey('FK-email_client-ecl_email_id', '{{%email_client}}');

        $this->dropForeignKey('FK-e_project_id', '{{%email_norm}}');
        $this->dropForeignKey('FK-e_departament_id', '{{%email_norm}}');
        $this->dropForeignKey('FK-e_created_user_id', '{{%email_norm}}');
        $this->dropForeignKey('FK-e_updated_user_id', '{{%email_norm}}');

        $this->alterColumn('{{%email_norm}}', 'e_id', $this->integer()->notNull());
        $this->dropPrimaryKey('PRIMARY', '{{%email_norm}}');
        $this->addPrimaryKey('PRIMARY', '{{%email_norm}}', ['e_id', 'e_created_dt']);
        $this->alterColumn('{{%email_norm}}', 'e_id', $this->integer()->notNull() . ' AUTO_INCREMENT');

         Yii::$app->db->createCommand("ALTER TABLE email_norm PARTITION BY RANGE(YEAR(e_created_dt))
            SUBPARTITION BY HASH(MONTH(e_created_dt))
            SUBPARTITIONS 12
            (
            PARTITION y19 VALUES LESS THAN (2020),
            PARTITION y20 VALUES LESS THAN (2021),
            PARTITION y21 VALUES LESS THAN (2022),
            PARTITION y22 VALUES LESS THAN (2023),
            PARTITION y23 VALUES LESS THAN (2024),
            PARTITION y VALUES LESS THAN MAXVALUE
             );
        ")->execute();

         $this->createIndex('IDX-e_project_id', '{{%email_norm}}', ['e_project_id']);
         $this->createIndex('IDX-e_departament_id', '{{%email_norm}}', ['e_departament_id']);
         $this->createIndex('IDX-e_created_user_id', '{{%email_norm}}', ['e_created_user_id']);
         $this->createIndex('IDX-e_updated_user_id', '{{%email_norm}}', ['e_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%email_norm}}', 'e_id', $this->integer()->notNull());
        $this->dropPrimaryKey('PRIMARY', '{{%email_norm}}');
        $this->addPrimaryKey('PRIMARY', '{{%email_norm}}', 'e_id');
        $this->alterColumn('{{%email_norm}}', 'e_id', $this->integer()->notNull() . ' AUTO_INCREMENT');

        $this->dropIndex('IDX-e_project_id', '{{%email_norm}}');
        $this->dropIndex('IDX-e_departament_id', '{{%email_norm}}');
        $this->dropIndex('IDX-e_created_user_id', '{{%email_norm}}');
        $this->dropIndex('IDX-e_updated_user_id', '{{%email_norm}}');

        $this->addForeignKey('FK-e_project_id', '{{%email_norm}}', ['e_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-e_departament_id', '{{%email_norm}}', ['e_departament_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-e_created_user_id', '{{%email_norm}}', ['e_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-e_updated_user_id', '{{%email_norm}}', ['e_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->addForeignKey('FK-ep_email_id', '{{%email_params}}', ['ep_email_id'], '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-el_email_id', '{{%email_log}}', ['el_email_id'], '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-ec_email_id', '{{%email_contact}}', ['ec_email_id'], '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_relation-er_email_id', '{{%email_relation}}', 'er_email_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_relation-er_reply_id', '{{%email_relation}}', 'er_reply_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-email_lead-el_email_id', '{{%email_lead}}', 'el_email_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_case-ec_email_id', '{{%email_case}}', 'ec_email_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_client-ecl_email_id', '{{%email_client}}', 'ecl_email_id', '{{%email_norm}}', 'e_id', 'CASCADE', 'CASCADE');
    }
}
