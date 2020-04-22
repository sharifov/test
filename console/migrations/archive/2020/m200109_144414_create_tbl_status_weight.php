<?php

use common\models\Lead;
use yii\db\Migration;

/**
 * Class m200109_144414_create_tbl_status_weight
 */
class m200109_144414_create_tbl_status_weight extends Migration
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

        $this->createTable('{{%status_weight}}', [
            'sw_status_id' => $this->integer()->notNull(),
            'sw_weight' => $this->integer()->defaultValue(0),
        ], $tableOptions);

        $this->addPrimaryKey('PK-status_weight_sw_status_id', '{{%status_weight}}', ['sw_status_id']);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%status_weight}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        $this->batchInsert('{{%status_weight}}', ['sw_status_id', 'sw_weight'], [
            [Lead::STATUS_PENDING, 0],
            [Lead::STATUS_PROCESSING, 0],
            [Lead::STATUS_REJECT, 0],
            [Lead::STATUS_FOLLOW_UP, 0],
            [Lead::STATUS_ON_HOLD, 0],
            [Lead::STATUS_SOLD, 0],
            [Lead::STATUS_TRASH, 0],
            [Lead::STATUS_BOOKED, 0],
            [Lead::STATUS_SNOOZE, 0],
            [Lead::STATUS_BOOK_FAILED, 0],
            [Lead::STATUS_ALTERNATIVE, 0],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%status_weight}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
