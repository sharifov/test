<?php

use common\models\Setting;
use common\models\Sources;
use yii\db\Migration;

/**
 * Class m191014_074108_add_column_rule_id_tbl_sources
 */
class m191014_074108_add_column_rule_id_tbl_sources extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Setting::deleteAll(['s_key' => 'jivo_chat_id']);

        $this->addColumn('{{%sources}}', 'rule', $this->integer()->defaultValue(Sources::RULE_PHONE_REQUIRED));

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'jivo_chat_id',
            's_name' => 'Jivo chat Id',
            's_type' => 'int',
            's_value' => 52,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->dropColumn('{{%sources}}', 'rule');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
