<?php

use yii\db\Migration;

/**
 * Class m200220_130816_add_column_sort_to_table_project
 */
class m200220_130816_add_column_sort_to_table_project extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeUp()
    {
        $this->addColumn('{{%projects}}', 'sort_order', $this->tinyInteger()->defaultValue(0));
        $this->createIndex('IDX-projects-sort_order', '{{%projects}}', ['sort_order']);

        $this->afterRun();
    }

    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     */
    public function safeDown()
    {
        $this->dropIndex('IDX-projects-sort_order', '{{%projects}}');
        $this->dropColumn('{{%projects}}', 'sort_order');

        $this->afterRun();
    }

    private function afterRun(): void
    {
        Yii::$app->db->getSchema()->refreshTableSchema('{{%projects}}');

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
