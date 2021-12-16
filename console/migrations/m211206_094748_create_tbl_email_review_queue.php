<?php

use yii\db\Migration;

/**
 * Class m211206_094748_create_tbl_email_review_queue
 */
class m211206_094748_create_tbl_email_review_queue extends Migration
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
        $this->createTable('{{%email_review_queue}}', [
            'erq_id' => $this->primaryKey(),
            'erq_email_id' => $this->integer()->notNull(),
            'erq_project_id' => $this->integer(),
            'erq_department_id' => $this->integer(),
            'erq_owner_id' => $this->integer(),
            'erq_status_id' => $this->tinyInteger(2),
            'erq_user_reviewer_id' => $this->integer(),
            'erq_created_dt' => $this->dateTime(),
            'erq_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey('FK-email_review_queue-erq_email_id', '{{%email_review_queue}}', 'erq_email_id', '{{%email}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_review_queue-erq_project_id', '{{%email_review_queue}}', 'erq_project_id', '{{%projects}}', 'id', 'SET NULL', 'SET NULL');
        $this->addForeignKey('FK-email_review_queue-erq_department_id', '{{%email_review_queue}}', 'erq_department_id', '{{%department}}', 'dep_id', 'SET NULL', 'SET NULL');
        $this->addForeignKey('FK-email_review_queue-erq_owner_id', '{{%email_review_queue}}', 'erq_owner_id', '{{%employees}}', 'id', 'SET NULL', 'SET NULL');
        $this->addForeignKey('FK-email_review_queue-erq_user_reviewer_id', '{{%email_review_queue}}', 'erq_user_reviewer_id', '{{%employees}}', 'id', 'SET NULL', 'SET NULL');

        $this->createIndex('IND-email_review_queue-erq_email_id', '{{%email_review_queue}}', 'erq_email_id');
        $this->createIndex('IND-email_review_queue-erq_project_id', '{{%email_review_queue}}', 'erq_project_id');
        $this->createIndex('IND-email_review_queue-erq_department_id', '{{%email_review_queue}}', 'erq_department_id');
        $this->createIndex('IND-email_review_queue-erq_owner_id', '{{%email_review_queue}}', 'erq_owner_id');
        $this->createIndex('IND-email_review_queue-erq_user_reviewer_id', '{{%email_review_queue}}', 'erq_user_reviewer_id');
        $this->createIndex('IND-email_review_queue-erq_status_id', '{{%email_review_queue}}', 'erq_status_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%email_review_queue}}');
    }
}
