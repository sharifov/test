<?php

use yii\db\Migration;

/**
 * Class m190814_053952_add_column_gid_tbl_cases
 */
class m190814_053952_add_column_gid_tbl_cases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cases}}', 'cs_gid', $this->string(32)->unique()->after('cs_id'));
        $this->createIndex('IND-cases_cs_gid','{{%cases}}', 'cs_gid', true);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%cases}}');

        $this->execute('UPDATE cases SET cs_gid = MD5(cs_id) WHERE cs_gid IS NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-cases_cs_gid', '{{%cases}}');
        $this->dropColumn('{{%cases}}', 'cs_gid');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
