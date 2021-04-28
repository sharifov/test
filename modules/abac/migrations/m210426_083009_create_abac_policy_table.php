<?php

namespace modules\abac\migrations;

use Yii;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%abac_policy}}`.
 */
class m210426_083009_create_abac_policy_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%abac_policy}}', [
            'ap_id' => $this->primaryKey(),
            'ap_rule_type' => $this->string(2)->defaultValue('p')->notNull(),
            'ap_subject' => $this->string(10000),
            'ap_subject_json' => $this->json(),
            'ap_object' => $this->string(255)->notNull(),
            'ap_action' => $this->string(255),
            'ap_action_json' => $this->json(),
            'ap_effect' => $this->tinyInteger(1)->defaultValue(1)->notNull(),
            'ap_title' => $this->string(255),
            'ap_sort_order' => $this->smallInteger()->defaultValue(100),
            'ap_created_dt' => $this->dateTime(),
            'ap_updated_dt' => $this->dateTime(),
            'ap_created_user_id' => $this->integer(),
            'ap_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-abac_policy-ap_created_user_id',
            '{{%abac_policy}}',
            'ap_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-abac_policy-ap_updated_user_id',
            '{{%abac_policy}}',
            'ap_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-abac_policy-ap_sort_order', '{{%abac_policy}}', ['ap_sort_order']);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%abac_policy}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%abac_policy}}');
    }
}
