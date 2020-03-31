<?php

use yii\db\Migration;

/**
 * Class m200326_144805_add_column_fare_rules_to_case_sale_tbl
 */
class m200326_144805_add_column_fare_rules_to_case_sale_tbl extends Migration
{
     /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->addColumn('{{%case_sale}}', 'css_fare_rules', $this->string(400));
        $this->afterRun();
    }

    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropColumn('{{%case_sale}}', 'css_fare_rules');
        $this->afterRun();
    }

    private function afterRun(): void
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
