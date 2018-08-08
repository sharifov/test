<?php

use yii\db\Migration;

/**
 * Handles the creation of table `projects`.
 */
class m180731_093414_create_projects_table extends Migration
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

        $this->createTable('projects', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'link' => $this->string(),
            'api_key' => $this->string(),
            'contact_info' => $this->text(),
            'closed' => $this->boolean()->defaultValue(false),
            'last_update' => $this->dateTime()->defaultExpression('NOW()')
        ], $tableOptions);

        $this->createTable('sources', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer(),
            'name' => $this->string(),
            'cid' => $this->string(),
            'last_update' => $this->dateTime()->defaultExpression('NOW()')
        ], $tableOptions);
        $this->addForeignKey('fk-sources-projects', 'sources', 'project_id', 'projects', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sources-projects', 'sources');
        $this->dropTable('sources');

        $this->dropTable('projects');
    }
}
