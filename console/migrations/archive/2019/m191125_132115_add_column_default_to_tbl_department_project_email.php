<?php

use yii\db\Migration;

/**
 * Class m191125_132115_add_column_default_to_tbl_department_project_email
 */
class m191125_132115_add_column_default_to_tbl_department_project_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%department_email_project}}', 'dep_default', $this->tinyInteger(1)->defaultValue('0')->after('dep_description'));

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
		$this->dropColumn('{{%department_email_project}}', 'dep_default');

		Yii::$app->db->getSchema()->refreshTableSchema('{{%department_email_project}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
