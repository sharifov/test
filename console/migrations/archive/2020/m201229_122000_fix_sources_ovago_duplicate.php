<?php

use common\models\Project;
use yii\db\Migration;

/**
 * Class m201229_122000_fix_sources_ovago_duplicate
 */
class m201229_122000_fix_sources_ovago_duplicate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->delete(
            'sources',
            'cid = :cid AND id != :id',
            [':cid' => 'OVA101', ':id' => 73]
        )->execute();

        try {
            Project::synchronizationProjects();
        } catch (\Throwable $throwable) {
            echo 'Error SynchronizationProjects: ' . $throwable->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201229_122000_fix_sources_ovago_duplicate cannot be reverted.\n";
    }
}
