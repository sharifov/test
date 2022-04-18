<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lead_rating}}`.
 */
class m220203_153825_create_lead_user_rating_table extends Migration
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
        $this->createTable('{{%lead_user_rating}}', [
            'lur_lead_id'    => $this->integer()->notNull(),
            'lur_user_id'    => $this->integer()->notNull(),
            'lur_rating'     => $this->integer()->unsigned()->notNull(),
            'lur_created_dt' => $this->dateTime()->notNull(),
            'lur_updated_dt' => $this->dateTime(),

        ], $tableOptions);
        $this->addPrimaryKey('PK-lead_user_rating-lur_lead_id-lur_user_id', 'lead_user_rating', ['lur_lead_id', 'lur_user_id']);
        $this->addForeignKey('FK-lead_user_rating-lur_lead_id', '{{%lead_user_rating}}', 'lur_lead_id', '{{%leads}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-lead_user_rating-lur_user_id', '{{%lead_user_rating}}', 'lur_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_user_rating}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
