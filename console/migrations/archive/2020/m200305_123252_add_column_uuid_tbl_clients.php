<?php

use yii\db\Migration;

/**
 * Class m200305_123252_add_column_uuid_tbl_clients
 */
class m200305_123252_add_column_uuid_tbl_clients extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->addColumn('{{%clients}}', 'uuid', $this->string(36)->null());
        $this->createIndex('IDX-clients-uuid', '{{%clients}}', 'uuid', true);

        $this->afterRun();
    }

    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropIndex('IDX-clients-uuid', '{{%clients}}');
        $this->dropColumn('{{%clients}}', 'uuid');

        $this->afterRun();
    }

    private function afterRun(): void
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%clients}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
