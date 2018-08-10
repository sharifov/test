<?php

use yii\db\Migration;

/**
 * Handles the creation of table `project_email_templates`.
 */
class m180809_121346_create_project_email_templates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('project_email_templates', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer(),
            'type' => $this->string()->notNull(),
            'subject' => $this->string()->notNull(),
            'template' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->notNull(),
            'layout_path' => $this->string(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'updated' => $this->dateTime()
        ], $tableOptions);
        $this->addForeignKey('fk-project_email_templates-projects', 'project_email_templates', 'project_id', 'projects', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-project_email_templates-projects', 'project_email_templates');
        $this->dropTable('project_email_templates');
    }
}
