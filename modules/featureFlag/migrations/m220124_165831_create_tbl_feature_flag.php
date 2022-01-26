<?php

namespace modules\featureFlag\migrations;

use Yii;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%feature_flag}}`.
 */
class m220124_165831_create_tbl_feature_flag extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%feature_flag}}', [
            'ff_id' => $this->primaryKey(),
            'ff_key' => $this->string(255)->unique()->notNull(),
            'ff_name' => $this->string(255),
            'ff_type' => $this->string(10)->notNull(),
            'ff_value' => $this->string(255)->notNull(),
            'ff_category' => $this->string(255),
            'ff_description' => $this->string(1000),
            'ff_enable_type' => $this->tinyInteger(1)->defaultValue(0)->notNull(),
            'ff_attributes' => $this->json(),
            'ff_condition' => $this->json(),
           // 'ff_expression' => $this->boolean()->notNull(),
            'ff_updated_dt' => $this->dateTime(),
            'ff_updated_user_id' => $this->integer(),
        ], $tableOptions);


        $this->addForeignKey(
            'FK-feature_flag-ff_updated_user_id',
            '{{%feature_flag}}',
            ['ff_updated_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        Yii::$app->db->getSchema()->refreshTableSchema('{{%feature_flag}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%feature_flag}}');
    }
}
