<?php

use yii\db\Migration;

/**
 * Class m191226_150959_create_tbl_api_user_allowance
 */
class m191226_150959_create_tbl_api_user_allowance extends Migration
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

        $this->createTable('{{%api_user_allowance}}',	[
            'aua_user_id' => $this->primaryKey(),
            'aua_allowed_number_requests' => $this->integer(10)->notNull(),
            'aua_last_check_time' => $this->integer(10)->notNull()
        ], $tableOptions);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%api_user_allowance}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%api_user_allowance}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
