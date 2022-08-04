<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lead_rating_parameter}}`.
 */
class m220727_063403_create_lead_rating_parameter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lead_rating_parameter}}', [
            'lrp_id' => $this->primaryKey(),
            'lrp_object' => $this->string(),
            'lrp_attribute' => $this->string(),
            'lrp_point' => $this->integer()->defaultValue(0),
            'lrp_condition' => $this->string(),
            'lrp_condition_json' => $this->json(),
            'lrp_created_dt' => $this->dateTime(),
            'lrp_created_user_id' => $this->integer(),
            'lrp_updated_dt' => $this->dateTime(),
            'lrp_updated_user_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'FK-lead_rating_parameter-lrp_created_user_id',
            '{{%lead_rating_parameter}}',
            'lrp_created_user_id',
            '{{%employees}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-lead_rating_parameter-lrp_updated_user_id',
            '{{%lead_rating_parameter}}',
            'lrp_updated_user_id',
            '{{%employees}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lead_rating_parameter}}');
    }
}
