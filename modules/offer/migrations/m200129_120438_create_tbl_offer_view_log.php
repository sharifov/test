<?php

namespace modules\offer\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m200129_120438_create_tbl_offer_view_log
 */
class m200129_120438_create_tbl_offer_view_log extends Migration
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

        $this->createTable('{{%offer_view_log}}', [
            'ofvwl_id' => $this->primaryKey(),
            'ofvwl_offer_id' => $this->integer()->notNull(),
            'ofvwl_visitor_id' => $this->string(32),
            'ofvwl_ip_address' => $this->string(40),
            'ofvwl_user_agent' => $this->string(255),
            'ofvwl_created_dt' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-offer_view_log_ofvwl_offer_id',
            '{{%offer_view_log}}',
            'ofvwl_offer_id',
            '{{%offer}}',
            'of_id',
            'CASCADE',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%offer_view_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-offer_view_log_ofvwl_offer_id', '{{%offer_view_log}}');
        $this->dropTable('{{%offer_view_log}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
