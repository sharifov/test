<?php

namespace modules\abac\migrations;

use Yii;
use yii\db\Migration;

/**
 * Class m220603_090433_add_column_hash_tbl_abac_polisy
 */
class m220603_090433_add_column_hash_tbl_abac_polisy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%abac_policy}}', 'ap_hash_code', $this->string(32));
        $this->createIndex('IND-abac_policy-ap_hash_code', '{{%abac_policy}}', ['ap_hash_code']);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%abac_policy}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-abac_policy-ap_hash_code', '{{%abac_policy}}');
        $this->dropColumn('{{%abac_policy}}', 'ap_hash_code');

        Yii::$app->db->getSchema()->refreshTableSchema('{{%abac_policy}}');
    }
}
