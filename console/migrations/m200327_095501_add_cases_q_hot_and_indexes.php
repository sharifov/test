<?php

use yii\db\Migration;

/**
 * Class m200327_095501_add_cases_q_hot_and_indexes
 */
class m200327_095501_add_cases_q_hot_and_indexes extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->createIndex('IND-case_sale-css_in_date', '{{%case_sale}}', ['css_in_date']);
        $this->createIndex('IND-case_sale-css_out_date', '{{%case_sale}}', ['css_out_date']);
        $this->createIndex('IND-cases-css_cs_deadline_dt', '{{%cases}}', ['cs_deadline_dt']);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-case_sale-css_in_date', '{{%case_sale}}');
        $this->dropIndex('IND-case_sale-css_out_date', '{{%case_sale}}');
        $this->dropIndex('IND-cases-css_cs_deadline_dt', '{{%cases}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        Yii::$app->db->getSchema()->refreshTableSchema('{{%case_sale}}');
    }
}
