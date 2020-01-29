<?php

namespace modules\offer\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200127_083124_create_tbl_offer_status_log
 */
class m200127_083124_create_tbl_offer_status_log extends Migration
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

        $this->createTable('{{%offer_status_log}}', [
            'osl_id' => $this->primaryKey(),
            'osl_offer_id' => $this->integer()->notNull(),
            'osl_start_status_id' => $this->tinyInteger(),
            'osl_end_status_id' => $this->tinyInteger()->notNull(),
            'osl_start_dt' => $this->dateTime()->notNull(),
            'osl_end_dt' => $this->dateTime(),
            'osl_duration' => $this->integer(),
            'osl_description' => $this->string(255),
            'osl_owner_user_id' => $this->integer(),
            'osl_created_user_id' => $this->integer(),

        ], $tableOptions);

        $this->addForeignKey(
            'FK-offer_status_log_osl_offer_id',
            '{{%offer_status_log}}',
            'osl_offer_id',
            '{{%offer}}',
            'of_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-offer_status_log_osl_owner_user_id',
            '{{%offer_status_log}}',
            'osl_owner_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-offer_status_log_osl_created_user_id',
            '{{%offer_status_log}}',
            'osl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%offer_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-offer_status_log_osl_created_user_id', '{{%offer_status_log}}');
        $this->dropForeignKey('FK-offer_status_log_osl_owner_user_id', '{{%offer_status_log}}');
        $this->dropForeignKey('FK-offer_status_log_osl_offer_id', '{{%offer_status_log}}');
        $this->dropTable('{{%offer_status_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
