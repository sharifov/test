<?php
namespace modules\rbacImportExport\migrations;

use yii\db\Migration;

/**
 * Class m200304_101850_create_tbl_auth_import_export
 */
class m200304_101850_create_tbl_auth_import_export extends Migration
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
		$this->createTable('{{%auth_import_export}}', [
			'aie_id' => $this->primaryKey(),
			'aie_type' => $this->tinyInteger(1),
			'aie_cnt_roles' => $this->smallInteger(),
			'aie_cnt_permissions' => $this->smallInteger(),
			'aie_cnt_rules' => $this->smallInteger(),
			'aie_cnt_child' => $this->smallInteger(),
			'aie_file_name' => $this->string(),
			'aie_file_size' => $this->integer(),
			'aie_created_dt' => $this->dateTime(),
			'aie_user_id' => $this->integer(),
			'aie_data' => 'mediumtext'
		], $tableOptions);

		if ($this->db->getTableSchema('employees') !== null) {
			$this->addForeignKey('FK-auth_import_export-aie_user_id', '{{%auth_import_export}}','aie_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropTable('{{%auth_import_export}}');
	}
}
