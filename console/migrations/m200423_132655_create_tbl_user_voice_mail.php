<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200423_132655_create_tbl_user_voice_mail
 */
class m200423_132655_create_tbl_user_voice_mail extends Migration
{
	public $route = [
		'/user-voice-mail/*',
	];

	public $roles = [
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_ADMIN,
	];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%user_voice_mail}}',	[
			'uvm_id'                        => $this->primaryKey(),
			'uvm_user_id'                   => $this->integer(),
			'uvm_name'                      => $this->string(50),
			'uvm_say_text_message'          => $this->text(),
			'uvm_say_language'              => $this->string(10),
			'uvm_say_voice'                 => $this->string(30)->defaultValue('alice'),
			'uvm_voice_file_message'        => $this->string(),
			'uvm_record_enable'             => $this->boolean(),
			'uvm_max_recording_time'        => $this->integer()->defaultValue(60),
			'uvm_transcribe_enable'			=> $this->boolean(),
			'uvm_enabled'					=> $this->boolean(),
			'uvm_created_dt'				=> $this->dateTime(),
			'uvm_updated_dt'				=> $this->dateTime(),
			'uvm_created_user_id'			=> $this->integer(),
			'uvm_updated_user_id'			=> $this->integer()
		], $tableOptions);

		$this->addForeignKey('FK-voice_mail-user_id', '{{%user_voice_mail}}', ['uvm_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-voice_mail-uvm_created_user_id', '{{%user_voice_mail}}', ['uvm_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-voice_mail-uvm_updated_user_id', '{{%user_voice_mail}}', ['uvm_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-voice_mail-uvm_say_language', '{{%user_voice_mail}}', ['uvm_say_language'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');

		$this->insert('{{%setting}}', [
			's_key' => 'user_voice_mail',
			's_name' => 'Maximum Users Voice Mail',
			's_type' => 'int',
			's_value' => 10,
			's_updated_dt' => date('Y-m-d H:i:s'),
		]);

		(new RbacMigrationService())->up($this->route, $this->roles);

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropTable('{{%user_voice_mail}}');

		$this->delete('{{%setting}}', ['IN', 's_key', ['user_voice_mail']]);

		(new RbacMigrationService())->down($this->route, $this->roles);

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }

}
