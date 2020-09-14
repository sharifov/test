<?php

use yii\db\Migration;

/**
 * Class m200831_075222_create_tbls_for_phone_lines
 */
class m200831_075222_create_tbls_for_phone_lines extends Migration
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

		$this->createTable('{{%user_personal_phone_number}}', [
			'upn_id' => $this->primaryKey(),
			'upn_user_id' => $this->integer()->notNull(),
			'upn_phone_number' => $this->string(15)->notNull(),
			'upn_title' => $this->string(100),
			'upn_approved' => $this->tinyInteger(1)->defaultValue(0),
			'upn_enabled' => $this->tinyInteger(1)->defaultValue(1),
			'upn_created_user_id' => $this->integer(),
			'upn_updated_user_id' => $this->integer(),
			'upn_created_dt' => $this->dateTime(),
			'upn_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addForeignKey('FK-upn_user_id', '{{%user_personal_phone_number}}', ['upn_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-upn_created_user_id', '{{%user_personal_phone_number}}', ['upn_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-upn_updated_user_id', '{{%user_personal_phone_number}}', ['upn_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

		$this->createTable('{{%phone_line}}', [
			'line_id' => $this->primaryKey(),
			'line_name' => $this->string(100),
			'line_project_id' => $this->integer()->notNull(),
			'line_dep_id' => $this->integer(),
			'line_language_id' => $this->string(5)->unsigned(),
			'line_settings_json' => $this->json(),
			'line_personal_user_id' => $this->integer(),
			'line_uvm_id' => $this->integer(),
			'line_allow_in' => $this->tinyInteger(1)->defaultValue(1),
			'line_allow_out' => $this->tinyInteger(1)->defaultValue(1),
			'line_enabled' => $this->tinyInteger(1)->defaultValue(1),
			'line_created_user_id' => $this->integer(),
			'line_updated_user_id' => $this->integer(),
			'line_created_dt' => $this->dateTime(),
			'line_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addForeignKey('FK-line_project_id', '{{%phone_line}}', ['line_project_id'], '{{%projects}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-line_dep_id', '{{%phone_line}}', ['line_dep_id'], '{{%department}}', 'dep_id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-line_personal_user_id', '{{%phone_line}}', ['line_personal_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-line_uvm_id', '{{%phone_line}}', ['line_uvm_id'], '{{%user_voice_mail}}', ['uvm_id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-line_created_user_id', '{{%phone_line}}', ['line_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-line_updated_user_id', '{{%phone_line}}', ['line_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->createIndex('IND-line_name', '{{%phone_line}}', ['line_name']);

		$this->createTable('{{%phone_line_user_assign}}', [
			'plus_line_id' => $this->integer()->notNull(),
			'plus_user_id' => $this->integer()->notNull(),
			'plus_allow_in' => $this->tinyInteger(1)->defaultValue(1),
			'plus_allow_out' => $this->tinyInteger(1)->defaultValue(1),
			'plus_uvm_id' => $this->integer(),
			'plus_enabled' => $this->tinyInteger(1)->defaultValue(1),
			'plus_settings_json' => $this->json(),
			'plus_created_user_id' => $this->integer(),
			'plus_updated_user_id' => $this->integer(),
			'plus_created_dt' => $this->dateTime(),
			'plus_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-plus_line_id_plus_user_id', '{{%phone_line_user_assign}}', ['plus_line_id', 'plus_user_id']);
		$this->addForeignKey('FK-plus_line_id', '{{%phone_line_user_assign}}', 'plus_line_id', '{{%phone_line}}', 'line_id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-plus_user_id', '{{%phone_line_user_assign}}', 'plus_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-plus_uvm_id', '{{%phone_line_user_assign}}', 'plus_uvm_id', '{{%user_voice_mail}}', 'uvm_id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-plus_created_user_id', '{{%phone_line_user_assign}}', 'plus_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-plus_updated_user_id', '{{%phone_line_user_assign}}', 'plus_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		$this->createTable('{{%phone_line_phone_number}}', [
			'plpn_line_id' => $this->integer(),
			'plpn_pl_id' => $this->integer()->unique(),
			'plpn_default' => $this->tinyInteger(1)->defaultValue(0),
			'plpn_enabled' => $this->tinyInteger(1)->defaultValue(1),
			'plpn_settings_json' => $this->json(),
			'plpn_created_user_id' => $this->integer(),
			'plpn_updated_user_id' => $this->integer(),
			'plpn_created_dt' => $this->dateTime(),
			'plpn_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-plpn_line_id-plpn_pl_id', '{{%phone_line_phone_number}}', ['plpn_line_id', 'plpn_pl_id']);
		$this->addForeignKey('FK-plpn_line_id', '{{%phone_line_phone_number}}', 'plpn_line_id', '{{%phone_line}}', 'line_id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-plpn_pl_id', '{{%phone_line_phone_number}}', 'plpn_pl_id', '{{%phone_list}}', 'pl_id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-plpn_created_user_id', '{{%phone_line_phone_number}}', 'plpn_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-plpn_updated_user_id', '{{%phone_line_phone_number}}', 'plpn_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		$this->createTable('{{%phone_line_user_group}}', [
			'plug_line_id' => $this->integer(),
			'plug_ug_id' => $this->integer(),
			'plug_created_dt' => $this->dateTime(),
			'plug_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-plug_line_id-plug_ug_id', '{{%phone_line_user_group}}',  ['plug_line_id', 'plug_ug_id']);
		$this->addForeignKey('FK-plug_line_id', '{{%phone_line_user_group}}', 'plug_line_id', '{{%phone_line}}', 'line_id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-plug_ug_id', '{{%phone_line_user_group}}', 'plug_ug_id', '{{%user_group}}', 'ug_id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%phone_line_user_group}}');
		$this->dropTable('{{%phone_line_phone_number}}');
		$this->dropTable('{{%phone_line_user_assign}}');
		$this->dropTable('{{%phone_line}}');
		$this->dropTable('{{%user_personal_phone_number}}');
    }
}
