<?php

use yii\db\Migration;

/**
 * Class m200331_124828_remove_old_rows_from_tbl_global_log
 */
class m200331_124828_remove_old_rows_from_tbl_global_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
		$db = Yii::$app->db;

		$maxGlId = $db->createCommand('Select max(gl_id) as gl_id from global_log where ABS(TIMESTAMPDIFF(DAY, curdate(), gl_created_at)) >= 90')->queryOne();
		$count = $db->createCommand('Select count(gl_id) as count_gl_id from global_log where ABS(TIMESTAMPDIFF(DAY, curdate(), gl_created_at)) >= 90')->queryOne();

		$iter = (int)($count['count_gl_id'] / 2000);

		for ($i = 0; $i <= $iter; $i++) {
			$db->createCommand('DELETE from global_log where gl_id <= :gl_id limit :limit')->bindValues([':gl_id' => $maxGlId['gl_id'], ':limit' => 2000])->execute();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
