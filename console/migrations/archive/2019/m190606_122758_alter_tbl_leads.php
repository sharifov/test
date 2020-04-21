<?php

use yii\db\Migration;

/**
 * Class m190606_122758_alter_tbl_leads
 */
class m190606_122758_alter_tbl_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('{{%leads}}', 'cabin', $this->string(1)->null());
        $this->alterColumn('{{%leads}}', 'trip_type', $this->string(2)->null());

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->alterColumn('{{%leads}}', 'cabin', $this->string(1)->notNull());
        $this->alterColumn('{{%leads}}', 'trip_type', $this->string(2)->notNull());

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
