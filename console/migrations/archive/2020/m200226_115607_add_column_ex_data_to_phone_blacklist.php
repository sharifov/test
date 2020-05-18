<?php

use yii\db\Migration;

/**
 * Class m200226_115607_add_colimn_ex_data_to_phone_blacklist
 */
class m200226_115607_add_column_ex_data_to_phone_blacklist extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->addColumn('{{%phone_blacklist}}', 'pbl_expiration_date', $this->date());
        $this->afterRun();
    }

    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropColumn('{{%phone_blacklist}}', 'pbl_expiration_date');
        $this->afterRun();
    }

    private function afterRun(): void
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%phone_blacklist}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
