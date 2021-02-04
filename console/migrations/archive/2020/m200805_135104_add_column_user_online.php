<?php

use yii\db\Migration;

/**
 * Class m200805_135104_add_column_user_online
 */
class m200805_135104_add_column_user_online extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_online}}', 'uo_idle_state', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%user_online}}', 'uo_idle_state_dt', $this->dateTime());

        $this->createIndex('IND-user_online-all', '{{%user_online}}', ['uo_user_id', 'uo_idle_state', 'uo_idle_state_dt']);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_online}}');
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('{{%user_online}}', 'uo_idle_state');
        $this->dropColumn('{{%user_online}}', 'uo_idle_state_dt');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%user_online}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
