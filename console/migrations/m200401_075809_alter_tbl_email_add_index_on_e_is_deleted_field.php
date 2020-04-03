<?php

use yii\db\Migration;

/**
 * Class m200401_075809_alter_tbl_email_add_index_on_e_is_deleted_field
 */
class m200401_075809_alter_tbl_email_add_index_on_e_is_deleted_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createIndex('IND-e_is_deleted', '{{%email}}', 'e_is_deleted');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropIndex('IND-e_is_deleted', '{{%email}}');
	}
}
