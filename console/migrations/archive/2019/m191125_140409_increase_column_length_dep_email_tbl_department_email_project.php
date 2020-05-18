<?php

use yii\db\Migration;

/**
 * Class m191125_140409_increase_column_length_dep_email_tbl_department_email_project
 */
class m191125_140409_increase_column_length_dep_email_tbl_department_email_project extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%department_email_project}}', 'dep_email', $this->string(50)->notNull());

		Yii::$app->db->getSchema()->refreshTableSchema('{{%department_email_project}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('{{%department_email_project}}', 'dep_email', $this->string(18)->notNull());

		Yii::$app->db->getSchema()->refreshTableSchema('{{%department_email_project}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
