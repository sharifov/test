<?php

use yii\db\Migration;

/**
 * Class m190321_165933_add_field_tbl_sources
 */
class m190321_165933_add_field_tbl_sources extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sources}}', 'phone_number', $this->string(20)->unique());
        $this->addColumn('{{%sources}}', 'default', $this->tinyInteger(1)->defaultValue(0));

        //$this->createIndex('IND-phone_number','{{%sources}}', 'phone_number', true);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%sources}}');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('{{%sources}}', 'phone_number');
        $this->dropColumn('{{%sources}}', 'default');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
