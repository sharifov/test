<?php

use yii\db\Migration;

/**
 * Class m200227_150551_create_tbl_visitor_log
 */
class m200227_150551_create_tbl_visitor_log extends Migration
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

        $this->createTable('{{%visitor_log}}', [
            'vl_id' => $this->primaryKey(),
            'vl_project_id' => $this->integer()->null(),
            'vl_source_cid' => $this->string(10)->null(),
            'vl_ga_client_id' => $this->string(36)->null(),
            'vl_ga_user_id' => $this->string(36)->null(),
            'vl_user_id' => $this->integer()->null(),
            'vl_client_id' => $this->integer()->null(),
            'vl_lead_id' => $this->integer()->null(),
            'vl_gclid' => $this->string(100)->null(),
            'vl_dclid' => $this->string(255)->null(),
            'vl_utm_source' => $this->string(50)->null(),
            'vl_utm_medium' => $this->string(50)->null(),
            'vl_utm_campaign' => $this->string(50)->null(),
            'vl_utm_term' => $this->string(50)->null(),
            'vl_utm_content' => $this->string(50)->null(),
            'vl_referral_url' => $this->string(500)->null(),
            'vl_location_url' => $this->string(500)->null(),
            'vl_user_agent' => $this->string(500)->null(),
            'vl_ip_address' => $this->string(39)->null(),
            'vl_visit_dt' => $this->dateTime()->null(),
            'vl_created_dt' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-visitor_log-vl_lead_id',
            '{{%visitor_log}}',
            'vl_lead_id',
            '{{%leads}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-visitor_log-vl_client_id',
            '{{%visitor_log}}',
            'vl_client_id',
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-visitor_log-vl_project_id',
            '{{%visitor_log}}',
            'vl_project_id',
            '{{%projects}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-visitor_log-vl_lead_id', '{{%visitor_log}}', 'vl_lead_id');
        $this->createIndex('IND-visitor_log-vl_client_id', '{{%visitor_log}}', 'vl_client_id');
        $this->createIndex('IND-visitor_log-vl_visit_dt', '{{%visitor_log}}', 'vl_visit_dt');

        \Yii::$app->db->getSchema()->refreshTableSchema('{{%visitor_log}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-visitor_log-vl_lead_id', '{{%visitor_log}}');
        $this->dropForeignKey('FK-visitor_log-vl_client_id','{{%visitor_log}}');
        $this->dropForeignKey('FK-visitor_log-vl_project_id','{{%visitor_log}}');

        $this->dropIndex('IND-visitor_log-vl_visit_dt', '{{%visitor_log}}');
        $this->dropIndex('IND-visitor_log-vl_client_id', '{{%visitor_log}}');
        $this->dropIndex('IND-visitor_log-vl_lead_id', '{{%visitor_log}}');

        $this->dropTable('{{%visitor_log}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
