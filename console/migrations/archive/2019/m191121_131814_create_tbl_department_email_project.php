<?php

use yii\db\Migration;

/**
 * Class m191121_131814_create_tbl_department_email_project
 */
class m191121_131814_create_tbl_department_email_project extends Migration
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

		$this->createTable('{{%department_email_project}}', [
			'dep_id' => $this->primaryKey(),
			'dep_email' => $this->string(18)->notNull()->unique(),
			'dep_project_id' => $this->integer()->notNull(),
			'dep_dep_id' => $this->integer(),
			'dep_source_id' => $this->integer(),
			'dep_enable' => $this->boolean()->defaultValue(true),
			'dep_description' => $this->string(),
			'dep_updated_user_id' => $this->integer(),
			'dep_updated_dt' => $this->dateTime(),
		], $tableOptions);

		$this->createIndex('IND-department_email_project_dep_email', '{{%department_email_project}}', ['dep_email']);

		$this->addForeignKey('FK-department_email_project_dep_dep_id', '{{%department_email_project}}', ['dep_dep_id'], '{{%department}}', ['dep_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-department_email_project_dep_project_id', '{{%department_email_project}}', ['dep_project_id'], '{{%projects}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-department_email_project_dep_source_id', '{{%department_email_project}}', ['dep_source_id'], '{{%sources}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-department_email_project_dep_updated_user_id', '{{%department_email_project}}', ['dep_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropIndex('IND-department_email_project_dep_email', '{{%department_email_project}}');

    	$this->dropForeignKey('FK-department_email_project_dep_dep_id', '{{%department_email_project}}');
    	$this->dropForeignKey('FK-department_email_project_dep_project_id', '{{%department_email_project}}');
    	$this->dropForeignKey('FK-department_email_project_dep_source_id', '{{%department_email_project}}');
    	$this->dropForeignKey('FK-department_email_project_dep_updated_user_id', '{{%department_email_project}}');

		$this->dropTable('{{%department_email_project}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
