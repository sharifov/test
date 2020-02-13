<?php

namespace modules\offer\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200129_100111_create_tbl_offer_send_log
 */
class m200129_100111_create_tbl_offer_send_log extends Migration
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

        $this->createTable('{{%offer_send_log}}', [
            'ofsndl_id' => $this->primaryKey(),
            'ofsndl_offer_id' => $this->integer()->notNull(),
            'ofsndl_type_id' => $this->tinyInteger()->notNull(),
            'ofsndl_send_to' => $this->string(160),
            'ofsndl_created_user_id' => $this->integer(),
            'ofsndl_created_dt' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-offer_send_log_ofsndl_offer_id',
            '{{%offer_send_log}}',
            'ofsndl_offer_id',
            '{{%offer}}',
            'of_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-offer_send_log_ofsndl_created_user_id',
            '{{%offer_send_log}}',
            'ofsndl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%offer_send_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-offer_send_log_ofsndl_created_user_id', '{{%offer_send_log}}');
        $this->dropForeignKey('FK-offer_send_log_ofsndl_offer_id', '{{%offer_send_log}}');
        $this->dropTable('{{%offer_send_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
