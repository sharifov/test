<?php

use yii\db\Migration;

/**
 * Class m191028_113127_create_tbl_global_logs
 */
class m191028_113127_create_tbl_global_logs extends Migration
{
	/**
	 * {@inheritdoc}
	 * @throws \yii\base\Exception
	 */
    public function safeUp()
    {
		$this->createTable('global_log', [
			'gl_id' => $this->primaryKey(11),
			'gl_app_id' => $this->string(20)->notNull(),
			'gl_app_user_id' => $this->integer(11),
			'gl_model' => $this->string(50)->notNull(),
			'gl_obj_id' => $this->integer(11)->notNull(),
			'gl_old_attr' => $this->json(),
			'gl_new_attr' => $this->json(),
			'gl_formatted_attr' => $this->json(),
			'gl_created_at' => $this->timestamp()
		]);

		$this->createIndex(
			'idx_gl_model_obj',
			'global_log',
			'gl_model, gl_obj_id'
		);

		$this->createIndex(
			'idx_gl_created_at',
			'global_log',
			'gl_created_at'
		);

		$this->createIndex(
			'idx_app_id_user_id',
			'global_log',
			'gl_app_id, gl_app_user_id'
		);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropIndex('idx_gl_model_obj', 'global_log');

    	$this->dropIndex('idx_gl_created_at', 'global_log');

    	$this->dropIndex('idx_app_id_user_id', 'global_log');

    	$this->dropTable('global_log');
    }
}
