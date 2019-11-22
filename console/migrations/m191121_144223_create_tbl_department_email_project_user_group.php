<?php

use yii\db\Migration;

/**
 * Class m191121_144223_create_tbl_department_email_project_user_group
 */
class m191121_144223_create_tbl_department_email_project_user_group extends Migration
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

		$this->createTable('{{%department_email_project_user_group}}',	[
			'dug_dep_id'        => $this->integer()->notNull(),
			'dug_ug_id'         => $this->integer()->notNull(),
			'dug_created_dt'    => $this->dateTime(),
		], $tableOptions);

		$this->addPrimaryKey('PK-department_email_project_user_group', '{{%department_email_project_user_group}}', ['dug_dep_id', 'dug_ug_id']);
		$this->addForeignKey('FK-department_email_project_user_group_dug_dep_id', '{{%department_email_project_user_group}}', ['dug_dep_id'], '{{%department_email_project}}', ['dep_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-department_email_project_user_group_dug_ug_id', '{{%department_email_project_user_group}}', ['dug_ug_id'], '{{%user_group}}', ['ug_id'], 'CASCADE', 'CASCADE');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-department_email_project_user_group_dug_dep_id', '{{%department_email_project_user_group}}');
    	$this->dropForeignKey('FK-department_email_project_user_group_dug_ug_id', '{{%department_email_project_user_group}}');

    	$this->dropTable('{{%department_email_project_user_group}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
