<?php

namespace modules\abac\migrations;

use Yii;
use yii\db\Migration;

/**
 * Handles the creation of column `{{%abac_policy}}`.
 */
class m210525_143009_add_column_enabled extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%abac_policy}}', 'ap_enabled', $this->boolean()->defaultValue(true));
        $this->createIndex('IND-abac_policy-ap_enabled', '{{%abac_policy}}', ['ap_enabled']);

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
        $this->dropIndex('IND-abac_policy-ap_enabled', '{{%abac_policy}}');
        $this->dropColumn('{{%abac_policy}}', 'ap_enabled');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%abac_policy}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
